<form action="{{ route('surat.bebas.lab') }}" method="GET">
    <div class="mb-3">
        <label>NIM Mahasiswa</label>
        <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM..." required>
    </div>
    
    <div class="mb-3">
        <label>Jurusan</label>
        <input type="text" name="jurusan" class="form-control" placeholder="Contoh: TEKNIK INDUSTRI">
    </div>

    <div class="mb-3">
        <label>Judul Skripsi</label>
        <textarea name="judul_skripsi" class="form-control" placeholder="Masukkan Judul Skripsi..."></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Cek & Cetak Surat</button>
</form>