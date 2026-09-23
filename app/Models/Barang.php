<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori', 'ruangan_id', 'kondisi', 'status', 'qr_code', 'keterangan', 'foto'
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    // Renderer SVG murni PHP (tak butuh ekstensi GD/Imagick), disimpan sekali di
    // storage; kolom qr_code menyimpan path relatif yang dipakai Storage::url().
    public function generateQrCode(): self
    {
        try {
            $svg = QrCode::format('svg')->size(300)->margin(2)
                ->generate(route('barang.show', $this->id));
            $file = 'qr/' . Str::slug($this->kode_barang) . '.svg';
            Storage::disk('public')->put($file, $svg);
            $this->update(['qr_code' => $file]);
        } catch (\Throwable $e) {
            // QR cadangan: kolom nullable, biarkan kosong bila render gagal.
        }
        return $this;
    }
}