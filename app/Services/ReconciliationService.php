<?php

namespace App\Services;

class ReconciliationService
{
    private const DAFTAR_WILAYAH = [
        "Kabupaten Aceh Barat" => "Aceh", "Kabupaten Aceh Barat Daya" => "Aceh", "Kabupaten Aceh Besar" => "Aceh",
        "Kabupaten Aceh Jaya" => "Aceh", "Kabupaten Aceh Selatan" => "Aceh", "Kabupaten Aceh Singkil" => "Aceh",
        "Kabupaten Aceh Tamiang" => "Aceh", "Kabupaten Aceh Tengah" => "Aceh", "Kabupaten Aceh Tenggara" => "Aceh",
        "Kabupaten Aceh Timur" => "Aceh", "Kabupaten Aceh Utara" => "Aceh", "Kabupaten Bener Meriah" => "Aceh",
        "Kabupaten Bireuen" => "Aceh", "Kabupaten Gayo Lues" => "Aceh", "Kabupaten Nagan Raya" => "Aceh",
        "Kabupaten Pidie" => "Aceh", "Kabupaten Pidie Jaya" => "Aceh", "Kabupaten Simeulue" => "Aceh",
        "Kota Banda Aceh" => "Aceh", "Kota Langsa" => "Aceh", "Kota Lhokseumawe" => "Aceh", "Kota Sabang" => "Aceh",
        "Kota Subulussalam" => "Aceh", "Kabupaten Badung" => "Bali", "Kabupaten Bangli" => "Bali",
        "Kabupaten Buleleng" => "Bali", "Kabupaten Gianyar" => "Bali", "Kabupaten Jembrana" => "Bali",
        "Kabupaten Karangasem" => "Bali", "Kabupaten Klungkung" => "Bali", "Kabupaten Tabanan" => "Bali",
        "Kota Denpasar" => "Bali", "Kabupaten Lebak" => "Banten", "Kabupaten Pandeglang" => "Banten",
        "Kabupaten Serang" => "Banten", "Kabupaten Tangerang" => "Banten", "Kota Cilegon" => "Banten",
        "Kota Serang" => "Banten", "Kota Tangerang" => "Banten", "Kota Tangerang Selatan" => "Banten",
        "Kabupaten Bengkulu Selatan" => "Bengkulu", "Kabupaten Bengkulu Tengah" => "Bengkulu", "Kabupaten Bengkulu Utara" => "Bengkulu",
        "Kabupaten Kaur" => "Bengkulu", "Kabupaten Kepahiang" => "Bengkulu", "Kabupaten Lebong" => "Bengkulu",
        "Kabupaten Muko Muko" => "Bengkulu", "Kabupaten Rejang Lebong" => "Bengkulu", "Kabupaten Seluma" => "Bengkulu",
        "Kota Bengkulu" => "Bengkulu", "Kabupaten Bantul" => "DI Yogyakarta", "Kabupaten Gunungkidul" => "DI Yogyakarta",
        "Kabupaten Kulon Progo" => "DI Yogyakarta", "Kabupaten Sleman" => "DI Yogyakarta", "Kota Yogyakarta" => "DI Yogyakarta",
        "Kabupaten Kepulauan Seribu" => "DK Jakarta", "Kota Jakarta Barat" => "DK Jakarta", "Kota Jakarta Pusat" => "DK Jakarta",
        "Kota Jakarta Selatan" => "DK Jakarta", "Kota Jakarta Timur" => "DK Jakarta", "Kota Jakarta Utara" => "DK Jakarta",
        "Kota Surabaya" => "Jawa Timur", "Kota Bandung" => "Jawa Barat", "Kota Semarang" => "Jawa Tengah"
    ];

    public static function normalisasiNama($nilai)
    {
        if (is_null($nilai) || $nilai === '') return '';
        if (is_array($nilai)) {
            $nilai = implode(' ', array_filter($nilai));
        }

        $val = trim(strtolower((string) $nilai));
        $val = preg_replace('/\s+/', ' ', $val);
        $val = preg_replace('/^kab\.?\s+/i', 'kabupaten ', $val);

        return trim($val);
    }

    public static function cariProvinsi($row, $namaKabKota)
    {
        // 1. Utamakan membaca langsung dari kolom "Provinsi" yang ada di dalam baris Excel
        if (is_array($row)) {
            foreach ($row as $key => $val) {
                if (stripos((string)$key, 'provinsi') !== false) {
                    $cleanVal = trim(is_array($val) ? implode(' ', $val) : (string)$val);
                    if ($cleanVal !== '' && strtolower($cleanVal) !== 'tidak diketahui') {
                        return $cleanVal;
                    }
                }
            }
        }

        // 2. Fallback ke mapping statis jika kolom provinsi di excel tidak terbaca
        $kunci = self::normalisasiNama($namaKabKota);

        static $map = null;
        if ($map === null) {
            $map = [];
            foreach (self::DAFTAR_WILAYAH as $kab => $prov) {
                $map[self::normalisasiNama($kab)] = $prov;
            }
        }

        return $map[$kunci] ?? "Tidak diketahui";
    }

    private const KATEGORI = [
        [
            "kode" => "A",
            "nama" => "Kapasitas Kelembagaan dan Data Pos",
            "kolom" => ["Bentuk Kelembagaan", "Tipe Kelembagaan", "Jumlah Mako", "Jumlah Pos Sektor", "Jumlah Pos"]
        ],
    ];

    private const KOLOM_DIABAIKAN = ["Provinsi"];

    public static function kelompokkanKolom($daftarKolom, $kolomKunci)
    {
        $sudahDipakai = array_flip(array_merge([$kolomKunci], self::KOLOM_DIABAIKAN));
        $kelompok = [];

        foreach (self::KATEGORI as $kat) {
            $kolomAda = array_values(array_intersect($kat['kolom'], $daftarKolom));
            foreach ($kolomAda as $k) $sudahDipakai[$k] = true;

            if (count($kolomAda) > 0) {
                $kelompok[] = ["kode" => $kat['kode'], "nama" => $kat['nama'], "kolom" => $kolomAda];
            }
        }

        $sisa = array_values(array_filter($daftarKolom, fn($k) => !isset($sudahDipakai[$k])));
        if (count($sisa) > 0) {
            $kelompok[] = ["kode" => "Tambahan", "nama" => "Data Tambahan (evaluasi internal)", "kolom" => $sisa];
        }

        return $kelompok;
    }

    public static function cocokkanData($rowsLama, $rowsBaru, $kolomKunci)
    {
        $petaLama = self::buatPeta($rowsLama, $kolomKunci);
        $petaBaru = self::buatPeta($rowsBaru, $kolomKunci);

        $headersLama = count($rowsLama) > 0 ? array_keys($rowsLama[0]) : [];
        $headersBaru = count($rowsBaru) > 0 ? array_keys($rowsBaru[0]) : [];
        $semuaKolom = self::gabungkanKolom($headersLama, $headersBaru, $kolomKunci);

        $semuaKunci = array_unique(array_merge(array_keys($petaLama['peta']), array_keys($petaBaru['peta'])));

        $hasil = [];
        foreach ($semuaKunci as $kunci) {
            $rowLama = $petaLama['peta'][$kunci] ?? null;
            $rowBaru = $petaBaru['peta'][$kunci] ?? null;

            $barisReferensi = $rowBaru ?? $rowLama;
            $label = (string) ($barisReferensi[$kolomKunci] ?? '');

            // Kirim seluruh baris referensi agar bisa mendeteksi kolom Provinsi di Excel
            $provinsi = self::cariProvinsi($barisReferensi, $label);

            $statusCheck = self::tentukanStatus($rowLama, $rowBaru, $semuaKolom, $kolomKunci);

            $hasil[] = [
                'kunci' => $kunci,
                'label' => $label,
                'provinsi' => $provinsi,
                'rowLama' => $rowLama,
                'rowBaru' => $rowBaru,
                'status' => $statusCheck['status'],
                'diffCount' => $statusCheck['diffCount'],
                'tertaut' => false
            ];
        }

        usort($hasil, function($a, $b) {
            if ($a['provinsi'] !== $b['provinsi']) {
                return strcmp($a['provinsi'], $b['provinsi']);
            }
            return strcmp($a['label'], $b['label']);
        });

        return [
            'regions' => $hasil,
            'allColumns' => $semuaKolom,
            'peringatan' => [
                'dilewatiLama' => $petaLama['dilewati'],
                'dilewatiBaru' => $petaBaru['dilewati']
            ]
        ];
    }

    private static function buatPeta($rows, $kolomKunci)
    {
        $peta = [];
        $dilewati = 0;
        foreach ($rows as $row) {
            $nilaiKunci = $row[$kolomKunci] ?? '';
            $kunci = self::normalisasiNama($nilaiKunci);
            if ($kunci !== '') {
                $peta[$kunci] = $row;
            } else {
                $dilewati++;
            }
        }
        return ['peta' => $peta, 'dilewati' => $dilewati];
    }

    private static function gabungkanKolom($headersLama, $headersBaru, $kolomKunci)
    {
        $kolom = [$kolomKunci];
        foreach (array_merge($headersBaru, $headersLama) as $h) {
            if ($h !== $kolomKunci && !in_array($h, $kolom)) {
                $kolom[] = $h;
            }
        }
        return $kolom;
    }

    private static function tentukanStatus($rowLama, $rowBaru, $semuaKolom, $kolomKunci)
    {
        if ($rowLama && !$rowBaru) return ['status' => 'oldOnly', 'diffCount' => 0];
        if ($rowBaru && !$rowLama) return ['status' => 'newOnly', 'diffCount' => 0];

        $diffCount = 0;
        foreach ($semuaKolom as $kolom) {
            if ($kolom === $kolomKunci) continue;

            $nilaiLama = self::normalisasiNama($rowLama[$kolom] ?? '');
            $nilaiBaru = self::normalisasiNama($rowBaru[$kolom] ?? '');

            if ($nilaiLama !== $nilaiBaru) {
                $diffCount++;
            }
        }

        return [
            'status' => $diffCount > 0 ? 'diffData' : 'sameData',
            'diffCount' => $diffCount
        ];
    }
}
