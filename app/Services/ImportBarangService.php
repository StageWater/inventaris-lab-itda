<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Ruangan;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportBarangService
{
    private array $usedCodes = [];
    private int $autoSeq = 1;
    private int $roomSeq = 0;
    private int $ruanganBaru = 0;

    public function import(string $filePath, ?string $kategori = null): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        $kategori ??= $this->kategoriFromFilename(basename($filePath));
        $this->roomSeq = (Ruangan::max('id') ?? 0) + 1;

        $barangBaru = 0;

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $sheet = $worksheet->toArray();

            $meta = $this->detect($sheet);
            if ($meta === null) {
                continue;
            }
            [$rowHeader, $cols, $style] = $meta;

            $namaRuang = $this->ruanganName($worksheet->getTitle(), $sheet, $rowHeader, $style);

            // Lewati sheet yang seluruh kode induknya sudah ada (mis. sheet duplikat "ALN ASTEP").
            if ($style === 'b2' && $this->sheetSudahAda($sheet, $rowHeader, $cols)) {
                continue;
            }

            // Jika ada kolom penanda ruangan per baris (mis. kolom "Lab"), jangan buat ruangan kosong untuk sheet.
            if (($cols['ruang'] ?? null) === null) {
                $ruanganId = $this->roomId($namaRuang);
            }

            $lastRuang = null;
            foreach ($sheet as $i => $row) {
                if ($i <= $rowHeader + 1) {
                    continue;
                }
                $items = $style === 'dasar'
                    ? $this->parseDasar($row, $cols)
                    : $this->parseStandard($row, $cols);

                if ($items === null) {
                    continue;
                }

                foreach ($items as $item) {
                    // Sel ruangan yang kosong mewarisi ruangan baris sebelumnya.
                    if ($item['ruangan'] === null && $style === 'b2') {
                        $item['ruangan'] = $lastRuang;
                    }
                    if ($item['ruangan'] !== null) {
                        $lastRuang = $item['ruangan'];
                    }
                    $ruanganTujuan = $item['ruangan'] ? $this->roomId($item['ruangan']) : ($ruanganId ?? $this->roomId($namaRuang));
                    $barang = Barang::create([
                        'kode_barang' => $item['kode'],
                        'nama_barang' => $item['nama'],
                        'kategori' => $kategori,
                        'ruangan_id' => $ruanganTujuan,
                        'kondisi' => $item['kondisi'],
                        'status' => 'Tersedia',
                        'keterangan' => $item['keterangan'] ?: null,
                    ]);
                    $barang->generateQrCode();
                    $barangBaru++;
                }
            }
        }

        return ['ruangan' => $this->ruanganBaru, 'barang' => $barangBaru];
    }

    private function roomId(string $nama): int
    {
        $ruangan = Ruangan::firstOrCreate(
            ['nama_ruangan' => trim($nama)],
            ['kode_ruangan' => 'LAB-' . str_pad($this->roomSeq++, 2, '0', STR_PAD_LEFT)],
        );
        $this->ruanganBaru += $ruangan->wasRecentlyCreated ? 1 : 0;
        return $ruangan->id;
    }

    private function kategoriFromFilename(string $file): string
    {
        $f = strtolower($file);
        return match (true) {
            str_contains($f, 'hibah') || str_contains($f, 'pp-pts') => 'Alat Hibah',
            str_contains($f, 'komputasi') => 'Komputasi',
            str_contains($f, 'bahasa') => 'Bahasa',
            str_contains($f, 'dasar teknik') => 'Dasar Teknik',
            str_contains($f, 'manufaktur') => 'Manufaktur & Dirgantara',
            str_contains($f, 'astep') => 'ASTEP',
            str_contains($f, 'prestasi') => 'Prestasi & Ergonomi',
            default => 'Umum',
        };
    }

    private function detect(array $sheet): ?array
    {
        foreach ($sheet as $i => $row) {
            $texts = array_map(fn ($c) => strtolower(trim((string) $c)), $row);
            $hasNama = array_filter($texts, fn ($t) => $t === 'nama barang' || $t === 'nama alat');
            if (!$hasNama) {
                continue;
            }
            $style = array_filter($texts, fn ($t) => str_contains($t, 'induk') && str_contains($t, 'no')) ? 'b2' : 'dasar';
            return [$i, $this->columns($sheet, $i, $style), $style];
        }
        return null;
    }

    private function columns(array $sheet, int $rowHeader, string $style): array
    {
        $h = $sheet[$rowHeader];
        $s = $sheet[$rowHeader + 1] ?? [];

        $idx = function (array $lines, string $needle) {
            foreach ($lines as $line) {
                foreach ($line as $i => $cell) {
                    if ($this->matchCell(strtolower(trim((string) $cell)), $needle)) {
                        return $i;
                    }
                }
            }
            return null;
        };

        $find = fn (string $needle) => $idx([$s, $h], $needle);

        if ($style === 'dasar') {
            $sub = array_map(fn ($c) => strtolower(trim((string) $c)), $s);
            return [
                'style' => 'dasar',
                'nama' => $find('nama'),
                'jml' => $find('jml'),
                'ket' => $find('ket'),
                'cond' => [
                    'baik' => array_search('b', $sub, true) ?: null,
                    'rs' => array_search('rs', $sub, true) ?: null,
                    'rb' => array_search('rb', $sub, true) ?: null,
                ],
            ];
        }

        return [
            'style' => 'b2',
            'induk' => $find('induk'),
            'nama' => $find('nama'),
            'baik' => $find('baik'),
            'rusak' => $find('rusak'),
            'ket' => $find('ket'),
            'ruang' => $find('ruang'),
            'merk' => $find('merk'),
            'ukuran' => $find('ukuran'),
            'pabrik' => $find('pabrik'),
        ];
    }

    private function matchCell(string $cell, string $needle): bool
    {
        if ($cell === $needle) {
            return true;
        }
        return match ($needle) {
            'induk' => $cell === 'no. induk',
            'nama' => $cell === 'nama barang' || $cell === 'nama alat',
            'ket' => str_starts_with($cell, 'ket'),
            'ruang' => $cell === 'ruang' || $cell === 'lab',
            default => false,
        };
    }

    private function sheetSudahAda(array $sheet, int $rowHeader, array $c): bool
    {
        $indukTotal = 0;
        $sudah = 0;
        foreach ($sheet as $i => $row) {
            if ($i <= $rowHeader + 1) {
                continue;
            }
            $v = trim((string) ($row[$c['induk']] ?? ''));
            // Lewati kode rentang/list (mis. "0204552820 - 0204554720") atau kosong.
            if ($v === '' || str_contains($v, '-') || str_contains($v, ',') || str_contains($v, ';')) {
                continue;
            }
            $indukTotal++;
            if (Barang::where('kode_barang', $v)->exists()) {
                $sudah++;
            }
        }
        return $indukTotal > 0 && $sudah === $indukTotal;
    }

    private function ruanganName(string $sheetTitle, array $sheet, int $rowHeader, string $style): string
    {
        if ($style === 'dasar') {
            for ($i = 0; $i < $rowHeader; $i++) {
                $cells = $sheet[$i];
                foreach ($cells as $j => $cell) {
                    if ($j + 1 < count($cells)
                        && strtolower(trim((string) $cell)) === 'ruang'
                        && trim((string) ($cells[$j + 1] ?? '')) !== '') {
                        return trim(ltrim(trim((string) $cells[$j + 1]), ':'));
                    }
                }
            }
        }
        return trim($sheetTitle);
    }

    private function parseStandard(array $row, array $c): ?array
    {
        $v = fn ($i) => $i === null ? '' : trim((string) ($row[$i] ?? ''));
        $nama = $v($c['nama']);
        if ($nama === '') {
            return null;
        }

        $qBaik = $this->countVal($v($c['baik']));
        $qRusak = $this->countVal($v($c['rusak']));
        if ($qBaik === null && $qRusak === null) {
            $qBaik = 1;
            $qRusak = 0;
        }
        $qBaik ??= 0;
        $qRusak ??= 0;

        $note = array_values(array_filter([
            $v($c['merk'] ?? null),
            $v($c['ukuran'] ?? null),
            $v($c['pabrik'] ?? null),
        ]));
        $keterangan = implode(', ', $note);
        if ($ketText = $v($c['ket'] ?? null)) {
            $keterangan = trim($keterangan . ' — ' . $ketText);
        }

        // Baris yang mengandung kode "Rusak (berat)" dari kolom Keterangan dianggap rusak saja.
        $total = $qBaik + $qRusak;
        $kodes = $this->kode($v($c['induk'] ?? null), $total);
        $ruangOverride = trim($v($c['ruang'] ?? null)) ?: null;

        $items = [];
        for ($i = 0; $i < $total; $i++) {
            $items[] = [
                'kode' => $kodes[$i],
                'nama' => $nama,
                'kondisi' => $i < $qBaik ? 'Baik' : 'Rusak Ringan',
                'keterangan' => $keterangan . ($ruangOverride ? " — Lab {$ruangOverride}" : ''),
                'ruangan' => $ruangOverride,
            ];
        }
        return $items;
    }

    private function parseDasar(array $row, array $c): ?array
    {
        $v = fn ($i) => $i === null ? '' : trim((string) ($row[$i] ?? ''));
        $nama = $v($c['nama']);
        // Buang baris legenda/keterangan di bawah tabel (mis. "B = Baik", "RS* = kondisi 50-70%").
        if ($nama === '' || str_contains($nama, '=') || str_contains($nama, '%') || strtolower($nama) === 'ket.') {
            return null;
        }

        $jml = $this->countVal($v($c['jml'])) ?? 0;
        $qBaik = $this->countVal($v($c['cond']['baik'])) ?? 0;
        $qRs = $this->countVal($v($c['cond']['rs'])) ?? 0;
        $qRb = $this->countVal($v($c['cond']['rb'])) ?? 0;

        if ($qBaik + $qRs + $qRb === 0) {
            $qBaik = max($jml, 1);
        } elseif ($jml > $qBaik + $qRs + $qRb) {
            $qBaik += $jml - ($qBaik + $qRs + $qRb);
        }

        $keterangan = $v($c['ket'] ?? null);
        $kodes = $this->kode('', $qBaik + $qRs + $qRb);

        $items = [];
        $k = 0;
        foreach (['Baik' => $qBaik, 'Rusak Ringan' => $qRs, 'Rusak Berat' => $qRb] as $kondisi => $n) {
            for ($i = 0; $i < $n; $i++) {
                $items[] = [
                    'kode' => $kodes[$k++],
                    'nama' => $nama,
                    'kondisi' => $kondisi,
                    'keterangan' => $keterangan,
                    'ruangan' => null,
                ];
            }
        }
        return $items;
    }

    private function countVal(string $cell): ?int
    {
        $s = strtolower(trim($cell));
        if ($s === '') {
            return null;
        }
        if (is_numeric($s)) {
            return (int) round((float) $s);
        }
        if (str_contains($s, '√') || $s === 'v' || $s === 'x' || $s === 'baik' || $s === 'rusak') {
            return 1;
        }
        return null;
    }

    private function kode(string $induk, int $qty): array
    {
        $explicit = [];
        foreach (preg_split('/[,;|]/', $induk) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $part, $m)) {
                $explicit[] = $m[1];
            } else {
                $explicit[] = $part;
            }
        }

        $result = [];
        for ($i = 0; $i < $qty; $i++) {
            if ($explicit !== []) {
                $base = $explicit[min($i, count($explicit) - 1)];
                if ($i >= count($explicit)) {
                    $base = preg_match('/^\d+$/', $base)
                        ? str_pad((string) ((int) $base + $i - count($explicit) + 1), strlen($base), '0', STR_PAD_LEFT)
                        : $base . '-' . ($i + 1);
                }
            } else {
                $base = 'AUTO-' . $this->autoSeq++;
            }
            $result[] = $this->uniqueCode($base);
        }
        return $result;
    }

    private function uniqueCode(string $base): string
    {
        $code = $base;
        $n = 1;
        while (isset($this->usedCodes[$code]) || Barang::where('kode_barang', $code)->exists()) {
            $code = $base . '-' . (++$n);
        }
        $this->usedCodes[$code] = true;
        return $code;
    }
}