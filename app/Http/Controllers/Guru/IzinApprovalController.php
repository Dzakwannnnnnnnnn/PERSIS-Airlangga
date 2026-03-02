<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class IzinApprovalController extends Controller
{
    private const PDF_PAGE_WIDTH = 227.00; // ~80mm in points
    private const PDF_MARGIN_X = 10.00;
    private const PDF_TOP_PADDING = 16.00;
    private const PDF_BOTTOM_PADDING = 18.00;
    private const PDF_LINE_HEIGHT = 13.00;
    private const PDF_TEXT_SIZE = 9.00;
    private const PDF_TITLE_SIZE = 10.50;
    private const PDF_CHAR_WIDTH = 5.20; // Courier average width at text size

    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'in:pending,diterima,ditolak'],
            'periode' => ['nullable', 'in:mingguan,bulanan,tahunan'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:latest,oldest'],
        ]);

        $sort = $request->get('sort', 'latest');
        $kelasOptions = Izin::query()
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $izins = $this->buildFilteredQuery($request)
            ->with('user')
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('guru.izin-index', compact('izins', 'sort', 'kelasOptions'));
    }

    public function show(Izin $izin): View
    {
        $izin->load('user');

        return view('guru.izin-show', compact('izin'));
    }

    public function update(Request $request, Izin $izin): RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:terima,tolak'],
        ]);

        if ($request->aksi === 'terima') {
            $request->validate([
                'paraf_guru' => ['required', 'accepted'],
                'nama_guru_validator' => ['required', 'string', 'max:255'],
            ]);

            $namaGuruValidator = trim((string) $request->nama_guru_validator);

            $izin->update([
                'status' => 'diterima',
                'paraf_guru' => true,
                'nama_guru_validator' => $namaGuruValidator,
            ]);

            return back()->with('success', 'Pengajuan izin diterima.');
        }

        $izin->update([
            'status' => 'ditolak',
            'paraf_guru' => false,
            'nama_guru_validator' => null,
        ]);

        return back()->with('success', 'Pengajuan izin ditolak.');
    }

    public function downloadPdf(Izin $izin): Response|RedirectResponse
    {
        if ($izin->status !== 'diterima') {
            return redirect()
                ->route('guru.izin.show', $izin->id)
                ->withErrors(['status' => 'PDF e-kartu dispen hanya bisa diunduh untuk pengajuan yang sudah diterima.']);
        }

        $pdfBinary = $this->buildThermalPdf($izin);
        $filename = 'e-kartu-dispen-IZ-' . str_pad((string) $izin->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => (string) strlen($pdfBinary),
        ]);
    }

    public function export(Request $request): Response
    {
        $request->validate([
            'format' => ['required', 'in:excel,pdf'],
            'q' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'in:pending,diterima,ditolak'],
            'periode' => ['nullable', 'in:mingguan,bulanan,tahunan'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:latest,oldest'],
        ]);

        $sort = $request->get('sort', 'latest');
        $izins = $this->buildFilteredQuery($request)
            ->with('user')
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->get();

        if ($request->format === 'excel') {
            return $this->exportExcelTable($izins, $request);
        }

        return $this->exportRekapPdf($izins, $request);
    }

    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Izin::query()
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function (Builder $q) use ($term) {
                    $q->where('nama', 'like', "%{$term}%")
                        ->orWhere('kelas', 'like', "%{$term}%")
                        ->orWhere('jenis_izin', 'like', "%{$term}%")
                        ->orWhereHas('user', function (Builder $uq) use ($term) {
                            $uq->where('name', 'like', "%{$term}%");
                        });
                });
            })
            ->when($request->filled('kelas'), function (Builder $query) use ($request) {
                $query->where('kelas', $request->kelas);
            })
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('tanggal_dari'), function (Builder $query) use ($request) {
                $query->whereDate('created_at', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function (Builder $query) use ($request) {
                $query->whereDate('created_at', '<=', $request->tanggal_sampai);
            });

        $periode = (string) $request->get('periode', '');
        if ($periode === 'mingguan') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulanan') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($periode === 'tahunan') {
            $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
        }

        return $query;
    }

    private function exportExcelTable($izins, Request $request): Response
    {
        $filename = 'rekap-pengajuan-guru-' . now()->format('Ymd-His') . '.xls';
        $periodeLabel = $this->formatPeriodeLabel((string) $request->get('periode', ''));

        $html = '<html><head><meta charset="UTF-8"><style>'
            . 'body{font-family:Arial,sans-serif;font-size:12px;color:#111;}'
            . 'h2{margin:0 0 8px 0;font-size:16px;}'
            . '.meta{margin:0 0 10px 0;font-size:12px;}'
            . 'table{border-collapse:collapse;width:100%;}'
            . 'th,td{border:1px solid #222;padding:6px;vertical-align:top;}'
            . 'th{background:#e9ecef;text-align:left;font-weight:700;}'
            . '</style></head><body>';

        $html .= '<h2>Rekap Pengajuan Izin Siswa</h2>';
        $html .= '<div class="meta">'
            . 'Dicetak: ' . e(now()->format('d/m/Y H:i')) . '<br>'
            . 'Filter Kelas: ' . e((string) ($request->kelas ?: 'Semua')) . '<br>'
            . 'Filter Nama/Kata Kunci: ' . e((string) ($request->q ?: 'Semua')) . '<br>'
            . 'Filter Periode: ' . e($periodeLabel)
            . '</div>';

        $html .= '<table><thead><tr>'
            . '<th>No</th>'
            . '<th>Nama Siswa</th>'
            . '<th>Kelas</th>'
            . '<th>Jenis Izin</th>'
            . '<th>Alasan</th>'
            . '<th>Status</th>'
            . '<th>Waktu Pengajuan</th>'
            . '<th>Waktu Izin</th>'
            . '<th>Guru Validator</th>'
            . '</tr></thead><tbody>';

        foreach ($izins as $index => $izin) {
            $html .= '<tr>'
                . '<td>' . e((string) ($index + 1)) . '</td>'
                . '<td>' . e((string) ($izin->nama ?? $izin->user?->name ?? '-')) . '</td>'
                . '<td>' . e((string) ($izin->kelas ?? '-')) . '</td>'
                . '<td>' . e(ucfirst((string) $izin->jenis_izin)) . '</td>'
                . '<td>' . e((string) preg_replace('/\s+/', ' ', (string) ($izin->alasan_izin ?? $izin->keterangan ?? '-'))) . '</td>'
                . '<td>' . e(strtoupper((string) $izin->status)) . '</td>'
                . '<td>' . e((string) ($izin->created_at?->format('d/m/Y H:i') ?? '-')) . '</td>'
                . '<td>' . e((string) ($izin->waktu_izin ? \Carbon\Carbon::parse($izin->waktu_izin)->format('d/m/Y H:i') : '-')) . '</td>'
                . '<td>' . e((string) ($izin->nama_guru_validator ?? '-')) . '</td>'
                . '</tr>';
        }

        if ($izins->isEmpty()) {
            $html .= '<tr><td colspan="9">Tidak ada data untuk filter yang dipilih.</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportRekapPdf($izins, Request $request): Response
    {
        $periodeLabel = $this->formatPeriodeLabel((string) $request->get('periode', ''));
        $pdfBinary = $this->buildTablePdf($izins, $request, $periodeLabel);
        $filename = 'rekap-pengajuan-guru-' . now()->format('Ymd-His') . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => (string) strlen($pdfBinary),
        ]);
    }

    private function buildTablePdf($izins, Request $request, string $periodeLabel): string
    {
        $pageWidth = 842.00; // A4 landscape width in points
        $marginX = 22.00;
        $topPadding = 26.00;
        $bottomPadding = 24.00;
        $rowHeight = 18.00;
        $headerHeight = 20.00;
        $fontSize = 8.50;

        $columns = [
            ['label' => 'No', 'width' => 28.00],
            ['label' => 'Nama', 'width' => 130.00],
            ['label' => 'Kelas', 'width' => 70.00],
            ['label' => 'Jenis Izin', 'width' => 78.00],
            ['label' => 'Status', 'width' => 62.00],
            ['label' => 'Waktu Pengajuan', 'width' => 105.00],
            ['label' => 'Waktu Izin', 'width' => 95.00],
            ['label' => 'Validator', 'width' => 95.00],
            ['label' => 'Alasan', 'width' => 135.00],
        ];

        $metaLines = [
            'REKAP PENGAJUAN IZIN SISWA',
            'Dicetak: ' . now()->format('d/m/Y H:i'),
            'Filter Kelas: ' . ($request->kelas ?: 'Semua'),
            'Filter Nama/Kata Kunci: ' . ($request->q ?: 'Semua'),
            'Filter Periode: ' . $periodeLabel,
        ];

        $metaHeight = 70.00;
        $tableHeight = $headerHeight + (max(1, $izins->count()) * $rowHeight);
        $pageHeight = max(595.00, $topPadding + $metaHeight + $tableHeight + $bottomPadding);

        $startX = $marginX;
        $metaY = $pageHeight - $topPadding;
        $tableTopY = $metaY - $metaHeight;
        $tableWidth = array_sum(array_column($columns, 'width'));

        $commands = [];

        foreach ($metaLines as $index => $line) {
            $size = $index === 0 ? 12.00 : 9.00;
            $y = $metaY - ($index * 14);
            $commands[] = 'BT';
            $commands[] = '/F1 ' . number_format($size, 2, '.', '') . ' Tf';
            $commands[] = '1 0 0 1 ' . number_format($startX, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . ' Tm';
            $commands[] = '(' . $this->pdfTextSafe($line) . ') Tj';
            $commands[] = 'ET';
        }

        // Header background
        $commands[] = '0.93 0.93 0.93 rg';
        $commands[] = number_format($startX, 2, '.', '') . ' ' . number_format($tableTopY - $headerHeight, 2, '.', '') . ' ' . number_format($tableWidth, 2, '.', '') . ' ' . number_format($headerHeight, 2, '.', '') . ' re f';
        $commands[] = '0 0 0 rg';

        // Outer border + horizontal lines
        $commands[] = '0 0 0 RG';
        $commands[] = '0.7 w';
        $totalRows = max(1, $izins->count());
        $tableBottomY = $tableTopY - $headerHeight - ($totalRows * $rowHeight);
        $commands[] = number_format($startX, 2, '.', '') . ' ' . number_format($tableBottomY, 2, '.', '') . ' ' . number_format($tableWidth, 2, '.', '') . ' ' . number_format($tableTopY - $tableBottomY, 2, '.', '') . ' re S';

        for ($r = 0; $r <= $totalRows; $r++) {
            $yLine = $tableTopY - $headerHeight - ($r * $rowHeight);
            $commands[] = number_format($startX, 2, '.', '') . ' ' . number_format($yLine, 2, '.', '') . ' m '
                . number_format($startX + $tableWidth, 2, '.', '') . ' ' . number_format($yLine, 2, '.', '') . ' l S';
        }

        // Vertical lines
        $xCursor = $startX;
        foreach ($columns as $col) {
            $xCursor += $col['width'];
            $commands[] = number_format($xCursor, 2, '.', '') . ' ' . number_format($tableBottomY, 2, '.', '') . ' m '
                . number_format($xCursor, 2, '.', '') . ' ' . number_format($tableTopY, 2, '.', '') . ' l S';
        }

        // Header text
        $xCursor = $startX;
        foreach ($columns as $col) {
            $commands[] = 'BT';
            $commands[] = '/F1 8.50 Tf';
            $commands[] = '1 0 0 1 ' . number_format($xCursor + 3, 2, '.', '') . ' ' . number_format($tableTopY - 13, 2, '.', '') . ' Tm';
            $commands[] = '(' . $this->pdfTextSafe($col['label']) . ') Tj';
            $commands[] = 'ET';
            $xCursor += $col['width'];
        }

        // Body rows
        $rows = [];
        foreach ($izins as $index => $izin) {
            $rows[] = [
                (string) ($index + 1),
                (string) ($izin->nama ?? $izin->user?->name ?? '-'),
                (string) ($izin->kelas ?? '-'),
                ucfirst((string) $izin->jenis_izin),
                strtoupper((string) $izin->status),
                (string) ($izin->created_at?->format('d/m/Y H:i') ?? '-'),
                (string) ($izin->waktu_izin ? \Carbon\Carbon::parse($izin->waktu_izin)->format('d/m/Y H:i') : '-'),
                (string) ($izin->nama_guru_validator ?? '-'),
                (string) preg_replace('/\s+/', ' ', (string) ($izin->alasan_izin ?? $izin->keterangan ?? '-')),
            ];
        }

        if (empty($rows)) {
            $rows[] = ['-', 'Tidak ada data untuk filter yang dipilih.', '-', '-', '-', '-', '-', '-', '-'];
        }

        foreach ($rows as $rowIndex => $row) {
            $xCursor = $startX;
            $textY = $tableTopY - $headerHeight - ($rowIndex * $rowHeight) - 12;
            foreach ($row as $cellIndex => $cell) {
                $cellText = $this->truncate($cell, (int) floor(($columns[$cellIndex]['width'] - 6) / 5));
                $commands[] = 'BT';
                $commands[] = '/F1 ' . number_format($fontSize, 2, '.', '') . ' Tf';
                $commands[] = '1 0 0 1 ' . number_format($xCursor + 3, 2, '.', '') . ' ' . number_format($textY, 2, '.', '') . ' Tm';
                $commands[] = '(' . $this->pdfTextSafe($cellText) . ') Tj';
                $commands[] = 'ET';
                $xCursor += $columns[$cellIndex]['width'];
            }
        }

        $contentStream = implode("\n", $commands) . "\n";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " . number_format($pageWidth, 2, '.', '') . ' ' . number_format($pageHeight, 2, '.', '') . "] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Length " . strlen($contentStream) . " >>\nstream\n{$contentStream}endstream\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function truncate(string $value, int $max): string
    {
        if (strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, max(0, $max - 1)) . '.';
    }

    private function formatPeriodeLabel(string $periode): string
    {
        return match ($periode) {
            'mingguan' => 'Minggu Ini',
            'bulanan' => 'Bulan Ini',
            'tahunan' => 'Tahun Ini',
            default => 'Semua',
        };
    }

    private function buildThermalPdf(Izin $izin): string
    {
        $ticketNo = 'IZ-' . str_pad((string) $izin->id, 5, '0', STR_PAD_LEFT);
        $nama = (string) ($izin->nama ?? $izin->user?->name ?? '-');
        $kelas = (string) ($izin->kelas ?? '-');
        $jenis = ucfirst((string) $izin->jenis_izin);
        $waktuIzin = $izin->waktu_izin ? \Carbon\Carbon::parse($izin->waktu_izin)->format('d/m/Y H:i') : '-';
        $alasan = (string) ($izin->alasan_izin ?? $izin->keterangan ?? '-');
        $namaGuru = (string) ($izin->nama_guru_validator ?? '-');
        $waktuSetuju = (string) ($izin->updated_at?->format('d/m/Y H:i') ?? '-');

        $contentWidth = self::PDF_PAGE_WIDTH - (self::PDF_MARGIN_X * 2);
        $maxChars = max(20, (int) floor($contentWidth / self::PDF_CHAR_WIDTH));
        $divider = str_repeat('-', $maxChars);

        $rows = [];
        $rows[] = ['text' => 'KARTU DISPENSASI SISWA', 'align' => 'center', 'size' => self::PDF_TITLE_SIZE];
        $rows[] = ['text' => 'SMK TI Airlangga Samarinda', 'align' => 'center', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'E-Izin Sekolah', 'align' => 'center', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => $divider, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'No: ' . $ticketNo, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Nama: ' . $nama, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Kelas: ' . $kelas, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Jenis: ' . $jenis, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Waktu Izin: ' . $waktuIzin, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Status: DISETUJUI', 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => $divider, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Alasan:', 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        foreach ($this->wrapThermalText($alasan, $maxChars) as $wrapped) {
            $rows[] = ['text' => $wrapped, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        }
        $rows[] = ['text' => $divider, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Disetujui guru pada: ' . $waktuSetuju, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Nama Guru Validator: ' . $namaGuru, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Paraf Siswa: ' . ($izin->paraf_siswa ? 'YA' : 'TIDAK'), 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Paraf Guru: ' . ($izin->paraf_guru ? 'YA' : 'TIDAK'), 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => $divider, 'align' => 'left', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => 'Tunjukkan bukti ini ke petugas keamanan', 'align' => 'center', 'size' => self::PDF_TEXT_SIZE];
        $rows[] = ['text' => '--- Sah sebagai kartu dispen ---', 'align' => 'center', 'size' => self::PDF_TEXT_SIZE];

        $pageHeight = self::PDF_TOP_PADDING + self::PDF_BOTTOM_PADDING + (count($rows) * self::PDF_LINE_HEIGHT);
        $y = $pageHeight - self::PDF_TOP_PADDING;
        $commands = [];

        foreach ($rows as $row) {
            $safe = $this->pdfTextSafe((string) $row['text']);
            $fontSize = (float) $row['size'];
            $textWidth = strlen((string) $row['text']) * self::PDF_CHAR_WIDTH;
            $x = self::PDF_MARGIN_X;

            if ($row['align'] === 'center') {
                $x = max(self::PDF_MARGIN_X, (self::PDF_PAGE_WIDTH - $textWidth) / 2);
            }

            $commands[] = 'BT';
            $commands[] = '/F1 ' . number_format($fontSize, 2, '.', '') . ' Tf';
            $commands[] = '1 0 0 1 ' . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . ' Tm';
            $commands[] = '(' . $safe . ') Tj';
            $commands[] = 'ET';

            $y -= self::PDF_LINE_HEIGHT;
        }

        $contentStream = implode("\n", $commands) . "\n";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " . number_format(self::PDF_PAGE_WIDTH, 2, '.', '') . ' ' . number_format($pageHeight, 2, '.', '') . "] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Length " . strlen($contentStream) . " >>\nstream\n{$contentStream}endstream\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function wrapThermalText(string $text, int $width): array
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        if ($normalized === '') {
            return ['-'];
        }

        $wrapped = wordwrap($normalized, $width, "\n", true);

        return explode("\n", $wrapped);
    }

    private function pdfTextSafe(string $value): string
    {
        $ascii = preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';

        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\\(', '\\)'],
            $ascii
        );
    }
}
