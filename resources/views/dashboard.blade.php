<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - ITD Adisutjipto</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; color: #333; }
        .navbar { background-color: #003366; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 20px; }
        .container { padding: 40px; }
        
        /* Desain Kotak Statistik */
        .grid-stats { display: flex; gap: 20px; margin-bottom: 40px; }
        .card-stat { flex: 1; padding: 20px; border-radius: 8px; color: white; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-stat h3 { margin: 0; font-size: 16px; text-transform: uppercase; opacity: 0.9; }
        .card-stat p { margin: 10px 0 0 0; font-size: 36px; font-weight: bold; }
        
        /* Warna Kotak */
        .bg-blue { background-color: #3498db; }
        .bg-green { background-color: #2ecc71; }
        .bg-orange { background-color: #e67e22; }
        .bg-red { background-color: #e74c3c; }

        /* Desain Menu Utama */
        .menu-container { display: flex; gap: 15px; }
        .btn-menu { flex: 1; background-color: white; color: #003366; text-align: center; padding: 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; border: 2px solid #003366; transition: 0.3s; }
        .btn-menu:hover { background-color: #003366; color: white; }
        
        .btn-logout { background-color: transparent; color: white; border: 1px solid white; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-logout:hover { background-color: white; color: #003366; }
    </style>
</head>
<body>

    <!-- Navigasi Atas -->
    <div class="navbar">
        <h2>Sistem Inventaris Lab ITDA</h2>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-logout">🚪 Keluar (Logout)</button>
        </form>
    </div>

    <div class="container">
        <h1 style="margin-top: 0;">Selamat Datang, Admin!</h1>
        <p style="color: #666; margin-bottom: 30px;">Berikut adalah ringkasan stok barang di laboratorium saat ini.</p>

        <!-- Kotak Statistik -->
        <div class="grid-stats">
            <div class="card-stat bg-blue">
                <h3>Total Barang</h3>
                <p>{{ $total_barang }}</p>
            </div>
            <div class="card-stat bg-green">
                <h3>Tersedia</h3>
                <p>{{ $barang_tersedia }}</p>
            </div>
            <div class="card-stat bg-orange">
                <h3>Sedang Dipinjam</h3>
                <p>{{ $barang_dipinjam }}</p>
            </div>
            <div class="card-stat bg-red">
                <h3>Kondisi Rusak</h3>
                <p>{{ $barang_rusak }}</p>
            </div>
        </div>

        <!-- Menu Utama -->
        <h2 style="margin-bottom: 15px;">Menu Utama</h2>
        <div class="menu-container">
            <a href="{{ route('ruangan.index') }}" class="btn-menu">🏢 Kelola Data Ruangan</a>
            <a href="{{ route('barang.index') }}" class="btn-menu">💻 Kelola Data Barang</a>
            <a href="{{ route('peminjaman.index') }}" class="btn-menu">🔄 Transaksi Peminjaman</a>
            
            <!-- Tombol Baru: Surat Bebas Lab -->
            <a href="{{ route('surat.bebas.lab') }}" class="btn-menu" style="border-color: #e67e22; color: #e67e22;">📄 Surat Bebas Lab</a>
        </div>

</body>
</html>