<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SPM Kebakaran Tahun {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3, .header h4 { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #999; }
        th, td { padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h4>KEMENTERIAN DALAM NEGERI REPUBLIK INDONESIA</h4>
        <h4>DIREKTORAT JENDERAL BINA ADMINISTRASI KEWILAYAHAN</h4>
        <h3>LAPORAN PENYELENGGARAAN SPM SUB URUSAN KEBAKARAN</h3>
        <p style="margin: 5px 0; font-size: 10pt;">Tahun Anggaran: {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Provinsi</th>
                <th>Kabupaten/Kota</th>
                <th class="text-center" width="10%">Jml Pos</th>
                <th class="text-center" width="10%">Total SDM</th>
                <th class="text-center" width="12%">Nilai Akhir</th>
                <th class="text-center" width="15%">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row->regency->province->name ?? '-' }}</td>
                <td>{{ $row->regency->name ?? '-' }}</td>
                <td class="text-center">{{ $row->jml_pos }}</td>
                <td class="text-center">{{ $row->total_sdm }}</td>
                <td class="text-center">{{ $row->nilai_akhir }}</td>
                <td class="text-center">{{ $row->kategori }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
