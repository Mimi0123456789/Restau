<head>
    <title>Vite & Gourmand</title>
</head>
<x-guest-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            
            <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-primary via-blue-600 to-blue-800 text-white p-10">
                <div>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center">
                            <i class="fa-solid fa-utensils text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-blue-100">Bienvenue</p>
                            <h2 class="text-3xl font-bold">Vite & Gourmand</h2>
                        </div>
                    </div>

                    <h3 class="text-4xl font-bold leading-tight mb-4">
                        Connectez-vous à votre espace client
                    </h3>

                    <p class="text-blue-100 text-base leading-7">
                        Retrouvez vos commandes, consultez nos menus et gérez votre compte en quelques clics.
                    </p>
                </div>

                <div class="space-y-4 pt-10">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Accès rapide à vos commandes</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Consultation simple de nos menus</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Suivi personnalisé de votre espace</p>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10 lg:p-12">
                <div class="lg:hidden text-center mb-8">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center mb-4">
                        <i class="fa-solid fa-utensils text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Vite & Gourmand</h2>
                    <p class="text-gray-500 mt-2">Connectez-vous à votre espace client</p>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Connexion</h1>
                    <p class="text-gray-500 mt-2">
                        Heureux de vous revoir. Entrez vos identifiants pour continuer.
                    </p>
                </div>

                <x-auth-session-status class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="'Adresse email'" class="mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <x-text-input
                                id="email"
                                class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="exemple@email.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <x-input-label for="password" :value="'Mot de passe'" />
                            @if (Route::has('password.request'))
                                <a
                                    class="text-sm text-primary hover:text-blue-700 font-medium"
                                    href="{{ route('password.request') }}"
                                >
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <x-text-input
                                id="password"
                                class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input
                                id="remember_me"
                                type="checkbox"
                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                name="remember"
                            >
                            <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center py-3 text-base rounded-xl bg-primary hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            Se connecter
                        </x-primary-button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Vous n’avez pas encore de compte ?
                        <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-blue-700">
                            Créer un compte
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>