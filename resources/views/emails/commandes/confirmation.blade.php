@component('mail::message')
# Bonjour {{ $commande->user->prenom ?? '' }},

@if($type === 'modification')
Votre commande #{{ $commande->id }} a bien été modifiée.
@elseif($type === 'statut')
Le statut de votre commande #{{ $commande->id }} a été mis à jour.
@else
Votre commande #{{ $commande->id }} a bien été enregistrée.
@endif

**Date de prestation :** {{ \Carbon\Carbon::parse($commande->date_prestation)->format('d/m/Y') }}

**Heure de livraison :** {{ \Carbon\Carbon::parse($commande->heure_livraison)->format('H:i') }}

**Nombre de personnes :** {{ $commande->nombre_personne }}

**Statut :** {{ $commande->statut }}

À bientôt,<br><br>
L’équipe Vite & Gourmand
@endcomponent