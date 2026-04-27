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
                            <i class="fa-solid fa-user-plus text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-blue-100">Bienvenue</p>
                            <h2 class="text-3xl font-bold">Vite & Gourmand</h2>
                        </div>
                    </div>

                    <h3 class="text-4xl font-bold leading-tight mb-4">
                        Créez votre compte en quelques instants
                    </h3>

                    <p class="text-blue-100 text-base leading-7">
                        Rejoignez-nous pour découvrir nos menus, passer vos commandes et profiter d’un espace client simple et rapide.
                    </p>
                </div>

                <div class="space-y-4 pt-10">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Inscription rapide et sécurisée</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Accès à tous nos menus</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-check mt-1 text-secondary"></i>
                        <p class="text-blue-50">Suivi de vos commandes</p>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10 lg:p-12">
                <div class="lg:hidden text-center mb-8">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center mb-4">
                        <i class="fa-solid fa-user-plus text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Vite & Gourmand</h2>
                    <p class="text-gray-500 mt-2">Créez votre espace client</p>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Inscription</h1>
                    <p class="text-gray-500 mt-2">
                        Remplissez les informations ci-dessous pour créer votre compte.
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="prenom" :value="'Prénom'" class="mb-2" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <x-text-input
                                    id="prenom"
                                    class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                    type="text"
                                    name="prenom"
                                    :value="old('prenom')"
                                    required
                                    autofocus
                                    placeholder="Votre prénom"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nom" :value="'Nom'" class="mb-2" />
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <x-text-input
                                    id="nom"
                                    class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                    type="text"
                                    name="nom"
                                    :value="old('nom')"
                                    required
                                    placeholder="Votre nom"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="telephone" :value="'Numéro de GSM'" class="mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <x-text-input
                                id="telephone"
                                class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                type="text"
                                name="telephone"
                                :value="old('telephone')"
                                required
                                placeholder="06XXXXXXXX"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="adresse_postale" :value="'Adresse postale'" class="mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <x-text-input
                                id="adresse_postale"
                                class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                type="text"
                                name="adresse_postale"
                                :value="old('adresse_postale')"
                                required
                                placeholder="Votre adresse complète"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('adresse_postale')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="'Adresse mail'" class="mb-2" />
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
                                autocomplete="username"
                                placeholder="exemple@email.com"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="'Mot de passe'" class="mb-2" />
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
                                autocomplete="new-password"
                                placeholder="Votre mot de passe"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="mt-2 text-sm text-gray-500 leading-6">
                            10 caractères minimum, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.
                        </p>
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="'Confirmation du mot de passe'" class="mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <x-text-input
                                id="password_confirmation"
                                class="block w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirmez votre mot de passe"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center py-3 text-base rounded-xl bg-primary hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Créer mon compte
                        </x-primary-button>
                    </div>

                    <div class="text-center pt-2">
                        <p class="text-sm text-gray-600">
                            Déjà inscrit ?
                            <a
                                href="{{ route('login') }}"
                                class="font-semibold text-primary hover:text-blue-700"
                            >
                                Se connecter
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>