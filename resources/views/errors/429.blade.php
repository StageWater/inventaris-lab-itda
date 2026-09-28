<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto w-14 h-14 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="clock-alert" class="w-7 h-7"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-800">Terlalu Banyak Pengiriman</h2>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Form hanya bisa dikirim maksimal <strong>3 kali per menit</strong> dari satu alamat.
            Mohon tunggu sebentar sebelum mencoba lagi.
        </p>
        <a href="{{ route('permohonan.publik') }}"
            class="mt-6 inline-block px-5 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-lg transition-colors">
            Kembali ke Form
        </a>
    </div>
</x-guest-layout>
