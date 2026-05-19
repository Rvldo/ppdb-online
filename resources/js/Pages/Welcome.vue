<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ParticleBackground from '@/Components/ParticleBackground.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    pengaturan: Object,
    jurusan: Array,
    is_open: Boolean,
    stats: Object,
});

// Animated counters
const animatedStats = ref({ total_pendaftar: 0, diterima: 0, jumlah_jurusan: 0 });

onMounted(() => {
    const targets = props.stats;
    const duration = 2000;
    const steps = 80;
    const interval = duration / steps;
    let step = 0;
    const timer = setInterval(() => {
        step++;
        const p = step / steps;
        const ease = 1 - Math.pow(1 - p, 4);
        animatedStats.value = {
            total_pendaftar: Math.round(targets.total_pendaftar * ease),
            diterima: Math.round(targets.diterima * ease),
            jumlah_jurusan: Math.round(targets.jumlah_jurusan * ease),
        };
        if (step >= steps) clearInterval(timer);
    }, interval);
});

// Countdown timer
const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
let countdownInterval = null;

function updateCountdown() {
    const now = new Date();
    const target = props.is_open
        ? new Date(props.pengaturan.tanggal_tutup)
        : new Date(props.pengaturan.tanggal_buka);
    const diff = Math.max(0, target - now);
    countdown.value = {
        days: Math.floor(diff / 86400000),
        hours: Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
        seconds: Math.floor((diff % 60000) / 1000),
    };
}

onMounted(() => {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
});
onUnmounted(() => clearInterval(countdownInterval));

const countdownLabel = computed(() => props.is_open ? 'Pendaftaran ditutup dalam' : 'Pendaftaran dibuka dalam');

// Scroll animation
const scrollY = ref(0);
function onScroll() { scrollY.value = window.scrollY; }
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

const howItWorks = [
    { icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', title: 'Isi Formulir', desc: 'Lengkapi data diri, akademik, dan data orang tua secara online dengan bantuan AI assistant' },
    { icon: 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12', title: 'Upload Berkas', desc: 'Unggah dokumen persyaratan (ijazah, akte, KK) dalam format PDF atau JPG' },
    { icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', title: 'Verifikasi', desc: 'Tim panitia akan memverifikasi data dan berkasmu. Pantau status secara real-time' },
    { icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', title: 'Pengumuman', desc: 'Hasil seleksi diumumkan di website. Yang diterima langsung daftar ulang' },
];

const features = [
    { icon: 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3', title: 'AI Assistant', desc: 'Chatbot cerdas siap menjawab pertanyaanmu 24/7', color: 'violet' },
    { icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', title: 'Aman & Terenkripsi', desc: 'Data pendaftaranmu terlindungi dengan baik', color: 'emerald' },
    { icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', title: 'Mobile Friendly', desc: 'Daftar dari HP, tablet, atau komputer', color: 'blue' },
    { icon: 'M13 10V3L4 14h7v7l9-11h-7z', title: 'Real-time Status', desc: 'Pantau status pendaftaranmu kapan saja', color: 'amber' },
];

const colorMap = {
    violet: { bg: 'bg-violet-100', text: 'text-violet-600', glow: 'shadow-violet-500/10' },
    emerald: { bg: 'bg-emerald-100', text: 'text-emerald-600', glow: 'shadow-emerald-500/10' },
    blue: { bg: 'bg-blue-100', text: 'text-blue-600', glow: 'shadow-blue-500/10' },
    amber: { bg: 'bg-amber-100', text: 'text-amber-600', glow: 'shadow-amber-500/10' },
};
</script>

<template>
    <PublicLayout>
        <Head title="Beranda" />

        <!-- ==================== HERO ==================== -->
        <section class="relative min-h-screen flex items-center overflow-hidden">
            <!-- Multi-layer background -->
            <div class="absolute inset-0 bg-[#0f0a2a]"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-violet-950/80 via-indigo-950/60 to-purple-950/80"></div>

            <!-- Animated mesh gradient -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-1/2 -left-1/4 w-[80vw] h-[80vw] rounded-full bg-violet-600/15 blur-[120px] animate-pulse-slow"></div>
                <div class="absolute -bottom-1/2 -right-1/4 w-[70vw] h-[70vw] rounded-full bg-indigo-600/15 blur-[120px] animate-pulse-slow" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/3 right-1/3 w-[50vw] h-[50vw] rounded-full bg-fuchsia-600/10 blur-[100px] animate-pulse-slow" style="animation-delay: 4s;"></div>
            </div>

            <!-- Particle network -->
            <ParticleBackground />

            <!-- Grid overlay -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Crect fill='none' stroke='white' stroke-width='0.5' width='80' height='80'/%3E%3C/svg%3E&quot;)"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left: Text -->
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/[0.07] backdrop-blur-sm border border-white/[0.08] px-4 py-2 rounded-full text-sm mb-8 animate-fade-in">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full rounded-full opacity-75 animate-ping" :class="is_open ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="is_open ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                            </span>
                            <span class="text-slate-300">Tahun Ajaran {{ pengaturan.tahun_ajaran }}</span>
                            <span class="text-white/30">|</span>
                            <span :class="is_open ? 'text-emerald-400' : 'text-rose-400'" class="font-semibold">{{ is_open ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}</span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-black text-white leading-[1.08] mb-6 tracking-tight animate-slide-up">
                            Penerimaan Peserta
                            <span class="block bg-gradient-to-r from-violet-400 via-fuchsia-400 to-pink-400 bg-clip-text text-transparent">Didik Baru</span>
                            <span class="block text-3xl sm:text-4xl lg:text-[2.5rem] mt-2 font-bold text-white/80">{{ pengaturan.nama_sekolah }}</span>
                        </h1>

                        <p class="text-lg text-slate-400 mb-10 max-w-lg animate-slide-up leading-relaxed" style="animation-delay: 0.1s">
                            Daftar online cepat & mudah dengan bantuan <span class="text-violet-400 font-semibold">AI Assistant</span>. Pantau statusmu kapan saja, di mana saja.
                        </p>

                        <div class="flex flex-wrap gap-4 animate-slide-up" style="animation-delay: 0.2s">
                            <Link
                                v-if="is_open"
                                :href="route('ppdb.daftar')"
                                class="group relative px-8 py-4 rounded-2xl text-base font-bold bg-gradient-to-r from-violet-500 to-indigo-500 text-white overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/30"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    Daftar Sekarang
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </Link>
                            <span v-else class="px-8 py-4 rounded-2xl text-base font-bold bg-white/[0.06] text-white/50 border border-white/[0.08]">
                                Pendaftaran Ditutup
                            </span>
                            <Link :href="route('ppdb.cek-status')" class="px-8 py-4 rounded-2xl text-base font-semibold text-white border border-white/15 hover:bg-white/[0.06] transition-all duration-300">
                                Cek Status
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Countdown + Info Card -->
                    <div class="animate-slide-up" style="animation-delay: 0.3s">
                        <div class="relative">
                            <!-- Glow behind card -->
                            <div class="absolute -inset-4 bg-gradient-to-r from-violet-600/20 to-indigo-600/20 rounded-3xl blur-2xl"></div>
                            <div class="relative bg-white/[0.06] backdrop-blur-xl border border-white/[0.1] rounded-3xl p-8 overflow-hidden">
                                <!-- Decorative corner -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-violet-500/10 to-transparent rounded-bl-[4rem]"></div>

                                <div class="text-xs text-violet-400 font-bold uppercase tracking-widest mb-4">{{ countdownLabel }}</div>

                                <!-- Countdown -->
                                <div class="grid grid-cols-4 gap-3 mb-8">
                                    <div v-for="(item, key) in { Hari: countdown.days, Jam: countdown.hours, Menit: countdown.minutes, Detik: countdown.seconds }" :key="key" class="text-center">
                                        <div class="bg-white/[0.08] backdrop-blur rounded-xl py-3 px-2 border border-white/[0.06]">
                                            <div class="text-3xl sm:text-4xl font-black text-white tabular-nums">{{ String(item).padStart(2, '0') }}</div>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-1.5 font-medium uppercase tracking-wider">{{ key }}</div>
                                    </div>
                                </div>

                                <!-- Period info -->
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center gap-3 text-slate-300">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500">Periode Pendaftaran</div>
                                            <div class="font-medium">{{ new Date(pengaturan.tanggal_buka).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }} — {{ new Date(pengaturan.tanggal_tutup).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</div>
                                        </div>
                                    </div>
                                    <div v-if="pengaturan.contact_person" class="flex items-center gap-3 text-slate-300">
                                        <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500">Contact Person</div>
                                            <div class="font-medium">{{ pengaturan.contact_person }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2">
                <span class="text-[10px] text-white/30 uppercase tracking-widest font-medium">Scroll</span>
                <div class="w-5 h-8 rounded-full border-2 border-white/20 flex justify-center pt-1.5">
                    <div class="w-1 h-2 bg-white/40 rounded-full animate-bounce"></div>
                </div>
            </div>
        </section>

        <!-- ==================== STATS ==================== -->
        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div v-for="(stat, i) in [
                    { value: animatedStats.total_pendaftar, label: 'Total Pendaftar', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'violet', suffix: '' },
                    { value: animatedStats.diterima, label: 'Sudah Diterima', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'emerald', suffix: '' },
                    { value: animatedStats.jumlah_jurusan, label: 'Jurusan Tersedia', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', color: 'fuchsia', suffix: '' },
                ]" :key="i"
                    class="group bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-6 sm:p-8 border border-slate-100/80 hover:shadow-2xl hover:-translate-y-1 transition-all duration-500"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
                             :class="{
                                 'bg-violet-100 text-violet-600': stat.color === 'violet',
                                 'bg-emerald-100 text-emerald-600': stat.color === 'emerald',
                                 'bg-fuchsia-100 text-fuchsia-600': stat.color === 'fuchsia',
                             }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" :d="stat.icon" /></svg>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-black text-slate-900 tabular-nums">{{ stat.value }}{{ stat.suffix }}</div>
                            <div class="text-sm text-slate-500 font-medium">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== FEATURES ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="text-center mb-16">
                <span class="inline-block bg-gradient-to-r from-violet-100 to-indigo-100 text-violet-700 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">Keunggulan</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900">Mengapa Daftar di Sini?</h2>
                <p class="text-slate-500 mt-3 max-w-xl mx-auto">Sistem PPDB modern dengan teknologi AI untuk pengalaman pendaftaran terbaik</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="f in features" :key="f.title" class="group relative bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 rounded-bl-[3rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                         :class="{
                             'bg-violet-500/5': f.color === 'violet',
                             'bg-emerald-500/5': f.color === 'emerald',
                             'bg-blue-500/5': f.color === 'blue',
                             'bg-amber-500/5': f.color === 'amber',
                         }"></div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 transition-transform duration-300 group-hover:scale-110"
                         :class="colorMap[f.color].bg + ' ' + colorMap[f.color].text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" :d="f.icon" /></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">{{ f.title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ f.desc }}</p>
                </div>
            </div>
        </section>

        <!-- ==================== HOW IT WORKS ==================== -->
        <section class="bg-gradient-to-b from-white via-slate-50/80 to-white border-y border-slate-100/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
                <div class="text-center mb-16">
                    <span class="inline-block bg-gradient-to-r from-violet-100 to-indigo-100 text-violet-700 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">Langkah Pendaftaran</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900">4 Langkah Mudah</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                    <!-- Connector line (desktop) -->
                    <div class="hidden lg:block absolute top-12 left-[12.5%] right-[12.5%] h-0.5 bg-gradient-to-r from-violet-200 via-indigo-200 to-purple-200"></div>

                    <div v-for="(s, i) in howItWorks" :key="i" class="relative text-center group">
                        <div class="relative z-10 w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-violet-500 via-purple-500 to-indigo-600 text-white flex items-center justify-center shadow-xl shadow-violet-500/20 group-hover:shadow-violet-500/40 group-hover:scale-105 transition-all duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" :d="s.icon" /></svg>
                            <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-lg shadow-lg flex items-center justify-center text-xs font-black text-violet-600">{{ i + 1 }}</div>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mt-6 mb-2">{{ s.title }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-[200px] mx-auto">{{ s.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== JURUSAN ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <span class="inline-block bg-gradient-to-r from-indigo-100 to-violet-100 text-indigo-700 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">Program Keahlian</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900">Pilihan Jurusan</h2>
                    <p class="text-slate-500 mt-2">Temukan jurusan yang sesuai dengan minat dan bakatmu</p>
                </div>
                <Link :href="route('ppdb.jurusan')" class="hidden sm:inline-flex items-center gap-2 text-sm font-bold text-violet-600 hover:text-violet-700 bg-violet-50 hover:bg-violet-100 px-4 py-2 rounded-xl transition-all">
                    Lihat Detail
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </Link>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="j in jurusan"
                    :key="j.id"
                    class="group relative bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-2xl hover:shadow-violet-500/5 hover:-translate-y-2 transition-all duration-500 overflow-hidden"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-500/[0.02] to-indigo-500/[0.02] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <span class="inline-block bg-gradient-to-r from-violet-100 to-indigo-100 text-violet-700 text-xs font-bold px-3 py-1.5 rounded-lg">{{ j.kode }}</span>
                            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">Kuota {{ j.kuota }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-violet-700 transition-colors duration-300">{{ j.nama }}</h3>
                        <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ j.deskripsi || 'Informasi jurusan akan segera ditambahkan.' }}</p>
                        <Link :href="route('ppdb.jurusan')" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-600 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Lihat detail
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </Link>
                    </div>
                </div>
            </div>
            <div v-if="jurusan.length === 0" class="text-center text-slate-400 py-16">Belum ada jurusan tersedia.</div>
            <div class="text-center mt-8 sm:hidden">
                <Link :href="route('ppdb.jurusan')" class="text-sm font-semibold text-violet-600">Lihat Semua Jurusan &rarr;</Link>
            </div>
        </section>

        <!-- ==================== AI CTA ==================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
            <div class="relative overflow-hidden rounded-[2rem]">
                <div class="absolute inset-0 bg-gradient-to-r from-violet-700 via-purple-700 to-indigo-700"></div>
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-violet-400/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>

                <div class="relative p-8 sm:p-14 flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-24 h-24 rounded-3xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0 ring-1 ring-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                        </svg>
                    </div>
                    <div class="text-center sm:text-left flex-1">
                        <h3 class="text-2xl sm:text-3xl font-black text-white mb-3">Ada Pertanyaan? Tanya AI Assistant</h3>
                        <p class="text-violet-200 leading-relaxed max-w-xl">Klik tombol chat di pojok kanan bawah untuk berbicara dengan asisten AI kami. Didukung oleh <strong class="text-white">Claude</strong> dari Anthropic — menjawab pertanyaanmu secara instan, 24/7.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== PENGUMUMAN ==================== -->
        <section v-if="pengaturan.pengumuman" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/40 p-6 sm:p-8 rounded-2xl flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                </div>
                <div>
                    <h3 class="font-bold text-amber-900 text-lg mb-2">Pengumuman</h3>
                    <p class="text-amber-800 whitespace-pre-line leading-relaxed">{{ pengaturan.pengumuman }}</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
