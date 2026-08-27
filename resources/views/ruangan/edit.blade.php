<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Ruangan</title>
</head>
<body>
    <h1>Edit Data Ruangan</h1>

    <!-- Form update butuh @method('PUT') agar dikenali oleh sistem Laravel -->
    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <label for="kode_ruangan">Kode Ruangan:</label><br>
        <input type="text" id="kode_ruangan" name="kode_ruangan" value="{{ $ruangan->kode_ruangan }}" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label for="nama_ruangan">Nama Ruangan:</label><br>
        <input type="text" id="nama_ruangan" name="nama_ruangan" value="{{ $ruangan->nama_ruangan }}" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <button type="submit" style="padding: 5px 15px;">Update Data</button>
        <a href="{{ route('ruangan.index') }}" style="margin-left: 10px;">Batal</a>
    </form>
</body>
</html>
