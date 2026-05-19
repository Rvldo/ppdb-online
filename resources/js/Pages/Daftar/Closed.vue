<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ pengaturan: Object });
</script>

<template>
    <PublicLayout>
        <Head title="Pendaftaran Ditutup" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='60' height='60'/%3E%3C/svg%3E&quot;)"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32 text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-rose-500/20 backdrop-blur flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white mb-4">Pendaftaran Ditutup</h1>
                <p class="text-slate-300 text-lg max-w-xl mx-auto">
                    Maaf, periode pendaftaran PPDB belum dibuka atau sudah berakhir.
                </p>
                <p class="text-slate-400 mt-4">
                    Periode: {{ new Date(pengaturan.tanggal_buka).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }} &mdash;
                    {{ new Date(pengaturan.tanggal_tutup).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
                <Link :href="route('home')" class="mt-8 inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl shadow-violet-500/25 hover:shadow-violet-500/40 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    Kembali ke Beranda
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
