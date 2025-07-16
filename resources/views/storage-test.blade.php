<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Image Storage Test</title>
</head>
<body>
    <h1>Image test depuis storage</h1>

    <p>Image chargée avec asset() :</p>
    <img src="{{ asset('storage/test.jpg') }}" alt="image de test" width="300">
</body>
</html>