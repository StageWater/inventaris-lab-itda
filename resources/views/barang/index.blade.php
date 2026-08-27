<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang Inventaris - ITD Adisutjipto</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f4f7f6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #003366; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { color: #555; margin-top: 0; font-size: 16px; font-weight: bold; }
        .nav-menu { margin-bottom: 20px; }
        .nav-menu a { margin-right: 15px; text-decoration: none; color: #00509E; font-weight: bold; }
        .nav-menu a:hover { text-decoration: underline; }
        .btn-tambah { background-color: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; font-weight: bold; }
        .btn-tambah:hover { background-color: #219150; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th { background-color: #003366; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f1f5f9; }
        .status-tersedia { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Barang Inventaris Laboratorium</h1>
        <p>Institut Teknologi Dirgantara Adisutjipto (ITDA) Yogyakarta</p>
    </div>

    <!-- Navigasi Sederhana -->
    <div class="nav-menu">
        <a href="{{ route('dashboard') }}">⬅ Kembali ke Dashboard</a>
    </div>

    <!-- Tombol Tambah Barang (Duplikat sudah dihapus) -->
    <a href="{{ route('barang.create') }}" class="btn-tambah">+ Tambah Barang Baru</a>

    <!-- Tombol Cetak PDF -->
    <a href="{{ route('barang.cetak') }}" style="background-color: #e67e22; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-left: 10px;">📄 Cetak PDF</a>
    
    <!-- Kolom Pencarian -->
    <div style="margin-top: 20px; margin-bottom: 20px;">
        <form action="{{ route('barang.index') }}" method="GET">
            <input type="text" name="katakunci" value="{{ Request::get('katakunci') }}" placeholder="Cari nama atau kode barang..." style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 10px 15px; background-color: #003366; color: white; border: none; border-radius: 5px; cursor:pointer;">🔍 Cari</button>
            <a href="{{ route('barang.index') }}" style="margin-left: 10px; color: #e74c3c; text-decoration: none;">✖ Reset</a>
        </form>
    </div>
        
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Kondisi</th>
                <th>Status</th>
                <th>Foto</th> <!-- Tambahan Kolom Foto -->
                <th>QR Code</th> <!-- Tambahan Kolom QR Code -->
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->kode_barang }}</strong></td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->kondisi }}</td>
                    <td class="status-tersedia">{{ $item->status }}</td>
                    
                    <!-- Penampil Foto -->
                    <td>
                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" width="60" style="border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                        @else
                            <span style="color: #999; font-size: 11px;">Tidak ada</span>
                        @endif
                    </td>

                    <!-- Penampil QR Code -->
                    <td>
                       {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($item->kode_barang) !!}
                    </td>

                    <!-- Tombol Aksi -->
                    <td>
                        <a href="{{ route('barang.edit', $item->id) }}">Edit</a> | 
                        <form action="{{ route('barang.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color:red; background:none; border:none; cursor:pointer; font-size:inherit; padding:0; text-decoration:underline;" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <!-- Colspan diubah menjadi 9 karena jumlah kolom bertambah -->
                    <td colspan="9" style="text-align: center; padding: 30px; color: #888;">
                        Belum ada data barang. Silakan tambah barang baru.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>