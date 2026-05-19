<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    pengaturan: Object,
    jurusan: Array,
});
</script>

<template>
    <PublicLayout>
        <Head title="Daftar Jurusan" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-900 via-indigo-900 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='60' height='60'/%3E%3C/svg%3E&quot;)"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32">
                <span class="inline-block bg-white/10 backdrop-blur-sm border border-white/10 px-3 py-1 rounded-full text-xs font-medium text-violet-200 mb-4">Program Keahlian</span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white">Pilihan Jurusan</h1>
                <p class="text-slate-300 mt-3 text-lg">{{ pengaturan.nama_sekolah }} &mdash; Tahun Ajaran {{ pengaturan.tahun_ajaran }}</p>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div v-if="jurusan.length === 0" class="text-center text-slate-400 py-20">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                Belum ada jurusan aktif.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="j in jurusan"
                    :key="j.id"
                    class="group bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 hover:shadow-xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300 overflow-hidden relative"
                >
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-violet-500/5 to-indigo-500/5 rounded-bl-[5rem]"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-5">
                            <div>
                                <span class="inline-block bg-violet-100 text-violet-700 px-3 py-1 rounded-lg text-xs font-bold mb-3">{{ j.kode }}</span>
                                <h2 class="text-2xl font-bold text-slate-900 group-hover:text-violet-700 transition-colors">{{ j.nama }}</h2>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-400 font-medium uppercase tracking-wider">Sisa Kuota</div>
                                <div class="text-3xl font-extrabold mt-1" :class="j.sisa_kuota === 0 ? 'text-rose-500' : 'text-emerald-500'">{{ j.sisa_kuota }}</div>
                                <div class="text-xs text-slate-400">dari {{ j.kuota }}</div>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 mb-6 leading-relaxed">{{ j.deskripsi || 'Informasi jurusan akan segera ditambahkan.' }}</p>

                        <div class="relative h-3 bg-slate-100 rounded-full overflow-hidden mb-2">
                            <div
                                class="absolute inset-y-0 left-0 rounded-full transition-all duration-1000 ease-out"
                                :class="j.sisa_kuota === 0 ? 'bg-gradient-to-r from-rose-400 to-rose-500' : 'bg-gradient-to-r from-violet-500 to-indigo-500'"
                                :style="{ width: (j.kuota ? Math.min(100, (j.diterima / j.kuota) * 100) : 0) + '%' }"
                            ></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">{{ j.diterima }} dari {{ j.kuota }} kursi terisi</span>
                            <span class="font-semibold" :class="j.sisa_kuota === 0 ? 'text-rose-500' : 'text-emerald-500'">
                                {{ j.sisa_kuota === 0 ? 'PENUH' : Math.round((j.diterima / j.kuota) * 100) + '%' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <Link :href="route('ppdb.daftar')" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl shadow-violet-500/25 hover:shadow-violet-500/40 transition-all duration-300">
                    Daftar Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
