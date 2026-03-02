<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Izin #{{ $izin->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: "Courier New", Courier, monospace;
            background: #fff;
            color: #111;
        }
        .ticket {
            width: 80mm;
            margin: 0 auto;
            padding: 8px;
            font-size: 12px;
            line-height: 1.4;
        }
        .center { text-align: center; }
        .title { font-weight: 700; font-size: 14px; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .row { margin-bottom: 4px; }
        .label { font-weight: 700; }
        .small { font-size: 11px; }
        .mt { margin-top: 10px; }
        @media print {
            @page { size: 80mm auto; margin: 2mm; }
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="center title">KARTU DISPENSASI SISWA</div>
        <div class="center">SMK TI Airlangga Samarinda</div>
        <div class="center">E-Izin Sekolah</div>
        <div class="divider"></div>

        <div class="row"><span class="label">No:</span> IZ-{{ str_pad((string)$izin->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div class="row"><span class="label">Nama:</span> {{ $izin->nama ?? $izin->user?->name ?? '-' }}</div>
        <div class="row"><span class="label">Kelas:</span> {{ $izin->kelas ?? '-' }}</div>
        <div class="row"><span class="label">Jenis:</span> {{ ucfirst($izin->jenis_izin) }}</div>
        <div class="row"><span class="label">Waktu Izin:</span> {{ $izin->waktu_izin ? \Carbon\Carbon::parse($izin->waktu_izin)->format('d/m/Y H:i') : '-' }}</div>
        <div class="row"><span class="label">Status:</span> DISETUJUI</div>

        <div class="divider"></div>
        <div class="row"><span class="label">Alasan:</span></div>
        <div class="small">{{ $izin->alasan_izin ?? $izin->keterangan }}</div>
        <div class="divider"></div>

        <div class="small">Disetujui guru pada: {{ $izin->updated_at?->format('d/m/Y H:i') }}</div>
        <div class="small">Nama Guru Validator: {{ $izin->nama_guru_validator ?? '-' }}</div>
        <div class="small">Paraf Siswa: {{ $izin->paraf_siswa ? 'YA' : 'TIDAK' }}</div>
        <div class="small">Paraf Guru: {{ $izin->paraf_guru ? 'YA' : 'TIDAK' }}</div>

        <div class="mt center small">Tunjukkan bukti ini ke petugas keamanan</div>
        <div class="center small">--- Sah sebagai kartu dispen ---</div>
    </div>

    <div class="no-print center mt">
        <button onclick="window.print()">Print Sekarang</button>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
