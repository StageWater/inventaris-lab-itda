<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Lab - ITD Adisutjipto</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f4f7f6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #003366; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; } /* Warna biru elegan */
        .header p { color: #555; margin-top: 0; font-size: 16px; font-weight: bold; }
        .btn-tambah { background-color: #00509E; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; font-weight: bold; transition: 0.3s; }
        .btn-tambah:hover { background-color: #003366; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th { background-color: #003366; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f1f5f9; }
        .btn-edit { color: #f39c12; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .btn-hapus { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .btn-edit:hover, .btn-hapus:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Inventaris Laboratorium Komputasi</h1>
        <p>Institut Teknologi Dirgantara Adisutjipto (ITDA) Yogyakarta</p>
    </div>

    <!-- Tombol Tambah Data -->
    <a href="{{ route('ruangan.create') }}" class="btn-tambah">+ Tambah Ruangan Baru</a>
    
    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Ruangan</th>
                <th>Nama Ruangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ruangan as $index => $item)
                <tr>
                    <!-- Menampilkan urutan angka otomatis, bukan ID database -->
                    <td>{{ $index + 1 }}</td> 
                    
                    <!-- Ini kuncinya! Menampilkan data asli dari database -->
                    <td><strong>{{ $item->kode_ruangan }}</strong></td>
                    <td>{{ $item->nama_ruangan }}</td>
                    
                    <td>
                       <a href="{{ route('ruangan.edit', $item->id) }}" class="btn-edit">Edit</a>
    <form action="{{ route('ruangan.destroy', $item->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-hapus" style="background:none; border:none; cursor:pointer; font-size:inherit; font-family:inherit;" onclick="return confirm('Yakin ingin menghapus ruangan ini?')">Hapus</button>
    </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #888;">
                        Belum ada data ruangan di database. Silakan tambah ruangan baru.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>