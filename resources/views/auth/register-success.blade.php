@extends('layouts.public')

@section('title', 'Inscription réussie')

@section('content')
<div class="bg-bg-main min-h-screen py-12">
    <div class="max-w-md mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="h-20 w-20 text-success mx-auto" fill="none" viewBox="0 0 24 24" 
stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 
9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-text-primary mb-4">Inscription réussie !</h2>
            
            <div class="bg-info/10 border border-info rounded-lg p-4 mb-6">
                <p class="text-info font-medium mb-2">Vérifiez votre boîte mail</p>
                <p class="text-sm text-text-secondary">
                    Un email de confirmation a été envoyé à votre adresse. 
                    Cliquez sur le lien dans l'email pour activer votre compte.
                </p>
            </div>
            
            <p class="text-sm text-text-secondary mb-4">
                Vous n'avez pas reçu l'email ? Vérifiez vos spams ou 
                <a href="#" class="text-primary hover:underline">renvoyez l'email</a>
            </p>
            
            <a href="{{ route('home') }}" class="text-primary hover:underline">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
