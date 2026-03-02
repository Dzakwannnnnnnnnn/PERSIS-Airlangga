<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'kelas',
        'waktu_izin',
        'jenis_izin',
        'alasan_izin',
        'keterangan',
        'bukti_foto',
        'paraf_siswa',
        'paraf_guru',
        'nama_guru_validator',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
