<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran - {{ $pendaftar->no_pendaftaran }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 24px;
            color: #1e293b;
            background: #f1f5f9;
        }
        .page {
            max-width: 780px;
            margin: 0 auto;
            background: #fff;
            padding: 32px;
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 18px; color: #4338ca; }
        .header p  { margin: 2px 0; font-size: 12px; color: #475569; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-diterima { background: #dcfce7; color: #166534; }
        .badge-ditolak  { background: #fee2e2; color: #991b1b; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 16px;
        }
        .info-grid h2 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #4f46e5;
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row {
            display: grid;
            grid-template-columns: 130px 1fr;
            font-size: 12px;
            padding: 4px 0;
        }
        .info-row .label { color: #64748b; }
        .info-row .value { color: #0f172a; font-weight: 500; }
        .nopendaftaran {
            background: #eef2ff;
            border-left: 4px solid #4f46e5;
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 6px;
        }
        .nopendaftaran .lbl  { font-size: 11px; color: #475569; }
        .nopendaftaran .num  { font-size: 22px; font-weight: 700; color: #4338ca; letter-spacing: 2px; font-family: 'Courier New', monospace; }
        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
            font-size: 11px;
            color: #64748b;
        }
        .signature {
            margin-top: 32px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            font-size: 12px;
        }
        .signature .box { text-align: center; }
        .signature .nm  { margin-top: 60px; padding-top: 4px; border-top: 1px solid #475569; display: inline-block; min-width: 180px; }
        .actions {
            display: flex; gap: 8px; margin-bottom: 16px;
        }
        .actions button, .actions a {
            padding: 8px 16px;
            background: #4f46e5; color: #fff;
            border: none; border-radius: 6px;
            font-size: 12px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        .actions a.secondary { background: #64748b; }
        @media print {
            body  { background: #fff; padding: 0; }
            .page { border: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="actions">
            <button onclick="window.print()">🖨️ Cetak</button>
            <a href="{{ url('/') }}" class="secondary">← Kembali</a>
        </div>

        <div class="header">
            <div>
                <h1>{{ $pengaturan->nama_sekolah }}</h1>
                <p>Bukti Pendaftaran Peserta Didik Baru</p>
                <p>Tahun Ajaran {{ $pengaturan->tahun_ajaran }}</p>
            </div>
            <div style="text-align:right;">
                @php $statusClass = ['pending' => 'badge-pending', 'diterima' => 'badge-diterima', 'ditolak' => 'badge-ditolak'][$pendaftar->status]; @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($pendaftar->status) }}</span>
                <p style="font-size:11px;color:#64748b;margin-top:6px;">{{ $pendaftar->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <div class="nopendaftaran">
            <div class="lbl">Nomor Pendaftaran</div>
            <div class="num">{{ $pendaftar->no_pendaftaran }}</div>
        </div>

        <div class="info-grid">
            <div>
                <h2>Data Pribadi</h2>
                <div class="info-row"><span class="label">Nama Lengkap</span><span class="value">{{ $pendaftar->nama_lengkap }}</span></div>
                <div class="info-row"><span class="label">NISN</span><span class="value">{{ $pendaftar->nisn ?: '-' }}</span></div>
                <div class="info-row"><span class="label">NIK</span><span class="value">{{ $pendaftar->nik ?: '-' }}</span></div>
                <div class="info-row"><span class="label">Jenis Kelamin</span><span class="value">{{ $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                <div class="info-row"><span class="label">Tempat, Tgl Lahir</span><span class="value">{{ $pendaftar->tempat_lahir }}, {{ $pendaftar->tanggal_lahir->format('d M Y') }}</span></div>
                <div class="info-row"><span class="label">Agama</span><span class="value">{{ $pendaftar->agama }}</span></div>
                <div class="info-row"><span class="label">No. HP</span><span class="value">{{ $pendaftar->no_hp }}</span></div>
                <div class="info-row"><span class="label">Email</span><span class="value">{{ $pendaftar->email ?: '-' }}</span></div>
                <div class="info-row"><span class="label">Alamat</span><span class="value">{{ $pendaftar->alamat }}</span></div>
            </div>

            <div>
                <h2>Data Akademik</h2>
                <div class="info-row"><span class="label">Asal Sekolah</span><span class="value">{{ $pendaftar->asal_sekolah }}</span></div>
                <div class="info-row"><span class="label">Tahun Lulus</span><span class="value">{{ $pendaftar->tahun_lulus }}</span></div>
                <div class="info-row"><span class="label">Nilai Rata-rata</span><span class="value">{{ $pendaftar->nilai_rata_rata ?: '-' }}</span></div>
                <div class="info-row"><span class="label">Pilihan Jurusan</span><span class="value">{{ $pendaftar->jurusan->nama }}</span></div>

                <h2 style="margin-top:18px;">Data Orang Tua</h2>
                <div class="info-row"><span class="label">Nama Ayah</span><span class="value">{{ $pendaftar->nama_ayah }}</span></div>
                <div class="info-row"><span class="label">Pekerjaan</span><span class="value">{{ $pendaftar->pekerjaan_ayah ?: '-' }}</span></div>
                <div class="info-row"><span class="label">Nama Ibu</span><span class="value">{{ $pendaftar->nama_ibu }}</span></div>
                <div class="info-row"><span class="label">Pekerjaan</span><span class="value">{{ $pendaftar->pekerjaan_ibu ?: '-' }}</span></div>
                <div class="info-row"><span class="label">No. HP Ortu</span><span class="value">{{ $pendaftar->no_hp_ortu }}</span></div>
            </div>
        </div>

        @if ($pendaftar->catatan_admin)
            <div style="background:#fffbeb;border-left:4px solid #d97706;padding:10px 14px;margin-top:16px;font-size:12px;">
                <strong>Catatan Admin:</strong> {{ $pendaftar->catatan_admin }}
            </div>
        @endif

        <div class="signature">
            <div class="box">
                <div>Pendaftar,</div>
                <div class="nm">{{ $pendaftar->nama_lengkap }}</div>
            </div>
            <div class="box">
                <div>{{ $pengaturan->contact_person ? 'Panitia PPDB,' : 'Panitia PPDB,' }}</div>
                <div class="nm">_______________________</div>
            </div>
        </div>

        <div class="footer">
            Bukti pendaftaran ini sah sebagai tanda terima.
            Untuk informasi lebih lanjut hubungi panitia PPDB
            @if ($pengaturan->contact_person) di {{ $pengaturan->contact_person }} @endif.
            Cetak / simpan halaman ini dan bawa saat verifikasi berkas.
        </div>
    </div>
</body>
</html>
