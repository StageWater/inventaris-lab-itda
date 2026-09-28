@extends('layouts.app_simalab')

@section('title', 'Surat Bebas Lab | SIMALAB ITDA')
@section('header', 'Surat Bebas Lab')
@section('activeMenu', 'permohonan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-blue-950">Surat Bebas Lab</h2>
            <p class="text-sm text-slate-500 mt-1">Tinjau, beri nomor, dan cetak surat yang diajukan mahasiswa.</p>
        </div>
        <a href="{{ route('permohonan.publik') }}" target="_blank"
            class="inline-flex items-center justify-center rounded-md text-sm font-semibold transition-all bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 h-10 px-5 flex-1 sm:flex-none">
            <i data-lucide="external-link" class="w-4 h-4 mr-2"></i> Link Form Mahasiswa
        </a>
    </div>

    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm flex items-start">
        <i data-lucide="info" class="w-5 h-5 mt-0.5 mr-2 shrink-0 text-blue-600"></i>
        <span>Form publik bisa dibuka siapa saja di <code class="px-1.5 py-0.5 bg-white border border-blue-200 rounded text-xs">{{ url('/permohonan-bebas-lab') }}</code>. Surat hanya bisa diubah selama belum dicetak.</span>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">NIM</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nama</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Judul Skripsi</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Nomor Surat</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Diajukan</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" data-reveal-stagger>
                    @forelse($permohonan as $surat)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $surat->nim }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $surat->nama }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $surat->jurusan }}</td>
                        <td class="px-6 py-4 text-slate-500 max-w-[16rem] truncate">{{ $surat->judul_skripsi }}</td>
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $surat->nomor_surat ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $surat->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($surat->status === 'Belum Dicetak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i> Belum Dicetak
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i> Sudah Dicetak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            @if($surat->status === 'Belum Dicetak')
                            <a href="{{ route('permohonan.edit', $surat->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4 mr-1"></i> Edit
                            </a>
                            @endif
                            @if($surat->nomor_surat)
                            <form action="{{ route('permohonan.cetak', $surat->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center text-blue-700 hover:text-blue-900 font-medium transition-colors">
                                    <i data-lucide="printer" class="w-4 h-4 mr-1"></i> Cetak
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="file-check" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                            <p>Belum ada pengajuan surat bebas lab.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($permohonan->hasPages())
        <div class="mt-4">
            {{ $permohonan->links() }}
        </div>
    @endif
@endsection