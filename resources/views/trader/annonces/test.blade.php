<!DOCTYPE html>
<html>
<head>
    <title>Test fichier</title>
</head>
<body>
    <p>URL générée :</p>
    <p>{{ asset('storage/file.txt') }}</p>

    <p>Lien cliquable :</p>
    <a href="{{ asset('storage/file.txt') }}" target="_blank">Voir le fichier</a>
</body>
</html>