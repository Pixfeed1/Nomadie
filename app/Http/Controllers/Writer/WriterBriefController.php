<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\ContentBrief;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WriterBriefController extends Controller
{
    /**
     * Display writer's assigned briefs
     */
    public function index(Request $request)
    {
        $writer = Auth::user();

        // Only show briefs assigned to this writer
        $query = ContentBrief::where('assigned_to', $writer->id)
            ->with(['createdBy', 'article'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $briefs = $query->paginate(15);

        // Stats for this writer
        $stats = [
            'total' => ContentBrief::where('assigned_to', $writer->id)->count(),
            'in_progress' => ContentBrief::where('assigned_to', $writer->id)
                ->where('status', ContentBrief::STATUS_IN_PROGRESS)
                ->count(),
            'pending_review' => ContentBrief::where('assigned_to', $writer->id)
                ->where('status', ContentBrief::STATUS_PENDING_REVIEW)
                ->count(),
            'revision_requested' => ContentBrief::where('assigned_to', $writer->id)
                ->where('status', ContentBrief::STATUS_REVISION_REQUESTED)
                ->count(),
            'completed' => ContentBrief::where('assigned_to', $writer->id)
                ->where('status', ContentBrief::STATUS_COMPLETED)
                ->count(),
            'overdue' => ContentBrief::where('assigned_to', $writer->id)
                ->overdue()
                ->count(),
        ];

        return view('writer.briefs.index', compact('briefs', 'stats'));
    }

    /**
     * Show brief details
     */
    public function show(ContentBrief $brief)
    {
        $writer = Auth::user();

        // Ensure this brief is assigned to the current writer
        if ($brief->assigned_to !== $writer->id) {
            abort(403, 'Ce brief ne vous est pas assigné.');
        }

        $brief->load(['createdBy', 'article']);

        return view('writer.briefs.show', compact('brief'));
    }

    /**
     * Mark brief as started
     */
    public function start(ContentBrief $brief)
    {
        $writer = Auth::user();

        // Ensure this brief is assigned to the current writer
        if ($brief->assigned_to !== $writer->id) {
            abort(403, 'Ce brief ne vous est pas assigné.');
        }

        // Check if brief can be started
        if ($brief->status !== ContentBrief::STATUS_ASSIGNED &&
            $brief->status !== ContentBrief::STATUS_REVISION_REQUESTED) {
            return back()->with('error', 'Ce brief ne peut pas être démarré dans son état actuel.');
        }

        $brief->markAsStarted();

        return back()->with('success', 'Brief marqué comme en cours ! Bon courage pour la rédaction 💪');
    }

    /**
     * Submit article for review
     */
    public function submit(Request $request, ContentBrief $brief)
    {
        $writer = Auth::user();

        // Ensure this brief is assigned to the current writer
        if ($brief->assigned_to !== $writer->id) {
            abort(403, 'Ce brief ne vous est pas assigné.');
        }

        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'writer_notes' => 'nullable|string|max:2000',
        ]);

        $article = Article::findOrFail($request->article_id);

        // Ensure the article belongs to this writer
        if ($article->author_id !== $writer->id) {
            return back()->with('error', 'Cet article ne vous appartient pas.');
        }

        // Submit for review
        $brief->submitForReview($article);

        // Add writer notes if provided
        if ($request->filled('writer_notes')) {
            $brief->update(['writer_notes' => $request->writer_notes]);
        }

        // Notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\BriefSubmittedForReview($brief));
        }

        return redirect()->route('writer.briefs.index')
            ->with('success', 'Article soumis pour review ! L\'équipe admin va examiner votre travail. ✅');
    }

    /**
     * Add or update writer notes
     */
    public function updateNotes(Request $request, ContentBrief $brief)
    {
        $writer = Auth::user();

        // Ensure this brief is assigned to the current writer
        if ($brief->assigned_to !== $writer->id) {
            abort(403, 'Ce brief ne vous est pas assigné.');
        }

        $request->validate([
            'writer_notes' => 'required|string|max:2000',
        ]);

        $brief->update(['writer_notes' => $request->writer_notes]);

        return back()->with('success', 'Notes enregistrées !');
    }

    /**
     * Get writer's articles for dropdown
     */
    public function getMyArticles()
    {
        $writer = Auth::user();

        $articles = Article::where('author_id', $writer->id)
            ->where('status', 'draft') // Only draft articles
            ->select('id', 'title', 'created_at')
            ->latest()
            ->get();

        return response()->json($articles);
    }
}
