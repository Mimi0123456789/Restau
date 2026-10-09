@extends('layouts.app')

@section('title', 'Mon panier')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold mb-6">Mon panier</h1>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        @if($menus->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                Votre panier est vide.
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <div class="space-y-4">
                    @foreach($menus as $menu)
                        <div class="border rounded-lg p-4 flex justify-between items-center gap-4 flex-wrap">
                            <div>
                                <div class="font-semibold">{{ $menu->titre }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ number_format($menu->prix_par_personne, 2, ',', ' ') }} € / pers.
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <form method="POST" action="{{ route('panier.update', $menu) }}" class="quantity-form flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number"
                                           name="quantite"
                                           value="{{ $panier[$menu->id]['quantite'] ?? 1 }}"
                                           min="{{ $menu->nombre_personne_minimum }}"
                                           data-min="{{ $menu->nombre_personne_minimum }}"
                                           class="quantity-input form-control"
                                           style="width:100px;">
                                </form>

                                <form method="POST" action="{{ route('panier.remove', $menu) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Retirer</button>
                                </form>
                            </div>

                            <div class="quantity-error text-danger text-sm w-full" data-menu="{{ $menu->id }}" hidden></div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-end">
                    <p class="text-lg font-semibold">
                        Total menus : {{ number_format($totalMenus, 2, ',', ' ') }} €
                    </p>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('panier.checkout.form') }}" class="btn btn-primary" id="continue-order-button">
                    Continuer la commande
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function validateQuantities() {
        const forms = document.querySelectorAll('.quantity-form');
        const continueButton = document.getElementById('continue-order-button');
        let isValid = true;

        forms.forEach(form => {
            const input = form.querySelector('.quantity-input');
            const errorBlock = form.closest('.border')?.querySelector('.quantity-error');
            const minimum = Number(input.dataset.min || 1);
            const value = Number(input.value || 0);

            if (value < minimum) {
                isValid = false;
                if (errorBlock) {
                    errorBlock.textContent = `La quantité minimum pour ce menu est de ${minimum} personne(s).`;
                    errorBlock.hidden = false;
                }
                input.classList.add('is-invalid');
            } else {
                if (errorBlock) {
                    errorBlock.textContent = '';
                    errorBlock.hidden = true;
                }
                input.classList.remove('is-invalid');
            }
        });

        if (continueButton) {
            continueButton.disabled = !isValid;
            continueButton.classList.toggle('opacity-50', !isValid);
            continueButton.classList.toggle('pointer-events-none', !isValid);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', validateQuantities);
            input.addEventListener('change', function () {
                const form = this.closest('form');
                const minimum = Number(this.dataset.min || 1);

                if (Number(this.value || 0) >= minimum) {
                    form.submit();
                }
            });
        });

        validateQuantities();
    });
</script>
@endpush