<!DOCTYPE html>
<html>
<head>
    <title>Upload de fichier</title>
</head>
<body>
    <h1>Formulaire d'upload</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="fichier">
        <button type="submit">Uploader</button>
    </form>
</body>
</html>