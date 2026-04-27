<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vite & Gourmand')</title>

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tailwind --}}
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

        /* Hauteurs fixes */
        :root {
            --header-height: 64px;
            --footer-height: 110px;
        }

        main {
            padding-top: calc(var(--header-height) + 1.5rem);
            padding-bottom: calc(var(--footer-height) + 1.5rem);
        }
    </style>
</head>

<body class="font-inter bg-gray-50">

{{-- ================= HEADER ================= --}}
<header
    class="fixed top-0 left-0 right-0 z-50
           bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600
           border-b-4 border-blue-300 shadow-md">

    <div class="mx-auto px-[2%]">
        <div class="flex justify-between items-center h-16">

            <h1 class="text-2xl font-bold text-white">
                <a href="{{ route('home') }}">Vite & Gourmand</a>
            </h1>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="text-white hover:underline text-sm font-medium">
                    Accueil
                </a>
                <a href="#menus" class="text-blue-100 hover:text-white text-sm font-medium">
                    Nos Menus
                </a>
                <a href="#about" class="text-blue-100 hover:text-white text-sm font-medium">
                    À propos
                </a>
                <a href="{{ route('contact.create') }}" class="text-blue-100 hover:text-white text-sm font-medium">
                    Contact
                </a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-100">
                        <i class="fa-solid fa-user mr-2"></i>Mon compte
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm font-medium text-white hover:text-red-200">
                            <i class="fa-solid fa-right-from-bracket mr-1"></i>
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-100">
                        <i class="fa-solid fa-user mr-2"></i>Connexion
                    </a>
                @endauth
            </div>

        </div>
    </div>
</header>

{{-- ================= CONTENU ================= --}}
<main class="mx-auto px-[2%]">
    @yield('content')
</main>

{{-- ================= FOOTER ================= --}}
<footer
    class="fixed bottom-0 left-0 right-0
           bg-gray-900 text-white
           border-t border-gray-700
           z-40"
    style="height: var(--footer-height);">

    <div class="mx-auto px-[2%] h-full">
        <div class="grid grid-cols-1 md:grid-cols-2 h-full items-center">

            {{-- GAUCHE --}}
            <div class="text-gray-400 text-sm space-y-1">
                <a href="#" class="block hover:text-white">
                    Conditions générales de vente
                </a>
                <a href="#" class="block hover:text-white">
                    Mentions légales
                </a>
                <p class="mt-2">
                    &copy; {{ date('Y') }} Vite & Gourmand
                </p>
            </div>

            {{-- DROITE --}}
            <div class="text-gray-400 text-sm md:text-right">
                <h4 class="text-white font-semibold mb-1">Horaires</h4>

                @php
                    $joursOrdre = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];

                    $horairesGroupes = collect($horaires)
                        ->groupBy('jour');
                @endphp

                @foreach($joursOrdre as $jour)
                    @php
                        $plages = $horairesGroupes->get($jour);
                    @endphp

                    @if($plages)
                        <p>
                            {{ $jour }} :
                            {{ $plages->map(fn($h) => $h->heure_ouverture.' - '.$h->heure_fermeture)->implode(' / ') }}
                        </p>
                    @endif
                @endforeach

                @if($horairesGroupes->isEmpty())
                    <p>Aucun horaire renseigné</p>
                @endif
            </div>

        </div>
    </div>
</footer>
{{-- Bootstrap JS (OBLIGATOIRE POUR LES MODALES) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Scripts spécifiques aux pages --}}
@stack('scripts')
</body>
</html>
