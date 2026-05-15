<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre avis nous intéresse</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">

    <h1>Bonjour {{ $user->prenom }} {{ $user->nom }},</h1>

    <p>
        Votre commande #{{ $commande->id }} est maintenant terminée.
    </p>

    <p>
        Nous espérons que votre expérience avec
        <strong>Vite & Gourmand</strong>
        vous a plu.
    </p>

    <p>
        Votre avis est important pour nous et aide aussi les futurs clients.
    </p>

    <p>
        Vous pouvez déposer un avis en cliquant sur le lien ci-dessous :
    </p>

    <p>
        <a href="{{ route('login') }}"
           style="
                display:inline-block;
                padding:12px 20px;
                background:#0d6efd;
                color:#fff;
                text-decoration:none;
                border-radius:6px;
           ">
            Me connecter pour déposer un avis
        </a>
    </p>

    <p>
        Merci pour votre confiance,<br>
        L’équipe Vite & Gourmand
    </p>

</body>
</html>