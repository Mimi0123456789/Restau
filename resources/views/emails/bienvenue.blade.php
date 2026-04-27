<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
    <h1>Bienvenue {{ $user->prenom }} {{ $user->nom }},</h1>

    <p>
        Votre compte a bien été créé sur <strong>Vite & Gourmand</strong>.
    </p>

    <p>
        Nous sommes ravis de vous compter parmi nos clients.
    </p>

    <p>
        Vous pouvez dès maintenant vous connecter et découvrir nos menus.
    </p>

    <p>
        À bientôt,<br>
        L’équipe Vite & Gourmand
    </p>
</body>
</html>