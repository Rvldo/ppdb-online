<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'kuota',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'kuota' => 'integer',
    ];

    public function pendaftar(): HasMany
    {
        return $this->hasMany(Pendaftar::class);
    }

    public function getSisaKuotaAttribute(): int
    {
        $diterima = $this->pendaftar()->where('status', 'diterima')->count();
        return max(0, $this->kuota - $diterima);
    }

    public function getJumlahDiterimaAttribute(): int
    {
        return $this->pendaftar()->where('status', 'diterima')->count();
    }
}
