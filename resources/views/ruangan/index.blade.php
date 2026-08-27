<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Inventaris ITDA</title>
    
    <!-- Memanggil Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Memanggil Tailwind CSS Instan -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Memanggil Ikon Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Konfigurasi Font Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased flex h-screen overflow-hidden">

    <!-- SIDEBAR (Menu Samping) -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <i data-lucide="plane" class="text-blue-700 w-6 h-6 mr-2"></i>
            <span class="font-bold text-lg tracking-tight text-blue-950">ITDA<span class="text-blue-600 font-normal">Lab</span></span>
        </div>
        
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Menu Aktif (Tema Biru ITDA) -->
            <a href="#" class="flex items-center px-3 py-2 text-sm font-semibold bg-blue-50 text-blue-700 border-r-4 border-blue-700 rounded-l-md">
                <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 text-blue-700"></i>
                Dashboard
            </a>
            
            <!-- Menu Tidak Aktif -->
            <a href="{{ route('barang.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md transition-colors">
                <i data-lucide="box" class="w-4 h-4 mr-3 text-slate-400"></i>
                Data Barang
            </a>
            <a href="{{ route('peminjaman.index') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-700 rounded-md transition-colors">
                <i data-lucide="arrow-right-left" class="w-4 h-4 mr-3 text-slate-400"></i>
                Transaksi
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center">
                <!-- Avatar Biru -->
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-blue-950">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">
                        {{ Auth::user()->ruangan_id == null ? 'Super Admin' : 'Admin Ruangan ' . Auth::user()->ruangan_id }}
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT (Isi Halaman) -->
    <main class="flex-1 overflow-y-auto">
        <!-- Header Atas -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <h1 class="text-xl font-bold text-blue-950">Ringkasan Sistem</h1>
            
            <!-- Tombol Aksi Utama (Biru ITDA) -->
            <a href="{{ route('surat.bebas.lab') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-all focus-visible:outline-none bg-blue-700 text-white hover:bg-blue-800 shadow-md shadow-blue-200 h-9 px-4 py-2">
                <i data-lucide="file-check" class="w-4 h-4 mr-2"></i> Cetak Surat Bebas
            </a>
        </header>

        <!-- Konten Utama -->
        <div class="p-8 max-w-7xl mx-auto space-y-6">
            
            <!-- Deretan Kartu Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kartu 1 -->
                <div class="rounded-xl border border-slate-200 bg-white text-blue-950 shadow-sm transition hover:shadow-md">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-slate-500">Total Barang Terdata</h3>
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <i data-lucide="package" class="h-4 w-4 text-blue-600"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-3xl font-bold text-blue-950">{{ $total_barang ?? 0 }}</div>
                        <p class="text-xs text-slate-500 mt-1">Semua aset di laboratorium</p>
                    </div>
                </div>

                <!-- Kartu 2 -->
                <div class="rounded-xl border border-slate-200 bg-white text-blue-950 shadow-sm transition hover:shadow-md">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-slate-500">Barang Tersedia</h3>
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-3xl font-bold text-blue-950">{{ $barang_tersedia ?? 0 }}</div>
                        <p class="text-xs text-slate-500 mt-1">Siap untuk dipinjam mahasiswa</p>
                    </div>
                </div>

                <!-- Kartu 3 -->
                <div class="rounded-xl border border-slate-200 bg-white text-blue-950 shadow-sm transition hover:shadow-md">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-slate-500">Sedang Dipinjam</h3>
                        <div class="p-2 bg-orange-50 rounded-lg">
                            <i data-lucide="clock" class="h-4 w-4 text-orange-600"></i>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-3xl font-bold text-blue-950">{{ $barang_dipinjam ?? 0 }}</div>
                        <p class="text-xs text-slate-500 mt-1">Belum dikembalikan ke lab</p>
                    </div>
                </div>
            </div>

            <!-- Tabel Kosong Bawah -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-blue-950 leading-none tracking-tight">Aktivitas Terakhir</h3>
                        <p class="text-sm text-slate-500 mt-2">Daftar peminjaman barang yang baru saja terjadi.</p>
                    </div>
                </div>
                <div class="p-0">
                    <div class="text-center py-12 text-slate-500 text-sm flex flex-col items-center">
                        <i data-lucide="folder-open" class="h-10 w-10 text-slate-300 mb-3"></i>
                        Silakan kelola menu transaksi untuk melihat data.
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Script Wajib untuk Ikon Lucide -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>