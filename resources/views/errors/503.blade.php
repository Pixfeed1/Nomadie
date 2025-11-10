<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomadie - Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes wrenchRotate {
            0%, 100% {
                transform: rotate(-15deg);
            }
            50% {
                transform: rotate(15deg);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-20px);
                opacity: 0.8;
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .wrench-icon {
            animation: wrenchRotate 3s ease-in-out infinite;
        }

        .floating-dot {
            animation: float 3s ease-in-out infinite;
        }

        .dot-1 {
            animation-delay: 0s;
        }

        .dot-2 {
            animation-delay: 0.5s;
        }

        .dot-3 {
            animation-delay: 1s;
        }

        .status-icon {
            animation: pulse 2s ease-in-out infinite;
        }

        .status-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: #f85149;
            border-radius: 50%;
            animation: ripple 2s ease-out infinite;
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 2.25rem !important;
            }
            .subtitle {
                font-size: 1.125rem !important;
            }
            .illustration {
                width: 200px !important;
                height: 200px !important;
            }
            .wrench-icon {
                width: 80px !important;
                height: 80px !important;
            }
        }
    </style>
</head>
<body class="bg-[#0d1117] text-[#c9d1d9] min-h-screen flex flex-col items-center justify-center p-5">
    <div class="container max-w-[600px] w-full text-center">
        <!-- Logo -->
        <div class="text-[32px] font-bold text-[#58a6ff] mb-12 tracking-tight">
            nomadie
        </div>

        <!-- Illustration -->
        <div class="w-[300px] h-[300px] mx-auto mb-10 relative illustration">
            <div class="w-full h-full flex items-center justify-center relative">
                <!-- Wrench Icon -->
                <svg class="wrench-icon w-[120px] h-[120px] fill-[#58a6ff] opacity-90" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
                </svg>

                <!-- Floating dots -->
                <div class="floating-dot dot-1 absolute top-[20%] left-[15%] w-3 h-3 bg-[#58a6ff] rounded-full"></div>
                <div class="floating-dot dot-2 absolute top-[60%] right-[20%] w-3 h-3 bg-[#58a6ff] rounded-full"></div>
                <div class="floating-dot dot-3 absolute bottom-[20%] left-[25%] w-3 h-3 bg-[#58a6ff] rounded-full"></div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="inline-flex items-center gap-3 bg-[#161b22] border border-[#30363d] rounded-md px-5 py-3 mb-8">
            <div class="status-icon w-4 h-4 bg-[#f85149] rounded-full relative"></div>
            <span class="text-sm text-[#c9d1d9] font-medium">Service temporairement indisponible</span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-5xl font-semibold text-[#f0f6fc] mb-4 tracking-tight">
            Maintenance en cours
        </h1>

        <!-- Subtitle -->
        <p class="text-xl text-[#8b949e] mb-8 font-normal subtitle">
            Nous travaillons à améliorer votre expérience
        </p>

        <!-- Message -->
        <p class="text-base text-[#8b949e] leading-relaxed mb-10">
            Nomadie est actuellement en maintenance programmée. Nous mettons à jour nos systèmes pour vous offrir une meilleure plateforme de découverte d'expériences uniques : yoga, trekking et voyages d'exception.
        </p>

        <!-- Info Section -->
        <div class="my-12 py-8 border-t border-b border-[#21262d]">
            <div class="text-sm text-[#8b949e] uppercase tracking-wider font-semibold mb-4">
                Durée estimée
            </div>
            <div class="text-base text-[#c9d1d9] mb-2">
                Quelques minutes
            </div>
            <div class="text-sm text-[#8b949e] uppercase tracking-wider font-semibold mb-4 mt-4">
                De retour vers
            </div>
            <div class="text-base text-[#c9d1d9]">
                Très bientôt
            </div>
        </div>

        <!-- Footer Links -->
        <div class="mt-12 pt-6 border-t border-[#21262d] flex justify-center gap-6 flex-wrap">
            <a href="#" class="text-[#58a6ff] no-underline text-sm hover:text-[#79c0ff] hover:underline transition-colors">
                Twitter
            </a>
            <a href="#" class="text-[#58a6ff] no-underline text-sm hover:text-[#79c0ff] hover:underline transition-colors">
                Facebook
            </a>
            <a href="mailto:support@nomadie.com" class="text-[#58a6ff] no-underline text-sm hover:text-[#79c0ff] hover:underline transition-colors">
                Support
            </a>
        </div>
    </div>
</body>
</html>
