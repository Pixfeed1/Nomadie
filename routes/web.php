<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Admin\AdminDestinationController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Public\VendorRegistrationController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ClientRegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Api\DestinationApiController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Vendor\TripTypeSelectionController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\Vendor\VendorMessagesController;
use App\Models\Vendor;
use App\Mail\VendorConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ici sont enregistrées toutes les routes web de l'application.
| Ces routes sont chargées par RouteServiceProvider dans un groupe
| qui contient le middleware "web".
|
*/

// ==========================================
// ROUTES PUBLIQUES
// ==========================================

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', function () {
    return redirect()->route('home');
});

// Routes d'authentification de base
Auth::routes(['register' => false]); // Désactive la route register par défaut de Laravel

// OAuth Google
Route::get('login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

// ==========================================
// ROUTES D'INSCRIPTION
// ==========================================
Route::middleware(['guest'])->group(function () {
    // Page de choix du type de compte
    Route::get('/creer-compte', [RegisterController::class, 'showChoicePage'])->name('register.choose');
    
    // Inscription client
    Route::get('/inscription', [ClientRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/inscription', [ClientRegisterController::class, 'register']);
    
    // Page de succès après inscription
    Route::get('/inscription/success', [ClientRegisterController::class, 'showSuccessPage'])->name('register.success');
    
    // Vérification de l'email
    Route::get('/verify-email/{token}', [ClientRegisterController::class, 'verifyEmail'])->name('client.verify.email');
    
    // Renvoi de l'email de vérification
    Route::post('/resend-verification', [ClientRegisterController::class, 'resendVerification'])->name('resend.verification');
    
    // Route de debug (développement uniquement)
    if (app()->environment('local', 'development')) {
        Route::get('/debug/user-status', [ClientRegisterController::class, 'debugUserStatus'])->name('debug.user.status');
    }
    
    // Routes pour l'inscription vendor
    Route::prefix('devenir-organisateur')->name('vendor.')->group(function () {
        Route::get('/', [VendorRegistrationController::class, 'index'])->name('register');
        Route::post('/etape', [VendorRegistrationController::class, 'nextStep'])->name('register.step');
        Route::post('/finaliser', [VendorRegistrationController::class, 'finalSubmit'])->name('register.final');
        Route::get('/confirmation/{vendor}', [VendorRegistrationController::class, 'showConfirmation'])->name('register.confirmation');
        Route::get('/create-password/{token}', [VendorRegistrationController::class, 'showCreatePassword'])->name('create-password');
        Route::post('/create-password/{token}', [VendorRegistrationController::class, 'storePassword'])->name('store-password');
        Route::get('/complete', [VendorRegistrationController::class, 'completeRegistration'])->name('register.complete');
        Route::get('/confirm/{token}', [VendorRegistrationController::class, 'confirmEmail'])->name('confirm');
        Route::get('/cleanup', [VendorRegistrationController::class, 'cleanupExpiredData'])->name('cleanup');
        Route::post('/', [VendorRegistrationController::class, 'store'])->name('register.submit');
    });
});

// ==========================================
// ROUTES POUR LES CLIENTS AUTHENTIFIÉS
// ==========================================
Route::middleware(['auth'])->prefix('mon-compte')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Réservations
    Route::get('/reservations', [CustomerDashboardController::class, 'bookings'])->name('bookings');
    Route::get('/reservations/{id}', [CustomerDashboardController::class, 'bookingDetail'])->name('bookings.show');
    Route::post('/reservations/{id}/cancel', [CustomerDashboardController::class, 'cancelBooking'])->name('bookings.cancel');
    
    // Favoris
    Route::get('/favoris', [CustomerDashboardController::class, 'favorites'])->name('favorites');
    Route::post('/favoris/{trip}/toggle', [CustomerDashboardController::class, 'toggleFavorite'])->name('favorites.toggle');
    
    // Messages
    Route::get('/messages', [CustomerDashboardController::class, 'messages'])->name('messages');
    Route::get('/messages/new', [CustomerDashboardController::class, 'newMessage'])->name('messages.new');
    Route::post('/messages/send', [CustomerDashboardController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/{tripSlug}', [CustomerDashboardController::class, 'showConversation'])->name('messages.show');
    Route::post('/messages/{tripSlug}/reply', [CustomerDashboardController::class, 'replyMessage'])->name('messages.reply');
    
    // Avis
    Route::get('/avis', [CustomerDashboardController::class, 'reviews'])->name('reviews');
    Route::get('/avis/nouveau/{booking}', [CustomerDashboardController::class, 'createReview'])->name('reviews.create');
    Route::post('/avis/nouveau/{booking}', [CustomerDashboardController::class, 'storeReview'])->name('reviews.store');
    
    // Profil
    Route::get('/profil', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profil', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profil/avatar', [CustomerDashboardController::class, 'updateAvatar'])->name('profile.avatar');
    
    // Paramètres
    Route::get('/parametres', [CustomerDashboardController::class, 'settings'])->name('settings');
    Route::put('/parametres', [CustomerDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::put('/parametres/password', [CustomerDashboardController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/compte', [CustomerDashboardController::class, 'deleteAccount'])->name('account.delete');
});

// ==========================================
// PAGES STATIQUES
// ==========================================
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Routes nécessaires pour les emails
Route::get('/help', function() {
    return view('help.index');
})->name('help');

Route::get('/profile', function() {
    return redirect()->route('customer.profile');
})->name('profile')->middleware('auth');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Commentaires blog
Route::post('/blog/{article:slug}/comments', [CommentController::class, 'store'])
    ->name('comments.store')
    ->middleware(['throttle:5,1']);

// ==========================================
// RECHERCHE
// ==========================================
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/advanced', [SearchController::class, 'advancedSearch'])->name('search.advanced');
Route::post('/search/ajax', [SearchController::class, 'ajaxSearch'])->name('search.ajax');

// ==========================================
// DESTINATIONS
// ==========================================
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{slug}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/destinations/{slug}/offres', [DestinationController::class, 'trips'])->name('destinations.trips');

// ==========================================
// OFFRES/SÉJOURS
// ==========================================
Route::get('/offres', [TripController::class, 'index'])->name('trips.index');
Route::get('/offres/{slug}', [TripController::class, 'show'])->name('trips.show');
Route::get('/offres/{slug}/galerie', [TripController::class, 'gallery'])->name('trips.gallery');
Route::get('/offres/{slug}/itineraire', [TripController::class, 'itinerary'])->name('trips.itinerary');
Route::get('/offres/{slug}/reserver', [TripController::class, 'showBookingForm'])->name('trips.booking.form');
Route::post('/offres/{slug}/reserver', [TripController::class, 'processBooking'])->name('trips.booking.process');
Route::post('/offres/{slug}/book', [TripController::class, 'book'])->name('trips.book');
Route::get('/offres/{slug}/confirmation', [TripController::class, 'confirmation'])->name('trips.confirmation');

// Paiements offres
Route::prefix('offres/paiement')->name('trips.payment.')->group(function () {
    Route::get('/{slug}', [PaymentController::class, 'showTripPaymentPage'])->name('show');
    Route::post('/{slug}/initiate', [PaymentController::class, 'initiateTripPayment'])->name('initiate');
    Route::get('/{slug}/success', [PaymentController::class, 'tripPaymentSuccess'])->name('success');
    Route::get('/{slug}/cancel', [PaymentController::class, 'tripPaymentCancel'])->name('cancel');
});

// Avis
Route::middleware(['auth'])->group(function () {
    Route::get('/offres/{slug}/avis/creer', [ReviewController::class, 'create'])->name('trips.review.create');
    Route::post('/offres/{slug}/avis', [ReviewController::class, 'store'])->name('trips.review.store');
});
Route::get('/offres/{slug}/avis', [ReviewController::class, 'index'])->name('trips.reviews.index');

// ==========================================
// ORGANISATEURS (VENDORS PUBLICS)
// ==========================================
Route::get('/organisateur/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
Route::get('/organisateurs', [VendorController::class, 'index'])->name('vendors.index');

// ==========================================
// PAIEMENT VENDOR (SANS AUTH)
// ==========================================
Route::middleware(['vendor_registration'])->group(function () {
    Route::prefix('vendor/payment')->name('vendor.payment.')->group(function () {
        Route::get('/', [PaymentController::class, 'showVendorPaymentPage'])->name('show');
        Route::post('/initiate', [PaymentController::class, 'initiateVendorPayment'])->name('initiate');
        Route::get('/success', [PaymentController::class, 'vendorPaymentSuccess'])->name('success');
        Route::get('/cancel', [PaymentController::class, 'vendorPaymentCancel'])->name('cancel');
    });
});

// ==========================================
// ESPACE ADMIN
// ==========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () { return redirect()->route('admin.dashboard'); });
    
    // Vendors
    Route::get('/vendors', [AdminVendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/pending', [AdminVendorController::class, 'pending'])->name('vendors.pending');
    Route::get('/vendors/approved', [AdminVendorController::class, 'approved'])->name('vendors.approved');
    Route::get('/vendors/rejected', [AdminVendorController::class, 'rejected'])->name('vendors.rejected');
    Route::get('/vendors/suspended', [AdminVendorController::class, 'suspended'])->name('vendors.suspended');
    Route::get('/vendors/{id}', [AdminVendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/approve', [AdminVendorController::class, 'approve'])->name('vendors.approve');
    Route::post('/vendors/{id}/reject', [AdminVendorController::class, 'reject'])->name('vendors.reject');
    Route::post('/vendors/{id}/suspend', [AdminVendorController::class, 'suspend'])->name('vendors.suspend');
    Route::post('/vendors/{id}/activate', [AdminVendorController::class, 'activate'])->name('vendors.activate');
    Route::delete('/vendors/{id}', [AdminVendorController::class, 'destroy'])->name('vendors.destroy');

    // Writers (Rédacteurs) - Validation des candidatures
    Route::prefix('writers')->name('writers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminWriterController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AdminWriterController::class, 'show'])->name('show');
        Route::post('/{id}/validate', [\App\Http\Controllers\Admin\AdminWriterController::class, 'validate'])->name('validate');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\AdminWriterController::class, 'reject'])->name('reject');
        Route::post('/{id}/suspend', [\App\Http\Controllers\Admin\AdminWriterController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/restore', [\App\Http\Controllers\Admin\AdminWriterController::class, 'restore'])->name('restore');
        Route::post('/{id}/update-notes', [\App\Http\Controllers\Admin\AdminWriterController::class, 'updateNotes'])->name('update-notes');
    });

    // Destinations
    Route::resource('destinations', AdminDestinationController::class);
    
    // Commentaires (modération)
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('index');
        Route::get('/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'show'])->name('show');
        Route::patch('/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('approve');
        Route::patch('/{comment}/reject', [\App\Http\Controllers\Admin\CommentController::class, 'reject'])->name('reject');
        Route::patch('/{comment}/spam', [\App\Http\Controllers\Admin\CommentController::class, 'markAsSpam'])->name('spam');
        Route::delete('/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\CommentController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Abonnements
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/analytics', [SubscriptionController::class, 'analytics'])->name('subscriptions.analytics');
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{id}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    
    // Commandes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/cancelled', [OrderController::class, 'cancelled'])->name('orders.cancelled');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/refund', [OrderController::class, 'refund'])->name('orders.refund');
    
    // Rapports
    Route::get('/reports/sales', [DashboardController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/vendors', [DashboardController::class, 'vendorsReport'])->name('reports.vendors');
    Route::get('/reports/destinations', [DashboardController::class, 'destinationsReport'])->name('reports.destinations');
    Route::get('/reports/export/sales', [DashboardController::class, 'exportSales'])->name('reports.export.sales');
});

// ==========================================
// ESPACE VENDOR
// ==========================================
Route::middleware(['auth', 'vendor_dashboard'])->prefix('vendor')->name('vendor.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/', function () { return redirect()->route('vendor.dashboard.index'); });
    Route::get('/welcome', [\App\Http\Controllers\Vendor\DashboardController::class, 'welcome'])->name('dashboard.welcome');
    Route::get('/analytics', [\App\Http\Controllers\Vendor\DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Messages - Routes complètes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [VendorMessagesController::class, 'index'])->name('index');
        Route::get('/unread', [VendorMessagesController::class, 'unread'])->name('unread');
        Route::get('/archived', [VendorMessagesController::class, 'archived'])->name('archived');
        Route::get('/search', [VendorMessagesController::class, 'search'])->name('search');
        Route::post('/mark-all-read', [VendorMessagesController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::get('/download/{message}', [VendorMessagesController::class, 'download'])->name('download');
        Route::get('/{tripSlug}', [VendorMessagesController::class, 'show'])->name('show');
        Route::post('/{tripSlug}/reply', [VendorMessagesController::class, 'reply'])->name('reply');
        Route::post('/{tripSlug}/archive', [VendorMessagesController::class, 'archive'])->name('archive');
        Route::post('/{tripSlug}/unarchive', [VendorMessagesController::class, 'unarchive'])->name('unarchive');
        Route::post('/{tripSlug}/mark-read', [VendorMessagesController::class, 'markAsRead'])->name('markAsRead');
        Route::delete('/{tripSlug}', [VendorMessagesController::class, 'delete'])->name('delete');
    });

    // Gestion des offres (trips)
    Route::prefix('trips')->name('trips.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\TripController::class, 'index'])->name('index');
        Route::get('/select-type', [TripTypeSelectionController::class, 'index'])->name('select-type');
        Route::post('/select-type', [TripTypeSelectionController::class, 'redirect'])->name('select-type.redirect');
        Route::get('/choose-type', [\App\Http\Controllers\Vendor\TripController::class, 'chooseType'])->name('choose-type');
        Route::get('/create/{type?}', [\App\Http\Controllers\Vendor\TripController::class, 'create'])
            ->name('create')
            ->where('type', 'accommodation|organized_trip|activity|custom');
        Route::post('/', [\App\Http\Controllers\Vendor\TripController::class, 'store'])->name('store');
        Route::get('/{trip}', [\App\Http\Controllers\Vendor\TripController::class, 'show'])->name('show');
        Route::get('/{trip}/preview', [\App\Http\Controllers\Vendor\TripController::class, 'preview'])->name('preview');
        Route::get('/{trip}/promote', [\App\Http\Controllers\Vendor\TripController::class, 'promote'])->name('promote');
        Route::get('/{trip}/edit', [\App\Http\Controllers\Vendor\TripController::class, 'edit'])->name('edit');
        Route::put('/{trip}', [\App\Http\Controllers\Vendor\TripController::class, 'update'])->name('update');
        Route::delete('/{trip}', [\App\Http\Controllers\Vendor\TripController::class, 'destroy'])->name('destroy');
        Route::patch('/{trip}/toggle-status', [\App\Http\Controllers\Vendor\TripController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{trip}/duplicate', [\App\Http\Controllers\Vendor\TripController::class, 'duplicate'])->name('duplicate');
        
        // Gestion des disponibilités
        Route::prefix('{trip}/availabilities')->name('availabilities.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'store'])->name('store');
            Route::get('/{availability}/edit', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'edit'])->name('edit');
            Route::put('/{availability}', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'update'])->name('update');
            Route::delete('/{availability}', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'destroy'])->name('destroy');
            Route::post('/{availability}/duplicate', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'duplicate'])->name('duplicate');
            Route::get('/export', [\App\Http\Controllers\Vendor\TripAvailabilityController::class, 'export'])->name('export');
        });
    });

    // Réservations
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\BookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [\App\Http\Controllers\Vendor\BookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/status', [\App\Http\Controllers\Vendor\BookingController::class, 'updateStatus'])->name('update-status');
        Route::get('/export/csv', [\App\Http\Controllers\Vendor\BookingController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [\App\Http\Controllers\Vendor\BookingController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/filter', [\App\Http\Controllers\Vendor\BookingController::class, 'filter'])->name('filter');
    });

    // Paiements
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [\App\Http\Controllers\Vendor\PaymentController::class, 'show'])->name('show');
        Route::get('/export/csv', [\App\Http\Controllers\Vendor\PaymentController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [\App\Http\Controllers\Vendor\PaymentController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/filter', [\App\Http\Controllers\Vendor\PaymentController::class, 'filter'])->name('filter');
        Route::get('/invoices', [\App\Http\Controllers\Vendor\PaymentController::class, 'invoices'])->name('invoices');
        Route::get('/invoice/{invoice}/download', [\App\Http\Controllers\Vendor\PaymentController::class, 'downloadInvoice'])->name('invoice.download');
    });

    // Avis
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\ReviewController::class, 'index'])->name('index');
        Route::get('/{review}', [\App\Http\Controllers\Vendor\ReviewController::class, 'show'])->name('show');
        Route::post('/{review}/reply', [\App\Http\Controllers\Vendor\ReviewController::class, 'reply'])->name('reply');
        Route::post('/{review}/report', [\App\Http\Controllers\Vendor\ReviewController::class, 'report'])->name('report');
        Route::get('/export/csv', [\App\Http\Controllers\Vendor\ReviewController::class, 'exportCsv'])->name('export.csv');
    });

    // Activité
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\ActivityController::class, 'index'])->name('index');
        Route::get('/filter', [\App\Http\Controllers\Vendor\ActivityController::class, 'filter'])->name('filter');
        Route::get('/export', [\App\Http\Controllers\Vendor\ActivityController::class, 'export'])->name('export');
    });

    // Abonnements
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'index'])->name('index');
        Route::get('/plans', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'plans'])->name('plans');
        Route::get('/upgrade', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'upgrade'])->name('upgrade');
        Route::post('/upgrade', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'processUpgrade'])->name('process-upgrade');
        Route::post('/cancel', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'resume'])->name('resume');
        Route::get('/invoices', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'invoices'])->name('invoices');
        Route::get('/invoice/{id}/download', [\App\Http\Controllers\Vendor\SubscriptionController::class, 'downloadInvoice'])->name('invoice.download');
    });

    // Paramètres
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\SettingsController::class, 'index'])->name('index');
        Route::get('/profile', [\App\Http\Controllers\Vendor\SettingsController::class, 'profile'])->name('profile');
        Route::get('/destinations', [\App\Http\Controllers\Vendor\SettingsController::class, 'destinations'])->name('destinations');
        Route::get('/security', [\App\Http\Controllers\Vendor\SettingsController::class, 'security'])->name('security');
        Route::get('/subscription', [\App\Http\Controllers\Vendor\SettingsController::class, 'subscription'])->name('subscription');
        Route::put('/profile', [\App\Http\Controllers\Vendor\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/representative', [\App\Http\Controllers\Vendor\SettingsController::class, 'updateRepresentative'])->name('representative.update');
        Route::put('/destinations', [\App\Http\Controllers\Vendor\SettingsController::class, 'updateDestinations'])->name('destinations.update');
        Route::put('/services', [\App\Http\Controllers\Vendor\SettingsController::class, 'updateServices'])->name('services.update');
        Route::put('/password', [\App\Http\Controllers\Vendor\SettingsController::class, 'updatePassword'])->name('password.update');
        Route::put('/notifications', [\App\Http\Controllers\Vendor\SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::post('/subscription', [\App\Http\Controllers\Vendor\SettingsController::class, 'manageSubscription'])->name('subscription.manage');
        Route::delete('/account', [\App\Http\Controllers\Vendor\SettingsController::class, 'deleteAccount'])->name('account.delete');
        Route::get('/export', [\App\Http\Controllers\Vendor\SettingsController::class, 'exportData'])->name('export');
    });

    // Rapports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [\App\Http\Controllers\Vendor\DashboardController::class, 'salesReport'])->name('sales');
        Route::get('/trips', [\App\Http\Controllers\Vendor\DashboardController::class, 'tripsReport'])->name('trips');
        Route::get('/export/sales', [\App\Http\Controllers\Vendor\DashboardController::class, 'exportSalesReport'])->name('export.sales');
        Route::get('/export/trips', [\App\Http\Controllers\Vendor\DashboardController::class, 'exportTripsReport'])->name('export.trips');
    });
    
    // Centre d'aide
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Vendor\HelpController::class, 'index'])->name('index');
        Route::get('/trip-types', [\App\Http\Controllers\Vendor\HelpController::class, 'tripTypes'])->name('trip-types');
        Route::get('/pricing', [\App\Http\Controllers\Vendor\HelpController::class, 'pricing'])->name('pricing');
        Route::get('/faq', [\App\Http\Controllers\Vendor\HelpController::class, 'faq'])->name('faq');
    });
});

// ==========================================
// ESPACE RÉDACTEURS
// ==========================================
// ==========================================
// ROUTES INSCRIPTION RÉDACTEUR (auth seulement)
// ==========================================
Route::middleware(['auth'])->prefix('writer')->name('writer.')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Writer\RegistrationController::class, 'showForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Writer\RegistrationController::class, 'register'])->name('register.submit');
    Route::get('/pending', [\App\Http\Controllers\Writer\RegistrationController::class, 'pending'])->name('pending');
});

// ==========================================
// ROUTES ESPACE RÉDACTEUR (writer middleware)
// ==========================================
Route::middleware(['auth', 'writer'])->prefix('writer')->name('writer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Writer\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Writer\ArticleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Writer\ArticleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Writer\ArticleController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [\App\Http\Controllers\Writer\ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [\App\Http\Controllers\Writer\ArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [\App\Http\Controllers\Writer\ArticleController::class, 'destroy'])->name('destroy');
        Route::post('/analyze', [\App\Http\Controllers\Writer\ArticleController::class, 'analyze'])->name('analyze');
        Route::post('/upload-image', [\App\Http\Controllers\Writer\ArticleController::class, 'uploadImage'])->name('upload-image');
    });
    
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Writer\BadgeController::class, 'index'])->name('index');
        Route::post('/{badge}/feature', [\App\Http\Controllers\Writer\BadgeController::class, 'feature'])->name('feature');
        Route::post('/check', [\App\Http\Controllers\Writer\BadgeController::class, 'check'])->name('check');
    });
    
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Writer\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\Writer\NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-read', [\App\Http\Controllers\Writer\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    });
});

// ==========================================
// PAGES D'ÉTAT VENDOR
// ==========================================
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/pending', function () {
        return view('admin.vendors.pending');
    })->name('pending');
    
    Route::get('/suspended', function () {
        if (view()->exists('admin.vendors.suspended')) {
            return view('admin.vendors.suspended');
        }
        return view('admin.vendors.pending')->with('status', 'suspended');
    })->name('suspended');
    
    Route::get('/verify-email', function () {
        if (view()->exists('admin.vendors.verify-email')) {
            return view('admin.vendors.verify-email');
        }
        return view('admin.vendors.pending')->with('status', 'verify-email');
    })->name('verify-email');
});

// ==========================================
// ROUTES API
// ==========================================
Route::get('/api/countries/{country}/cities', [\App\Http\Controllers\Vendor\TripController::class, 'getCities'])
    ->name('api.countries.cities')
    ->middleware('auth');

Route::post('/api/check-email', [VendorRegistrationController::class, 'checkEmailAvailability'])->name('api.check-email');

Route::prefix('api/destinations')->name('api.destinations.')->group(function () {
    Route::get('/featured', [DestinationApiController::class, 'getFeaturedDestinations'])->name('featured');
    Route::get('/continent/{continentSlug}', [DestinationApiController::class, 'getCountriesByContinent'])->name('continent');
    Route::get('/search', [DestinationApiController::class, 'searchCountries'])->name('search');
    Route::get('/filters', [DestinationApiController::class, 'getFilters'])->name('filters');
    Route::get('/{id}/offres', [DestinationApiController::class, 'getDestinationTrips'])->name('trips');
});

Route::prefix('api/offres')->name('api.trips.')->group(function () {
    Route::get('/popular', [TripController::class, 'getPopularTrips'])->name('popular');
    Route::get('/recent', [TripController::class, 'getRecentTrips'])->name('recent');
    Route::get('/search', [TripController::class, 'apiSearch'])->name('search');
    Route::get('/{slug}', [TripController::class, 'apiShow'])->name('show');
});

Route::prefix('api/search')->name('api.search.')->group(function () {
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/suggestions', [SearchController::class, 'getSuggestions'])->name('suggestions');
});

// ==========================================
// WEBHOOKS
// ==========================================
Route::post('webhook/stripe', [PaymentController::class, 'handleWebhook'])->name('webhook.stripe');

// ==========================================
// ROUTES DE TEST (DÉVELOPPEMENT UNIQUEMENT)
// ==========================================
if (app()->environment('local', 'development')) {
    Route::get('/test-email', function() {
        try {
            Mail::raw('Test email depuis votre application', function($message) {
                $message->to('test@test.fr')
                        ->subject('Test Email - ' . config('app.name'))
                        ->from(config('mail.from.address'), config('app.name'));
            });
            
            return 'Email de test envoyé à test@test.fr !';
        } catch (Exception $e) {
            return 'Erreur: ' . $e->getMessage();
        }
    })->name('test.email.simple');

    Route::middleware(['web'])->group(function () {
        Route::get('/test-session', function () {
            if (session()->has('test_counter')) {
                $counter = session('test_counter');
                session(['test_counter' => $counter + 1]);
            } else {
                session(['test_counter' => 1]);
            }
            session()->save();
            return 'Session counter: ' . session('test_counter') . '<br>Session ID: ' . session()->getId();
        })->name('test.session');

        Route::get('/session-debug', function () {
            return response()->json([
                'session_id' => session()->getId(),
                'session_keys' => array_keys(session()->all()),
                'has_vendor_data' => session()->has('vendor_data'),
                'cookies' => request()->cookies->all(),
                'session_cookie_name' => config('session.cookie'),
                'test_counter' => session('test_counter')
            ]);
        })->name('test.session.debug');
    });
}

// ==========================================
// PAGES UTILITAIRES
// ==========================================
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// ==========================================
// REDIRECTIONS ET COMPATIBILITÉ
// ==========================================
Route::get('/inscription-organisateur', function () {
    return redirect()->route('vendor.register');
});

Route::get('/become-vendor', function () {
    return redirect()->route('vendor.register');
});

Route::get('/vendors', function () {
    return redirect()->route('vendors.index');
});

Route::get('/vendors/{id}', function ($id) {
    $vendor = Vendor::findOrFail($id);
    return redirect()->route('vendors.show', $vendor->slug ?: $vendor->id);
});

Route::get('/sejours', function () {
    return redirect()->route('trips.index');
});

Route::get('/sejours/{id}', function ($id) {
    $trip = \App\Models\Trip::where('id', $id)->orWhere('slug', $id)->firstOrFail();
    return redirect()->route('trips.show', $trip->slug ?: $trip->id);
});

Route::get('/trips', function () {
    return redirect()->route('trips.index');
});

Route::get('/trips/{id}', function ($id) {
    $trip = \App\Models\Trip::where('id', $id)->orWhere('slug', $id)->firstOrFail();
    return redirect()->route('trips.show', $trip->slug ?: $trip->id);
});

Route::get('/voyages', function () {
    return redirect()->route('trips.index');
});

Route::get('/voyages/{id}', function ($id) {
    $trip = \App\Models\Trip::where('id', $id)->orWhere('slug', $id)->firstOrFail();
    return redirect()->route('trips.show', $trip->slug ?: $trip->id);
});

// ==========================================
// FALLBACK ROUTE
// ==========================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});