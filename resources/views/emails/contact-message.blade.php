<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222;">
    <h2>Nouveau message de contact</h2>

    <p><strong>Titre :</strong> {{ $titre }}</p>
    <p><strong>Email du visiteur :</strong> {{ $email }}</p>

    <p><strong>Description :</strong></p>
    <p>{!! nl2br(e($description)) !!}</p>
</body>
</html>
