<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Vite & Gourmand') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563eb',
                        secondary: '#f59e0b',
                        accent: '#10b981',
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { display: none; }

        :root {
            --header-height: 64px;
            --footer-height: 110px;
        }

        main {
            min-height: calc(100vh - var(--header-height) - var(--footer-height));
            padding-top: calc(var(--header-height) + 2rem);
            padding-bottom: calc(var(--footer-height) + 2rem);
        }
    </style>
</head>
<body class="font-inter bg-gray-50">

    <header class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 border-b-4 border-blue-300 shadow-md">
        <div class="mx-auto px-[2%]">
            <div class="flex justify-between items-center h-16">
                <h1 class="text-2xl font-bold text-white">
                    <a href="{{ route('home') }}">Vite & Gourmand</a>
                </h1>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}"
                       class="text-sm font-medium text-white hover:text-blue-100">
                        Accueil
                    </a>

                    @if(Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-100">
                            <i class="fa-solid fa-user mr-2"></i>Connexion
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto px-4">
        {{ $slot }}
    </main>

    <footer
        class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white border-t border-gray-700 z-40"
        style="height: var(--footer-height);"
    >
        <div class="mx-auto px-[2%] h-full">
            <div class="grid grid-cols-1 md:grid-cols-2 h-full items-center">
                <div class="text-gray-400 text-sm space-y-1">
                    <a href="#" class="block hover:text-white">Conditions générales de vente</a>
                    <a href="#" class="block hover:text-white">Mentions légales</a>
                    <p class="mt-2">&copy; {{ date('Y') }} Vite & Gourmand</p>
                </div>

                <div class="text-gray-400 text-sm md:text-right">
                    <p class="text-white font-semibold">Vite & Gourmand</p>
                    <p>Connexion et inscription sécurisées</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>