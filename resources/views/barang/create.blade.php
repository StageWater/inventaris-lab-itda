<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Baru</title>
</head>
<body>
    <h1>Tambah Barang Baru</h1>

    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 400px;">
        @csrf
        
        <label for="kode_barang">Kode Barang:</label><br>
        <input type="text" id="kode_barang" name="kode_barang" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label for="nama_barang">Nama Barang:</label><br>
        <input type="text" id="nama_barang" name="nama_barang" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label for="kategori">Kategori (Contoh: Elektronik, Mebel, dll):</label><br>
        <input type="text" id="kategori" name="kategori" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label for="kondisi">Kondisi:</label><br>
        <select name="kondisi" id="kondisi" style="margin-bottom: 15px; padding: 5px; width: 313px;">
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select><br>

        <!-- Dropdown Dinamis dari Tabel Ruangan -->
        <label for="ruangan_id">Ditempatkan di Ruangan:</label><br>
        <select name="ruangan_id" id="ruangan_id" required style="margin-bottom: 25px; padding: 5px; width: 313px;">
            <option value="">-- Pilih Ruangan --</option>
            @foreach($ruangan as $item)
                <option value="{{ $item->id }}">{{ $item->kode_ruangan }} - {{ $item->nama_ruangan }}</option>
            @endforeach
        </select><br>
        <label>Foto Barang (Opsional):</label><br>
        <input type="file" name="foto" accept="image/*" style="margin-bottom: 25px;"><br>
        <button type="submit" style="padding: 5px 15px; background-color: #00509E; color: white; border: none; cursor:pointer;">Simpan Data</button>
        <a href="{{ route('barang.index') }}" style="margin-left: 10px;">Batal</a>
    </form>
</body>
</html>