<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\Article;

class NotificationController extends Controller
{
    /**
     * Récupérer les notifications de l'admin
     */
    public function index()
    {
        $notifications = [];

        // 1. Nouveaux vendeurs en attente
        $pendingVendors = Vendor::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($pendingVendors as $vendor) {
            $notifications[] = [
                'id' => 'vendor-' . $vendor->id,
                'type' => 'vendor_pending',
                'icon' => 'user',
                'color' => 'accent',
                'title' => 'Nouveau vendeur en attente',
                'message' => $vendor->business_name . ' a demandé à rejoindre la plateforme',
                'time' => $vendor->created_at->diffForHumans(),
                'url' => route('admin.vendors.show', $vendor->id),
                'read' => false
            ];
        }

        // 2. Nouvelles commandes
        $recentOrders = Order::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentOrders as $order) {
            $notifications[] = [
                'id' => 'order-' . $order->id,
                'type' => 'order_created',
                'icon' => 'shopping',
                'color' => 'success',
                'title' => 'Nouvelle commande',
                'message' => 'Commande #' . $order->id . ' confirmée (' . $order->total . '€)',
                'time' => $order->created_at->diffForHumans(),
                'url' => route('admin.orders.show', $order->id),
                'read' => false
            ];
        }

        // 3. Nouveaux articles publiés (si table articles existe)
        if (DB::getSchemaBuilder()->hasTable('articles')) {
            $recentArticles = Article::where('status', 'published')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentArticles as $article) {
                $notifications[] = [
                    'id' => 'article-' . $article->id,
                    'type' => 'article_published',
                    'icon' => 'document',
                    'color' => 'blue',
                    'title' => 'Nouvel article publié',
                    'message' => '"' . $article->title . '" par ' . $article->writer->name,
                    'time' => $article->created_at->diffForHumans(),
                    'url' => route('blog.show', $article->slug),
                    'read' => false
                ];
            }
        }

        // Trier par date (plus récent en premier)
        usort($notifications, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Limiter à 10 notifications
        $notifications = array_slice($notifications, 0, 10);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => count($notifications)
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        // Pour l'instant, juste retourner success
        // Plus tard, on pourra stocker l'état "lu" en base
        return response()->json(['success' => true]);
    }
}
