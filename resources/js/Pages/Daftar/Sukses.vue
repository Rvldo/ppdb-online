<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ pendaftar: Object });
</script>

<template>
    <PublicLayout>
        <Head title="Pendaftaran Berhasil" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='60' height='60'/%3E%3C/svg%3E&quot;)"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32 text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white mb-4">Pendaftaran Berhasil!</h1>
                <p class="text-emerald-200 text-lg">Selamat, <strong>{{ pendaftar.nama_lengkap }}</strong>. Pendaftaranmu telah kami terima.</p>
            </div>
        </section>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 pb-16">
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 rounded-2xl p-6 mb-6">
                        <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Nomor Pendaftaran</div>
                        <div class="text-3xl font-mono font-extrabold text-violet-700 tracking-wider mt-1">{{ pendaftar.no_pendaftaran }}</div>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm">
                            <div>
                                <span class="text-slate-400">Jurusan:</span>
                                <strong class="text-slate-700 ml-1">{{ pendaftar.jurusan?.nama }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400">Status:</span>
                                <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Verifikasi</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200/60 rounded-xl p-4 mb-6 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm text-amber-800">Simpan nomor pendaftaran ini. Gunakan nomor ini untuk mengecek status pendaftaranmu.</p>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-center">
                        <a :href="route('ppdb.bukti', pendaftar.no_pendaftaran)" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" /></svg>
                            Cetak Bukti
                        </a>
                        <Link :href="route('ppdb.cek-status', { no_pendaftaran: pendaftar.no_pendaftaran })" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                            Cek Status
                        </Link>
                        <Link :href="route('home')" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                            Beranda
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
