<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftar';

    protected $fillable = [
        'no_pendaftaran',
        'nama_lengkap',
        'nisn',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'no_hp',
        'email',
        'asal_sekolah',
        'tahun_lulus',
        'nilai_rata_rata',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'no_hp_ortu',
        'jurusan_id',
        'status',
        'catatan_admin',
        'berkas_path',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'nilai_rata_rata' => 'decimal:2',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public static function generateNoPendaftaran(): string
    {
        $prefix = 'PPDB-' . date('Y');
        $last = self::where('no_pendaftaran', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('no_pendaftaran');

        $seq = 1;
        if ($last) {
            $seq = (int) substr($last, -5) + 1;
        }

        return $prefix . '-' . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
