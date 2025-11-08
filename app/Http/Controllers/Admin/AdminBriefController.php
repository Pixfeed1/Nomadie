<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBrief;
use App\Models\BriefTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBriefController extends Controller
{
    /**
     * Dashboard principal des briefs
     */
    public function index(Request $request)
    {
        $query = ContentBrief::with(['assignedTo', 'createdBy', 'article'])
            ->latest();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $briefs = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => ContentBrief::count(),
            'active' => ContentBrief::active()->count(),
            'overdue' => ContentBrief::overdue()->count(),
            'completed_this_month' => ContentBrief::where('status', ContentBrief::STATUS_COMPLETED)
                ->whereMonth('completed_at', now()->month)
                ->count(),
        ];

        // Rédacteurs team disponibles
        $teamWriters = User::where('writer_type', User::WRITER_TYPE_TEAM)
            ->where('writer_status', User::WRITER_STATUS_VALIDATED)
            ->get();

        return view('admin.briefs.index', compact('briefs', 'stats', 'teamWriters'));
    }

    /**
     * Formulaire création brief
     */
    public function create(Request $request)
    {
        $templates = BriefTemplate::active()->get();
        $teamWriters = User::where('writer_type', User::WRITER_TYPE_TEAM)
            ->where('writer_status', User::WRITER_STATUS_VALIDATED)
            ->get();

        // Si un template est sélectionné, le pré-charger
        $selectedTemplate = null;
        if ($request->filled('template_id')) {
            $selectedTemplate = BriefTemplate::find($request->template_id);
        }

        return view('admin.briefs.create', compact('templates', 'teamWriters', 'selectedTemplate'));
    }

    /**
     * Enregistrer nouveau brief
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:destination,guide_pratique,culture,gastronomie,hebergement,transport,budget,custom',
            'category' => 'nullable|string|max:255',
            'content_requirements' => 'nullable|array',
            'keywords' => 'nullable|array',
            'references' => 'nullable|array',
            'min_words' => 'nullable|integer|min:500',
            'target_score' => 'nullable|integer|min:60|max:100',
            'seo_requirements' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'admin_notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        // Si assigné dès la création
        if (!empty($validated['assigned_to'])) {
            $validated['assigned_at'] = now();
            $validated['status'] = ContentBrief::STATUS_ASSIGNED;
        } else {
            $validated['status'] = ContentBrief::STATUS_DRAFT;
        }

        $brief = ContentBrief::create($validated);

        return redirect()->route('admin.briefs.show', $brief)
            ->with('success', 'Brief créé avec succès !');
    }

    /**
     * Afficher détails d'un brief
     */
    public function show(ContentBrief $brief)
    {
        $brief->load(['assignedTo', 'createdBy', 'article.latestSeoAnalysis']);

        $teamWriters = User::where('writer_type', User::WRITER_TYPE_TEAM)
            ->where('writer_status', User::WRITER_STATUS_VALIDATED)
            ->get();

        return view('admin.briefs.show', compact('brief', 'teamWriters'));
    }

    /**
     * Formulaire édition brief
     */
    public function edit(ContentBrief $brief)
    {
        $teamWriters = User::where('writer_type', User::WRITER_TYPE_TEAM)
            ->where('writer_status', User::WRITER_STATUS_VALIDATED)
            ->get();

        return view('admin.briefs.edit', compact('brief', 'teamWriters'));
    }

    /**
     * Mettre à jour brief
     */
    public function update(Request $request, ContentBrief $brief)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:destination,guide_pratique,culture,gastronomie,hebergement,transport,budget,custom',
            'category' => 'nullable|string|max:255',
            'content_requirements' => 'nullable|array',
            'keywords' => 'nullable|array',
            'references' => 'nullable|array',
            'min_words' => 'nullable|integer|min:500',
            'target_score' => 'nullable|integer|min:60|max:100',
            'seo_requirements' => 'nullable|array',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'admin_notes' => 'nullable|string',
            'status' => 'nullable|in:draft,assigned,in_progress,pending_review,revision_requested,completed,cancelled',
        ]);

        $brief->update($validated);

        return redirect()->route('admin.briefs.show', $brief)
            ->with('success', 'Brief mis à jour !');
    }

    /**
     * Assigner brief à un rédacteur
     */
    public function assign(Request $request, ContentBrief $brief)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $writer = User::findOrFail($request->assigned_to);

        // Vérifier que c'est un rédacteur team
        if (!$writer->isTeamMember()) {
            return back()->with('error', 'Seuls les membres de la team peuvent recevoir des briefs.');
        }

        $brief->assignTo($writer);

        // Notification au rédacteur
        $writer->notify(new \App\Notifications\BriefAssigned($brief));

        return back()->with('success', "Brief assigné à {$writer->name} !");
    }

    /**
     * Approuver l'article et marquer le brief comme complété
     */
    public function approve(ContentBrief $brief)
    {
        if (!$brief->article) {
            return back()->with('error', 'Aucun article associé à ce brief.');
        }

        $brief->markAsCompleted();

        // Publier l'article si pas encore publié
        if ($brief->article->status === 'draft') {
            $brief->article->update(['status' => 'published']);
        }

        // Notification au rédacteur
        if ($brief->assignedTo) {
            $brief->assignedTo->notify(new \App\Notifications\BriefApproved($brief));
        }

        return redirect()->route('admin.briefs.index')
            ->with('success', 'Brief approuvé et complété !');
    }

    /**
     * Demander des révisions
     */
    public function requestRevision(Request $request, ContentBrief $brief)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $brief->requestRevision($request->notes);

        // Notification au rédacteur
        if ($brief->assignedTo) {
            $brief->assignedTo->notify(new \App\Notifications\RevisionRequested($brief, $request->notes));
        }

        return back()->with('success', 'Révision demandée au rédacteur.');
    }

    /**
     * Annuler un brief
     */
    public function cancel(ContentBrief $brief)
    {
        $brief->update(['status' => ContentBrief::STATUS_CANCELLED]);

        return redirect()->route('admin.briefs.index')
            ->with('success', 'Brief annulé.');
    }

    /**
     * Supprimer un brief
     */
    public function destroy(ContentBrief $brief)
    {
        $brief->delete();

        return redirect()->route('admin.briefs.index')
            ->with('success', 'Brief supprimé.');
    }

    /**
     * Gérer les templates
     */
    public function templates()
    {
        $templates = BriefTemplate::with('createdBy')->latest()->paginate(15);

        return view('admin.briefs.templates', compact('templates'));
    }

    /**
     * Créer un brief depuis un template
     */
    public function createFromTemplate(BriefTemplate $template)
    {
        return redirect()->route('admin.briefs.create', ['template_id' => $template->id]);
    }
}
