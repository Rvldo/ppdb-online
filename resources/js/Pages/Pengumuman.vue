<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    pengaturan: Object,
    diumumkan: Boolean,
    tanggal_pengumuman: String,
    diterima_per_jurusan: Array,
});

function formatTanggal(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>
    <PublicLayout>
        <Head title="Pengumuman Hasil PPDB" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='60' height='60'/%3E%3C/svg%3E&quot;)"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32">
                <span class="inline-block bg-white/10 backdrop-blur-sm border border-white/10 px-3 py-1 rounded-full text-xs font-medium text-emerald-200 mb-4">Hasil Seleksi</span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white">Pengumuman Hasil PPDB</h1>
                <p class="text-emerald-200 mt-3 text-lg">{{ pengaturan.nama_sekolah }} &mdash; Tahun Ajaran {{ pengaturan.tahun_ajaran }}</p>
            </div>
        </section>

        <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <!-- Belum diumumkan -->
            <div v-if="!diumumkan" class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/60 rounded-2xl p-8 sm:p-12 text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-amber-100 text-amber-500 flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h2 class="text-2xl font-bold text-amber-900 mb-3">Hasil Belum Diumumkan</h2>
                <p class="text-amber-800 text-lg mb-2">
                    Pengumuman akan dirilis pada <strong>{{ formatTanggal(tanggal_pengumuman) }}</strong>.
                </p>
                <p class="text-sm text-amber-700 mb-8">Pantau halaman ini secara berkala atau cek status melalui nomor pendaftaranmu.</p>
                <Link :href="route('ppdb.cek-status')" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all">
                    Cek Status Saya
                </Link>
            </div>

            <!-- Sudah diumumkan -->
            <div v-else>
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/60 rounded-2xl p-6 sm:p-8 mb-8 flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-emerald-900 text-xl mb-1">Selamat kepada para pendaftar yang diterima!</h2>
                        <p class="text-sm text-emerald-700">
                            Diumumkan pada {{ formatTanggal(tanggal_pengumuman) }}.
                            Silakan menunggu informasi daftar ulang dari panitia
                            <span v-if="pengaturan.contact_person">({{ pengaturan.contact_person }})</span>.
                        </p>
                    </div>
                </div>

                <div v-if="diterima_per_jurusan.length === 0" class="text-center py-16 text-slate-400">
                    Belum ada pendaftar yang diterima.
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="group in diterima_per_jurusan"
                        :key="group.jurusan"
                        class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300"
                    >
                        <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 text-lg">{{ group.jurusan }}</h3>
                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" /></svg>
                                {{ group.pendaftar.length }} diterima
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50/80 text-slate-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="text-left px-6 py-3 font-semibold">No.</th>
                                        <th class="text-left px-6 py-3 font-semibold">No. Pendaftaran</th>
                                        <th class="text-left px-6 py-3 font-semibold">Nama Lengkap</th>
                                        <th class="text-left px-6 py-3 font-semibold">Asal Sekolah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(p, i) in group.pendaftar" :key="p.no_pendaftaran" class="hover:bg-violet-50/30 transition-colors">
                                        <td class="px-6 py-3 text-slate-400">{{ i + 1 }}</td>
                                        <td class="px-6 py-3 font-mono text-violet-700 font-medium">{{ p.no_pendaftaran }}</td>
                                        <td class="px-6 py-3 font-semibold text-slate-900">{{ p.nama_lengkap }}</td>
                                        <td class="px-6 py-3 text-slate-500">{{ p.asal_sekolah }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
