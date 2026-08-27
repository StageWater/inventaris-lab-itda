<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Bebas Lab - {{ $namaPeminjam }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; padding: 20px 40px; }
        
        /* Kop Surat */
        .kop-surat { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .kop-surat h2 { margin: 0; font-size: 14pt; }
        .kop-surat p { margin: 0; font-size: 10pt; font-style: italic; }
        
        /* Isi Surat */
        .judul-surat { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px; font-size: 14pt; }
        .isi-surat { text-align: justify; }
        
        /* Tabel Data Mahasiswa */
        table.data-mhs { margin: 20px 0; width: 100%; }
        table.data-mhs td { padding: 5px; }
        table.data-mhs td:first-child { width: 30%; font-weight: bold; }
        
        /* Bagian Tanda Tangan */
        .ttd-container { width: 100%; margin-top: 50px; }
        .ttd-box { float: right; width: 40%; text-align: center; }
        .ttd-box p { margin: 0; }
        .nama-terang { font-weight: bold; text-decoration: underline; margin-top: 70px; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h1>INSTITUT TEKNOLOGI DIRGANTARA ADISUTJIPTO (ITDA)</h1>
        <p>Jl. Janti Blok R, Lanud Adisutjipto, Yogyakarta 55198 | Telp: (0274) 488435</p>
    </div>

    <!-- JUDUL SURAT -->
    <div class="judul-surat">
        SURAT KETERANGAN BEBAS LABORATORIUM
    </div>

    <!-- ISI SURAT -->
    <div class="isi-surat">
        <p>Kepala Laboratorium Institut Teknologi Dirgantara Adisutjipto (ITDA) dengan ini menerangkan bahwa mahasiswa di bawah ini:</p>
        
        <table class="data-mhs">
            <tr>
                <td>Nama Lengkap</td>
                <td>: {{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: Informatika</td>
            </tr>
            <tr>
                <td>Status Peminjaman</td>
                <td>: <b>TIDAK ADA TANGGUNGAN (BEBAS LAB)</b></td>
            </tr>
        </table>

        <p>Telah mengembalikan seluruh peralatan dan inventaris laboratorium yang dipinjam. Surat keterangan ini diberikan sebagai syarat untuk keperluan Pendaftaran Wisuda / Sidang Tugas Akhir.</p>
        <p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- BAGIAN TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Laboratorium ITDA,</p>
            
            <!-- Ruang untuk Tanda Tangan Asli -->
            <div class="nama-terang">
                ( NAMA DOSEN KEPALA LAB )
            </div>
            <p>NIDN. .....................................</p>
        </div>
    </div>

</body>
</html>
