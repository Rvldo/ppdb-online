<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pendaftar: Object,
    berkas_url: String,
});

const form = useForm({
    status: props.pendaftar.status,
    catatan_admin: props.pendaftar.catatan_admin || '',
});

function update() {
    form.put(route('admin.pendaftar.update', props.pendaftar.id), { preserveScroll: true });
}

function destroy() {
    if (confirm(`Hapus pendaftar "${props.pendaftar.nama_lengkap}"? Tindakan ini tidak dapat dibatalkan.`)) {
        router.delete(route('admin.pendaftar.destroy', props.pendaftar.id));
    }
}

function fmtDate(d) {
    return d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';
}
</script>

<template>
    <AdminLayout>
        <template #header>Detail Pendaftar</template>
        <Head :title="pendaftar.nama_lengkap" />

        <div class="mb-4">
            <Link :href="route('admin.pendaftar.index')" class="text-sm text-indigo-600">← Kembali ke daftar</Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-xs text-slate-500">{{ pendaftar.no_pendaftaran }}</div>
                            <h2 class="text-xl font-bold">{{ pendaftar.nama_lengkap }}</h2>
                        </div>
                        <button @click="destroy" class="text-rose-600 text-sm hover:underline">Hapus</button>
                    </div>

                    <h3 class="font-semibold mt-4 mb-2">Data Pribadi</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div><dt class="text-slate-500">NISN</dt><dd>{{ pendaftar.nisn || '-' }}</dd></div>
                        <div><dt class="text-slate-500">NIK</dt><dd>{{ pendaftar.nik || '-' }}</dd></div>
                        <div><dt class="text-slate-500">Jenis Kelamin</dt><dd>{{ pendaftar.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                        <div><dt class="text-slate-500">Agama</dt><dd>{{ pendaftar.agama }}</dd></div>
                        <div><dt class="text-slate-500">Tempat Lahir</dt><dd>{{ pendaftar.tempat_lahir }}</dd></div>
                        <div><dt class="text-slate-500">Tanggal Lahir</dt><dd>{{ fmtDate(pendaftar.tanggal_lahir) }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd>{{ pendaftar.alamat }}</dd></div>
                        <div><dt class="text-slate-500">No. HP</dt><dd>{{ pendaftar.no_hp }}</dd></div>
                        <div><dt class="text-slate-500">Email</dt><dd>{{ pendaftar.email || '-' }}</dd></div>
                    </dl>

                    <h3 class="font-semibold mt-6 mb-2">Data Akademik</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div><dt class="text-slate-500">Asal Sekolah</dt><dd>{{ pendaftar.asal_sekolah }}</dd></div>
                        <div><dt class="text-slate-500">Tahun Lulus</dt><dd>{{ pendaftar.tahun_lulus }}</dd></div>
                        <div><dt class="text-slate-500">Nilai Rata-rata</dt><dd>{{ pendaftar.nilai_rata_rata || '-' }}</dd></div>
                        <div><dt class="text-slate-500">Jurusan Pilihan</dt><dd class="font-semibold">{{ pendaftar.jurusan?.nama }}</dd></div>
                    </dl>

                    <h3 class="font-semibold mt-6 mb-2">Data Orang Tua</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div><dt class="text-slate-500">Nama Ayah</dt><dd>{{ pendaftar.nama_ayah }}</dd></div>
                        <div><dt class="text-slate-500">Nama Ibu</dt><dd>{{ pendaftar.nama_ibu }}</dd></div>
                        <div><dt class="text-slate-500">Pekerjaan Ayah</dt><dd>{{ pendaftar.pekerjaan_ayah || '-' }}</dd></div>
                        <div><dt class="text-slate-500">Pekerjaan Ibu</dt><dd>{{ pendaftar.pekerjaan_ibu || '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">No. HP Orang Tua</dt><dd>{{ pendaftar.no_hp_ortu }}</dd></div>
                    </dl>

                    <div v-if="berkas_url" class="mt-6">
                        <h3 class="font-semibold mb-2">Berkas Pendukung</h3>
                        <a :href="berkas_url" target="_blank" class="btn-secondary text-sm">Buka Berkas ↗</a>
                    </div>
                </div>
            </div>

            <div>
                <form @submit.prevent="update" class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="font-semibold mb-4">Verifikasi</h3>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="pending">Menunggu Verifikasi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin</label>
                        <textarea v-model="form.catatan_admin" rows="4" class="form-input" placeholder="Catatan ditampilkan ke pendaftar..."></textarea>
                    </div>
                    <button type="submit" :disabled="form.processing" class="btn-primary w-full">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                    <div class="mt-4 text-xs text-slate-500">
                        Daftar pada: {{ fmtDate(pendaftar.created_at) }}
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
