<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Cek Surat Bebas Lab</h2>
        <p class="text-sm text-slate-500 mt-1">Masukkan NIM dan nama lengkap seperti saat mengajukan.</p>
    </div>

    <form method="GET" action="{{ route('permohonan.publik.cek') }}" class="space-y-4">
        <div>
            <label for="nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
            <input id="nim" type="text" name="nim" value="{{ request('nim') }}" required maxlength="30" placeholder="Contoh: 621801234"
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            @error('nim')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" value="{{ request('nama') }}" required maxlength="255"
                class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all text-slate-700 placeholder-slate-400">
            @error('nama')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
            <i data-lucide="search"></i> Cek Status
        </button>
    </form>

    @isset($surat)
        <div class="mt-6 pt-6 border-t border-slate-200 space-y-4">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Nama</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $surat->nama }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">NIM</span>
                    <span class="font-semibold text-slate-800">{{ $surat->nim }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Jurusan</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $surat->jurusan }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">Nomor Surat</span>
                    <span class="font-semibold text-slate-800">{{ $surat->nomor_surat ?? 'belum diberi nomor' }}</span>
                </div>
            </div>

            @if($surat->status === 'Sudah Dicetak')
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-start" role="alert">
                    <i data-lucide="check-circle" class="w-5 h-5 mt-0.5 mr-2 shrink-0"></i>
                    <span>Surat sudah dicetak dan bisa diunduh.</span>
                </div>
                <a href="{{ route('permohonan.publik.unduh', ['id' => $surat->id, 'nim' => $surat->nim]) }}"
                    class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="download"></i> Unduh Surat PDF
                </a>
            @else
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm flex items-start" role="status">
                    <i data-lucide="clock" class="w-5 h-5 mt-0.5 mr-2 shrink-0"></i>
                    <span>Pengajuan Anda sudah diterima laboratory dan sedang menunggu nomor surat. Halaman ini bisa Anda buka lagi nanti untuk mengunduh.</span>
                </div>
            @endif
        </div>
    @endisset

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('permohonan.publik') }}" class="text-blue-700 hover:text-blue-900 font-medium">Kembali ke form pengajuan</a>
    </p>
</x-guest-layout>
