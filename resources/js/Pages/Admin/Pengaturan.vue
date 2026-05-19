<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pengaturan: Object,
});

const form = useForm({
    nama_sekolah: props.pengaturan.nama_sekolah,
    tahun_ajaran: props.pengaturan.tahun_ajaran,
    tanggal_buka: props.pengaturan.tanggal_buka?.slice(0, 10),
    tanggal_tutup: props.pengaturan.tanggal_tutup?.slice(0, 10),
    pendaftaran_dibuka: !!props.pengaturan.pendaftaran_dibuka,
    pengumuman: props.pengaturan.pengumuman || '',
    syarat_pendaftaran: props.pengaturan.syarat_pendaftaran || '',
    contact_person: props.pengaturan.contact_person || '',
    hasil_diumumkan: !!props.pengaturan.hasil_diumumkan,
    tanggal_pengumuman: props.pengaturan.tanggal_pengumuman?.slice(0, 10) || '',
});

function submit() {
    form.put(route('admin.pengaturan.update'));
}
</script>

<template>
    <AdminLayout>
        <template #header>Pengaturan PPDB</template>
        <Head title="Pengaturan PPDB" />

        <form @submit.prevent="submit" class="max-w-3xl space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Informasi Umum</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Nama Sekolah *</label>
                        <input v-model="form.nama_sekolah" type="text" class="form-input" required />
                        <div v-if="form.errors.nama_sekolah" class="form-error">{{ form.errors.nama_sekolah }}</div>
                    </div>
                    <div>
                        <label class="form-label">Tahun Ajaran *</label>
                        <input v-model="form.tahun_ajaran" type="text" class="form-input" placeholder="2026/2027" required />
                    </div>
                    <div>
                        <label class="form-label">Contact Person</label>
                        <input v-model="form.contact_person" type="text" class="form-input" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Periode Pendaftaran</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Buka *</label>
                        <input v-model="form.tanggal_buka" type="date" class="form-input" required />
                    </div>
                    <div>
                        <label class="form-label">Tanggal Tutup *</label>
                        <input v-model="form.tanggal_tutup" type="date" class="form-input" required />
                        <div v-if="form.errors.tanggal_tutup" class="form-error">{{ form.errors.tanggal_tutup }}</div>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 mt-4">
                    <input v-model="form.pendaftaran_dibuka" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                    Pendaftaran dibuka (toggle global)
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Pengumuman Hasil</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Pengumuman</label>
                        <input v-model="form.tanggal_pengumuman" type="date" class="form-input" />
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 mt-4">
                    <input v-model="form.hasil_diumumkan" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                    Tampilkan hasil di halaman publik (Pengumuman)
                </label>
                <p class="text-xs text-slate-500 mt-2">
                    Saat dicentang, semua pendaftar berstatus <strong>diterima</strong> akan
                    muncul di halaman pengumuman publik.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Konten</h2>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Pengumuman</label>
                        <textarea v-model="form.pengumuman" rows="4" class="form-input" placeholder="Pengumuman ditampilkan di halaman utama..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">Syarat Pendaftaran</label>
                        <textarea v-model="form.syarat_pendaftaran" rows="6" class="form-input" placeholder="Daftar syarat pendaftaran..."></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="btn-primary">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
