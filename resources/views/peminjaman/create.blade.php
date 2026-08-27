<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Peminjaman Barang</title>
</head>
<body style="font-family: 'Segoe UI', sans-serif; margin: 40px; background-color: #f4f7f6;">
    <h1 style="color: #003366;">Catat Peminjaman Barang</h1>

    <form action="{{ route('peminjaman.store') }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 400px;">
        @csrf
        
        <label>Nama Peminjam:</label><br>
        <input type="text" name="nama_peminjam" required style="margin-bottom: 15px; padding: 8px; width: 95%;"><br>

        <label>Barang (Hanya yang Tersedia):</label><br>
        <select name="barang_id" required style="margin-bottom: 15px; padding: 8px; width: 100%;">
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $item)
                <option value="{{ $item->id }}">{{ $item->kode_barang }} - {{ $item->nama_barang }}</option>
            @endforeach
        </select><br>

        <label>Tanggal Pinjam:</label><br>
        <!-- Tipe "date" agar muncul kalender otomatis -->
        <input type="date" name="tanggal_pinjam" required style="margin-bottom: 25px; padding: 8px; width: 95%;"><br>

        <button type="submit" style="padding: 10px 15px; background-color: #00509E; color: white; border: none; cursor:pointer; font-weight: bold; border-radius: 5px;">Simpan Transaksi</button>
        <a href="{{ route('peminjaman.index') }}" style="margin-left: 10px; color: #555; text-decoration: none;">Batal</a>
    </form>
</body>
</html>