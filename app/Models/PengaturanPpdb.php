<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanPpdb extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_ppdb';

    protected $fillable = [
        'nama_sekolah',
        'logo_path',
        'favicon_path',
        'singkatan_sekolah',
        'alamat_sekolah',
        'website_sekolah',
        'email_sekolah',
        'tahun_ajaran',
        'tanggal_buka',
        'tanggal_tutup',
        'pendaftaran_dibuka',
        'pengumuman',
        'syarat_pendaftaran',
        'contact_person',
        'hasil_diumumkan',
        'tanggal_pengumuman',
        'warna_primary',
        'warna_secondary',
        'warna_accent',
        'hero_bg_path',
        'hero_title',
        'hero_subtitle',
        'footer_text',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
        'pendaftaran_dibuka' => 'boolean',
        'hasil_diumumkan' => 'boolean',
        'tanggal_pengumuman' => 'date',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'nama_sekolah' => 'SMK Negeri 1',
                'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
                'tanggal_buka' => now()->startOfMonth(),
                'tanggal_tutup' => now()->addMonths(2),
                'pendaftaran_dibuka' => true,
                'warna_primary' => '#7c3aed',
                'warna_secondary' => '#4f46e5',
                'warna_accent' => '#d946ef',
            ]
        );
    }

    public function isOpen(): bool
    {
        if (! $this->pendaftaran_dibuka) {
            return false;
        }
        $today = now()->startOfDay();
        return $today->between($this->tanggal_buka, $this->tanggal_tutup);
    }

    /**
     * Get branding data for frontend.
     */
    public function getBrandingAttribute(): array
    {
        return [
            'nama_sekolah' => $this->nama_sekolah,
            'singkatan' => $this->singkatan_sekolah ?: mb_substr($this->nama_sekolah, 0, 1),
            'logo_url' => $this->logo_path ? asset('storage/' . $this->logo_path) : null,
            'favicon_url' => $this->favicon_path ? asset('storage/' . $this->favicon_path) : null,
            'alamat' => $this->alamat_sekolah,
            'website' => $this->website_sekolah,
            'email' => $this->email_sekolah,
            'warna_primary' => $this->warna_primary ?: '#7c3aed',
            'warna_secondary' => $this->warna_secondary ?: '#4f46e5',
            'warna_accent' => $this->warna_accent ?: '#d946ef',
            'hero_bg_url' => $this->hero_bg_path ? asset('storage/' . $this->hero_bg_path) : null,
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'footer_text' => $this->footer_text,
        ];
    }
}
