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
                        primary: '#38B2AC',
                        accent: '#F6AD55',
                        success: '#48BB78',
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-20px) translateX(10px);
                opacity: 1;
            }
        }

        @keyframes pulse-ripple {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }

        .icon-rotate {
            animation: rotate 3s linear infinite;
        }

        .dot-float-1 {
            animation: float 3s ease-in-out infinite;
        }

        .dot-float-2 {
            animation: float 4s ease-in-out infinite 0.5s;
        }

        .dot-float-3 {
            animation: float 5s ease-in-out infinite 1s;
        }

        .dot-float-4 {
            animation: float 3.5s ease-in-out infinite 1.5s;
        }

        .pulse-ripple {
            animation: pulse-ripple 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-[#0d1117] flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Floating dots animation -->
    <div class="absolute top-[10%] left-[15%] w-2 h-2 bg-primary rounded-full dot-float-1"></div>
    <div class="absolute top-[20%] right-[20%] w-3 h-3 bg-accent rounded-full dot-float-2"></div>
    <div class="absolute bottom-[15%] left-[25%] w-2.5 h-2.5 bg-success rounded-full dot-float-3"></div>
    <div class="absolute bottom-[30%] right-[15%] w-2 h-2 bg-primary rounded-full dot-float-4"></div>
    <div class="absolute top-[50%] left-[10%] w-1.5 h-1.5 bg-accent rounded-full dot-float-1" style="animation-delay: 0.3s;"></div>
    <div class="absolute top-[70%] right-[25%] w-2 h-2 bg-success rounded-full dot-float-2" style="animation-delay: 0.8s;"></div>

    <!-- Main card -->
    <div class="relative z-10 w-full max-w-2xl">
        <div class="bg-[#161b22] rounded-2xl shadow-2xl border border-[#30363d] overflow-hidden backdrop-blur-sm">

            <!-- Header -->
            <div class="px-8 py-12 text-center">
                <!-- Logo with rotation animation -->
                <div class="mb-6 inline-flex items-center justify-center">
                    <div class="relative">
                        <div class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary icon-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">
                    Maintenance en cours
                </h1>
                <p class="text-lg text-gray-400">
                    Nous améliorons Nomadie pour vous
                </p>
            </div>

            <!-- Content -->
            <div class="px-8 pb-12">

                <!-- Status badge with pulse -->
                <div class="flex items-center justify-center mb-8">
                    <div class="relative flex items-center gap-3 px-6 py-3 bg-accent/10 border border-accent/20 rounded-full">
                        <div class="relative flex items-center justify-center">
                            <span class="absolute inline-flex h-4 w-4 rounded-full bg-accent pulse-ripple"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-accent"></span>
                        </div>
                        <span class="text-sm font-semibold text-accent">
                            Maintenance planifiée
                        </span>
                    </div>
                </div>

                <!-- Message -->
                <div class="text-center mb-8">
                    <p class="text-base text-gray-300 leading-relaxed max-w-lg mx-auto">
                        Notre équipe effectue actuellement des mises à jour importantes.
                        Nous serons de retour très bientôt avec de nouvelles fonctionnalités.
                    </p>
                </div>

                <!-- Info grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <!-- Duration -->
                    <div class="flex items-start gap-4 p-5 bg-[#0d1117] border border-[#30363d] rounded-xl">
                        <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white mb-1">Durée estimée</p>
                            <p class="text-sm text-gray-400">Quelques minutes</p>
                        </div>
                    </div>

                    <!-- Return time -->
                    <div class="flex items-start gap-4 p-5 bg-[#0d1117] border border-[#30363d] rounded-xl">
                        <div class="flex-shrink-0 w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white mb-1">Retour prévu</p>
                            <p class="text-sm text-gray-400">Bientôt disponible</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="mailto:contact@nomadie.com"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-primary/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contactez-nous
                    </a>
                    <a href="{{ route('login') }}"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-[#0d1117] hover:bg-[#161b22] border border-[#30363d] text-gray-300 font-semibold rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Administration
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-[#0d1117] border-t border-[#30363d] px-8 py-6">
                <!-- Social links -->
                <div class="flex justify-center gap-4 mb-4">
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-[#161b22] border border-[#30363d] rounded-full text-gray-400 hover:text-primary hover:border-primary transition-all duration-200" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center bg-[#161b22] border border-[#30363d] rounded-full text-gray-400 hover:text-accent hover:border-accent transition-all duration-200" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="mailto:support@nomadie.com" class="w-10 h-10 flex items-center justify-center bg-[#161b22] border border-[#30363d] rounded-full text-gray-400 hover:text-success hover:border-success transition-all duration-200" aria-label="Email">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
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
