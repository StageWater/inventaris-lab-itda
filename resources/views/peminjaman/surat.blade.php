<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Bebas Lab - ITD Adisutjipto</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 40px; background-color: #f4f7f6; color: #333; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; margin: auto; text-align: center; }
        input[type="text"] { width: 80%; padding: 10px; margin: 15px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #e67e22; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; }
        .alert { background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>

    <a href="{{ route('dashboard') }}" style="text-decoration: none; color: #00509E; font-weight: bold;">⬅ Kembali ke Dashboard</a><br><br>

    <div class="container">
        <h2 style="color: #003366;">Cetak Surat Bebas Lab</h2>
        <p>Masukkan Nama atau NIM Mahasiswa untuk mengecek tanggungan peminjaman.</p>

        <!-- Pesan Error jika mahasiswa masih punya tanggungan -->
        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('surat.bebas.lab') }}" method="GET">
            <input type="text" name="nama" placeholder="Ketik Nama / NIM Mahasiswa..." required>
            <br>
            <button type="submit">Cek & Download PDF</button>
        </form>
    </div>

</body>
</html>