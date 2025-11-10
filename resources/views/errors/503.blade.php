<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours - Nomadie</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#38B2AC',
                            dark: '#319795',
                            light: '#4FD1C5'
                        },
                        accent: {
                            DEFAULT: '#F6AD55',
                            dark: '#ED8936',
                            light: '#FBD38D'
                        },
                        success: '#48BB78',
                        dark: {
                            DEFAULT: '#1a202c',
                            light: '#2d3748'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        'heading': ['Montserrat', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        @keyframes drift {
            from { transform: translate(0, 0); }
            to { transform: translate(30px, 30px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }

        .bg-pattern {
            background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: drift 30s linear infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-primary via-primary-dark to-dark flex items-center justify-center p-4 sm:p-6 font-sans relative overflow-hidden">

    <!-- Background animated pattern -->
    <div class="absolute inset-0 bg-pattern opacity-30"></div>

    <!-- Floating decorative elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-accent/10 rounded-full blur-xl float-animation" style="animation-delay: 0s;"></div>
    <div class="absolute top-40 right-20 w-32 h-32 bg-primary-light/10 rounded-full blur-xl float-animation" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-success/10 rounded-full blur-xl float-animation" style="animation-delay: 2s;"></div>

    <!-- Main container -->
    <div class="relative z-10 w-full max-w-2xl">
        <!-- Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header with gradient -->
            <div class="relative bg-gradient-to-br from-primary via-primary-dark to-dark-light px-8 py-12 sm:px-12 sm:py-16 text-center overflow-hidden">
                <!-- Animated background pattern -->
                <div class="absolute inset-0 bg-pattern opacity-20"></div>

                <!-- Logo with floating animation -->
                <div class="relative z-10 mb-6 inline-block">
                    <div class="w-20 h-20 bg-white rounded-2xl shadow-lg flex items-center justify-center float-animation">
                        <svg class="w-10 h-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="relative z-10 text-3xl sm:text-4xl font-heading font-bold text-white mb-3">
                    Maintenance planifiée
                </h1>
                <p class="relative z-10 text-base sm:text-lg text-white/90">
                    Nous améliorons votre expérience Nomadie
                </p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8 sm:px-12 sm:py-12">

                <!-- Status badge with pulse effect -->
                <div class="flex items-center justify-center gap-3 mb-8">
                    <div class="relative flex items-center justify-center">
                        <span class="absolute inline-flex h-4 w-4 rounded-full bg-accent opacity-75 pulse-ring"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-accent"></span>
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-accent/10 text-accent-dark border border-accent/20">
                        Maintenance en cours
                    </span>
                </div>

                <!-- Message -->
                <div class="text-center mb-8">
                    <h2 class="text-xl sm:text-2xl font-heading font-semibold text-gray-900 mb-3">
                        Nomadie sera bientôt de retour
                    </h2>
                    <p class="text-base text-gray-600 leading-relaxed max-w-lg mx-auto">
                        Notre équipe effectue actuellement des mises à jour importantes pour vous offrir une meilleure expérience. Nous devrions être de retour dans quelques instants.
                    </p>
                </div>

                <!-- Info grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <!-- Duration -->
                    <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-primary/5 to-primary/10 rounded-xl border border-primary/10">
                        <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Durée estimée</p>
                            <p class="text-sm text-gray-600 mt-0.5">Quelques minutes</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-success/5 to-success/10 rounded-xl border border-success/10">
                        <div class="flex-shrink-0 w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Statut</p>
                            <p class="text-sm text-gray-600 mt-0.5">Tous les systèmes opérationnels</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="mailto:contact@nomadie.com"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Nous contacter
                    </a>
                    <a href="{{ route('login') }}"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-semibold hover:border-primary hover:text-primary hover:bg-gray-50 transition-all duration-200">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Administration
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-t border-gray-200 px-6 py-6 sm:px-12">
                <!-- Social links -->
                <div class="flex justify-center gap-4 mb-4">
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-full text-gray-500 hover:text-primary hover:border-primary hover:-translate-y-1 transition-all duration-200" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-full text-gray-500 hover:text-primary hover:border-primary hover:-translate-y-1 transition-all duration-200" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-full text-gray-500 hover:text-primary hover:border-primary hover:-translate-y-1 transition-all duration-200" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>

                <!-- Copyright -->
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} Nomadie. Tous droits réservés.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
