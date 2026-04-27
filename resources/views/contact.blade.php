@extends('layouts.app')

@section('title', 'Contact - Vite & Gourmand')

@section('content')
    <section class="py-16 bg-gray-50 min-h-[70vh]">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-gray-900">Nous contacter</h1>
                <p class="mt-3 text-gray-600 text-lg">
                    Une question, une demande ou un besoin particulier ? Envoyez-nous un message.
                </p>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-primary to-blue-700 px-8 py-6 text-white">
                    <h2 class="text-2xl font-semibold">Formulaire de contact</h2>
                    <p class="text-blue-100 mt-1">
                        Nous reviendrons vers vous à l’adresse indiquée.
                    </p>
                </div>

                <div class="p-8">
                    @if(session('success'))
                        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">
                                Titre
                            </label>
                            <input
                                id="titre"
                                name="titre"
                                type="text"
                                value="{{ old('titre') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-primary focus:ring-primary"
                                placeholder="Ex : Demande de renseignements"
                                required
                            >
                            @error('titre')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse mail
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-primary focus:ring-primary"
                                placeholder="vous@example.com"
                                required
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-primary focus:ring-primary"
                                placeholder="Décrivez votre demande..."
                                required
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-white font-semibold hover:bg-blue-700"
                            >
                                <i class="fa-solid fa-paper-plane"></i>
                                Envoyer ma demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection