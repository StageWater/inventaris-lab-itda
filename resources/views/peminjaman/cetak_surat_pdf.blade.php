<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Bebas Lab - {{ $nama ?? '' }}</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            padding: 20px 40px; 
            color: #000;
        }
        .kop-surat { 
            text-align: center; 
            margin-bottom: 5px; 
        }
        .kop-surat h1 { 
            margin: 0; 
            font-size: 15pt; 
            font-weight: bold;
            text-transform: uppercase; 
        }
        .kop-surat h2 { 
            margin: 2px 0; 
            font-size: 13pt; 
            font-weight: bold;
            text-transform: uppercase;
        }
        .garis-kop {
            border-top: 2px solid black;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        /* Nomor Surat */
        .nomor-surat { 
            text-align: center; 
            font-size: 12pt;
            margin-bottom: 25px; 
        }

        /* Isi Surat */
        .isi-surat { 
            text-align: justify; 
        }
        .list-keperluan {
            margin-top: 5px;
            margin-bottom: 15px;
            padding-left: 50px;
        }
        
        /* Tabel Data Mahasiswa */
        table.data-mhs { 
            margin: 10px 0 20px 0; 
            width: 100%; 
            border-collapse: collapse;
        }
        table.data-mhs td { 
            padding: 3px 5px; 
            vertical-align: top;
        }
        table.data-mhs td.label { 
            width: 140px; 
        }
        table.data-mhs td.titik-dua { 
            width: 15px; 
        }

        /* Bagian Tanda Tangan */
        .ttd-container { 
            width: 100%; 
            margin-top: 40px; 
        }
        .ttd-box { 
            float: right; 
            width: 320px; 
            text-align: center; 
        }
        .ttd-box p { 
            margin: 0; 
        }
        .nama-pejabat { 
            font-weight: bold; 
            margin-top: 80px; 
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h1>LABORATORIUM TERPADU</h1>
        <h1>ITD ADISUTJIPTO</h1>
        <h2>SURAT KETERANGAN BEBAS PEMINJAMAN PERALATAN</h2>
    </div>

    <div class="garis-kop"></div>

    <!-- NOMOR SURAT -->
    <div class="nomor-surat">
        No: {{ $nomorSurat }}
    </div>

    <!-- ISI SURAT -->
    <div class="isi-surat">
        <p>Surat ini diberikan kepada yang telah menyelesaikan semua urusan administrasi atau peminjaman alat pada Lab. ITDA untuk :</p>
        
        <ol class="list-keperluan">
            <li>Mengikuti Yudisium Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</li>
            <li>Mengambil Ijazah</li>
        </ol>

        <p>Identitas Mahasiswa :</p>
        
        <table class="data-mhs">
            <tr>
                <td class="label">Nama</td>
                <td class="titik-dua">:</td>
                <td><b>{{ strtoupper($nama) }}</b></td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="titik-dua">:</td>
                <td>{{ $nim }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan</td>
                <td class="titik-dua">:</td>
                <td>{{ $jurusan }}</td>
            </tr>
            <tr>
                <td class="label">Judul Skripsi</td>
                <td class="titik-dua">:</td>
                <td>{{ $judul_skripsi }}</td>
            </tr>
        </table>

        <p>Semoga surat ini dapat memenuhi keperluan bagi yang bersangkutan.</p>
    </div>

    <!-- BAGIAN TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <br>
            <p>Mengetahui,</p>
            <p>Kepala Pusat Laboratorium Terpadu</p>
            
            <div class="nama-pejabat">
                Riani Nurdin, S.T. M.Sc.
            </div>
            <p>NIDN: 197510272005012001</p>
        </div>
    </div>

</body>
</html>