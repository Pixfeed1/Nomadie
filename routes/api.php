<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Api\SeoAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour récupérer les pays par continent
Route::get('/destinations/continent/{continentSlug}', [DestinationController::class, 'getCountriesByContinent']);

// ==========================================
// ROUTES NOMADSEO
// ==========================================
Route::prefix('seo')->middleware('auth:sanctum')->group(function () {
    // Analyse en temps réel
    Route::post('/analyze', [SeoAnalysisController::class, 'analyze'])->name('api.seo.analyze');
    
    // Obtenir une analyse existante
    Route::get('/analysis/{id}', [SeoAnalysisController::class, 'getAnalysis'])->name('api.seo.analysis');
    
    // Obtenir les critères pour le type de rédacteur
    Route::get('/criteria', [SeoAnalysisController::class, 'getCriteria'])->name('api.seo.criteria');
    
    // Suggestions de maillage interne
    Route::post('/suggestions', [SeoAnalysisController::class, 'getSuggestions'])->name('api.seo.suggestions');
    Route::put('/suggestions/{id}', [SeoAnalysisController::class, 'updateSuggestion'])->name('api.seo.suggestions.update');
    
    // Historique et statistiques
    Route::get('/history', [SeoAnalysisController::class, 'history'])->name('api.seo.history');
    Route::get('/stats', [SeoAnalysisController::class, 'stats'])->name('api.seo.stats');
});