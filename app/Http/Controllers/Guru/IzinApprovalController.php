<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Izin;
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
            'status' => ['nullable', 'in:pending,diterima,ditolak'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:latest,oldest'],
        ]);

        $sort = $request->get('sort', 'latest');

        $izins = Izin::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($q) use ($term) {
                    $q->where('nama', 'like', "%{$term}%")
                        ->orWhere('kelas', 'like', "%{$term}%")
                        ->orWhere('jenis_izin', 'like', "%{$term}%")
                        ->orWhereHas('user', function ($uq) use ($term) {
                            $uq->where('name', 'like', "%{$term}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('tanggal_dari'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->tanggal_sampai);
            })
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('guru.izin-index', compact('izins', 'sort'));
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
            'paraf_guru' => ['nullable', 'accepted'],
            'nama_guru_validator' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->aksi === 'terima') {
            if (!$request->boolean('paraf_guru')) {
                return back()->withErrors([
                    'paraf_guru' => 'Paraf guru wajib dicentang jika izin diterima.',
                ]);
            }

            $namaGuruValidator = trim((string) $request->nama_guru_validator);
            if ($namaGuruValidator === '') {
                return back()->withInput()->withErrors([
                    'nama_guru_validator' => 'Nama lengkap guru wajib diisi saat paraf validasi.',
                ]);
            }

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
