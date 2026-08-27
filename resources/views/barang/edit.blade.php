<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Barang</title>
</head>
<body>
    <h1>Edit Data Barang</h1>

    <form action="{{ route('barang.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <label>Kode Barang:</label><br>
        <input type="text" name="kode_barang" value="{{ $barang->kode_barang }}" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label>Nama Barang:</label><br>
        <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label>Kategori:</label><br>
        <input type="text" name="kategori" value="{{ $barang->kategori }}" required style="margin-bottom: 15px; padding: 5px; width: 300px;"><br>

        <label>Kondisi:</label><br>
        <select name="kondisi" style="margin-bottom: 15px; padding: 5px; width: 313px;">
            <option value="Baik" {{ $barang->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
            <option value="Rusak Ringan" {{ $barang->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
            <option value="Rusak Berat" {{ $barang->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
        </select><br>

        <label>Ditempatkan di Ruangan:</label><br>
        <select name="ruangan_id" required style="margin-bottom: 25px; padding: 5px; width: 313px;">
            @foreach($ruangan as $item)
                <option value="{{ $item->id }}" {{ $barang->ruangan_id == $item->id ? 'selected' : '' }}>
                    {{ $item->kode_ruangan }} - {{ $item->nama_ruangan }}
                </option>
            @endforeach
        </select><br>

        <button type="submit" style="padding: 5px 15px; background-color: #00509E; color: white; border: none; cursor:pointer;">Update Data</button>
        <a href="{{ route('barang.index') }}" style="margin-left: 10px;">Batal</a>
    </form>
</body>
</html>