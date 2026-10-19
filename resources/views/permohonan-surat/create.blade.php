<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Surat Bebas Lab</h2>
        <p class="text-sm text-slate-500 mt-1">Formulir pengajuan mahasiswa. Isi data dengan benar.</p>
    </div>

    @if(session('status'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm flex items-center" role="alert">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-2 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('permohonan.publik.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
            <input id="nim" type="text" name="nim" value="{{ old('nim') }}" required maxlength="30" placeholder="Contoh: 621801234"
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            @error('nim')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required maxlength="255" placeholder="Nama sesuai dokumen"
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            @error('nama')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jurusan" class="block text-sm font-medium text-slate-700 mb-1">Prodi</label>
            <select id="jurusan" name="jurusan" required
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 bg-white">
                <option value="" disabled selected>-- Pilih Prodi --</option>
                @foreach(['S1 TEKNIK DIRGANTARA', 'S1 TEKNIK MESIN', 'D3 AERONAUTIKA', 'S1 TEKNIK INDUSTRI', 'S1 INFORMATIKA', 'S1 TEKNIK ELEKTRO'] as $p)
                    <option value="{{ $p }}" {{ old('jurusan') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            @error('jurusan')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="judul_skripsi" class="block text-sm font-medium text-slate-700 mb-1">Judul Skripsi</label>
            <textarea id="judul_skripsi" name="judul_skripsi" required rows="3" maxlength="255" placeholder="Masukkan judul skripsi..."
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">{{ old('judul_skripsi') }}</textarea>
            @error('judul_skripsi')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tanggal_yudisium" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Yudisium</label>
            <input id="tanggal_yudisium" type="date" name="tanggal_yudisium" value="{{ old('tanggal_yudisium') }}" required
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700">
            <p class="mt-1.5 text-xs text-slate-500">Tanggal, bulan, dan tahun yudisium Anda. Tanggal ini yang ditulis di surat bebas lab.</p>
            @error('tanggal_yudisium')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
            <i data-lucide="send"></i> Kirim Pengajuan
        </button>
    </form>
</x-guest-layout>