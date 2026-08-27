<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ruangan Baru</title>
</head>
<body>
    <h1>Tambah Ruangan Baru</h1>
<!-- Menampilkan Pesan Error Validasi -->
    @if ($errors->any())
        <div style="background-color: #ffcccc; color: #cc0000; padding: 10px; margin-bottom: 20px; border-radius: 5px; width: 400px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
   <!-- Formulir untuk mengisi data -->
    <form action="{{ route('ruangan.store') }}" method="POST">
        @csrf
        
        <!-- Tambahan Baru: Kode Ruangan -->
        <label for="kode_ruangan">Kode Ruangan:</label><br>
        <input type="text" id="kode_ruangan" name="kode_ruangan" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <!-- Yang sudah ada: Nama Ruangan -->
        <label for="nama_ruangan">Nama Ruangan:</label><br>
        <input type="text" id="nama_ruangan" name="nama_ruangan" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <button type="submit" style="padding: 5px 15px;">Simpan Data</button>
        <a href="{{ route('ruangan.index') }}" style="margin-left: 10px;">Batal</a>
    </form>


</body>
</html>