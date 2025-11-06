<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/session-test', function() {
    Log::info('Session test accessed', [
        'session_id' => session()->getId(),
        'has_counter' => session()->has('test_counter')
    ]);
    
    if (session()->has('test_counter')) {
        $counter = session('test_counter');
        session(['test_counter' => $counter + 1]);
    } else {
        session(['test_counter' => 1]);
    }
    
    // Force la sauvegarde de la session
    session()->save();
    
    return 'Session ID: ' . session()->getId() . '<br>Counter: ' . session('test_counter');
});

Route::get('/session-test-step1', function() {
    $testData = 'Test data at ' . now();
    
    Log::info('Session test step 1', [
        'session_id' => session()->getId(),
        'test_data' => $testData
    ]);
    
    session(['test_data' => $testData]);
    
    // Force la sauvegarde de la session
    session()->save();
    
    return redirect('/session-test-step2');
});

Route::get('/session-test-step2', function() {
    $data = session('test_data') ?? 'No data found';
    
    Log::info('Session test step 2', [
        'session_id' => session()->getId(),
        'has_data' => session()->has('test_data'),
        'test_data' => $data
    ]);
    
    return 'Session ID: ' . session()->getId() . '<br>Data: ' . $data;
});