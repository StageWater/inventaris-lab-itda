<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peminjaman - ITD Adisutjipto</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 40px; background-color: #f4f7f6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #003366; text-transform: uppercase; }
        .btn-tambah { background-color: #00509E; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th { background-color: #003366; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }
        .status-dipinjam { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Transaksi Peminjaman</h1>
    </div>

    <a href="{{ route('dashboard') }}" style="margin-right: 15px; text-decoration: none; color: #00509E; font-weight: bold;">⬅ Kembali ke Dashboard</a><br><br>

    <a href="{{ route('peminjaman.create') }}" class="btn-tambah">+ Catat Peminjaman Baru</a>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Barang yang Dipinjam</th>
                <th>Tanggal Pinjam</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->nama_peminjam }}</strong></td>
                    <td>{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                    <td>{{ $item->tanggal_pinjam }}</td>
                    <td class="status-dipinjam">{{ $item->status_pinjam }}</td>
                    
                    <!-- KOLOM AKSI (TOMBOL KEMBALIKAN & HAPUS) -->
                    <td>
                        @if($item->status_pinjam == 'Dipinjam')
                            <form action="{{ route('peminjaman.kembalikan', $item->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" style="background-color: #27ae60; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; margin-right: 5px;" onclick="return confirm('Konfirmasi pengembalian barang ini?')">Kembalikan</button>
                            </form>
                        @else
                            <span style="color: #777; font-style: italic; margin-right: 10px;">Dikembalikan</span>
                        @endif

                        <!-- INI DIA TOMBOL HAPUSNYA -->
                        <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;" onclick="return confirm('Yakin ingin menghapus riwayat transaksi ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px;">Belum ada transaksi peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>