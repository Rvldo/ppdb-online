<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pendaftar: Object,
    no_pendaftaran: String,
    not_found: Boolean,
});

const form = useForm({
    no_pendaftaran: props.no_pendaftaran || '',
});

function submit() {
    form.get(route('ppdb.cek-status'), { preserveState: true });
}

const statusLabel = {
    pending: { label: 'Menunggu Verifikasi', class: 'bg-amber-100 text-amber-800 border-amber-200', icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z' },
    diterima: { label: 'Diterima', class: 'bg-emerald-100 text-emerald-800 border-emerald-200', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    ditolak: { label: 'Tidak Diterima', class: 'bg-rose-100 text-rose-800 border-rose-200', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
};
</script>

<template>
    <PublicLayout>
        <Head title="Cek Status" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-900 via-indigo-900 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='60' height='60'/%3E%3C/svg%3E&quot;)"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32">
                <span class="inline-block bg-white/10 backdrop-blur-sm border border-white/10 px-3 py-1 rounded-full text-xs font-medium text-violet-200 mb-4">Lacak Pendaftaran</span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white">Cek Status Pendaftaran</h1>
                <p class="text-slate-300 mt-3 text-lg">Masukkan nomor pendaftaran untuk melihat status terkini</p>
            </div>
        </section>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 pb-16">
            <!-- Search Form -->
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-6 sm:p-8 mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Pendaftaran</label>
                <div class="flex gap-3">
                    <input
                        v-model="form.no_pendaftaran"
                        type="text"
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition-all"
                        placeholder="contoh: PPDB-2026-00001"
                        required
                    />
                    <button type="submit" :disabled="form.processing" class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 hover:from-violet-700 hover:to-indigo-700 disabled:opacity-50 transition-all whitespace-nowrap">
                        Cek
                    </button>
                </div>
            </form>

            <!-- Not Found -->
            <div v-if="not_found" class="bg-rose-50 border border-rose-200/60 text-rose-800 rounded-2xl p-6 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <div>
                    <div class="font-semibold">Tidak Ditemukan</div>
                    <p class="text-sm mt-1">Nomor pendaftaran <strong class="font-mono">{{ no_pendaftaran }}</strong> tidak ditemukan. Pastikan nomor yang dimasukkan sudah benar.</p>
                </div>
            </div>

            <!-- Result -->
            <div v-if="pendaftar" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <!-- Status Banner -->
                <div class="px-6 sm:px-8 py-6 border-b border-slate-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Nomor Pendaftaran</div>
                            <div class="font-mono font-bold text-2xl text-violet-700 mt-1">{{ pendaftar.no_pendaftaran }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border" :class="statusLabel[pendaftar.status].class">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" :d="statusLabel[pendaftar.status].icon" /></svg>
                            {{ statusLabel[pendaftar.status].label }}
                        </span>
                    </div>
                </div>

                <!-- Data -->
                <div class="px-6 sm:px-8 py-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <div>
                            <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ pendaftar.nama_lengkap }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Asal Sekolah</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ pendaftar.asal_sekolah }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Jurusan Pilihan</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ pendaftar.jurusan?.nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Tanggal Daftar</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ new Date(pendaftar.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</dd>
                        </div>
                    </dl>

                    <div v-if="pendaftar.catatan_admin" class="mt-6 bg-slate-50 border-l-4 border-violet-400 p-4 rounded-lg">
                        <div class="text-xs text-slate-400 font-medium mb-1">Catatan dari Panitia</div>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ pendaftar.catatan_admin }}</p>
                    </div>
                </div>

                <!-- Action -->
                <div class="px-6 sm:px-8 py-4 bg-slate-50 border-t border-slate-100">
                    <a :href="route('ppdb.bukti', pendaftar.no_pendaftaran)" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" /></svg>
                        Cetak Bukti Pendaftaran
                    </a>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
