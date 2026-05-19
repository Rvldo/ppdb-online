<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import AiChatbot from '@/Components/AiChatbot.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const appName = computed(() => page.props.app_name || 'PPDB Online');
const branding = computed(() => page.props.branding || {});
const logoUrl = computed(() => branding.value.logo_url);
const initial = computed(() => branding.value.singkatan || appName.value.charAt(0));
const primary = computed(() => branding.value.warna_primary || '#7c3aed');
const secondary = computed(() => branding.value.warna_secondary || '#4f46e5');

const scrolled = ref(false);
const mobileMenuOpen = ref(false);

function onScroll() { scrolled.value = window.scrollY > 20; }
onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

// Dynamic CSS vars
const cssVars = computed(() => ({
    '--color-primary': primary.value,
    '--color-secondary': secondary.value,
    '--color-accent': branding.value.warna_accent || '#d946ef',
}));
</script>

<template>
    <div class="min-h-screen flex flex-col bg-slate-50" :style="cssVars">
        <!-- Navbar -->
        <header
            class="fixed top-0 left-0 right-0 z-40 transition-all duration-300"
            :class="scrolled ? 'bg-white/80 backdrop-blur-xl shadow-lg shadow-slate-900/5 border-b border-slate-200/50' : 'bg-transparent'"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <Link :href="route('home')" class="flex items-center gap-3 group">
                        <!-- Dynamic logo -->
                        <div v-if="logoUrl" class="w-10 h-10 rounded-xl overflow-hidden shadow-lg group-hover:shadow-xl transition-shadow flex-shrink-0">
                            <img :src="logoUrl" :alt="appName" class="w-full h-full object-contain" />
                        </div>
                        <div v-else class="w-10 h-10 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-lg transition-shadow flex-shrink-0" :style="{ background: `linear-gradient(135deg, ${primary}, ${secondary})` }">
                            {{ initial }}
                        </div>
                        <div>
                            <span class="font-bold text-lg block leading-tight" :class="scrolled ? 'text-slate-900' : 'text-white'">{{ appName }}</span>
                            <span class="text-[10px] font-medium tracking-wider uppercase" :class="scrolled ? 'text-slate-400' : 'text-white/60'">Sistem PPDB Online</span>
                        </div>
                    </Link>

                    <nav class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="item in [
                                { name: 'Beranda', route: 'home' },
                                { name: 'Jurusan', route: 'ppdb.jurusan' },
                                { name: 'Pengumuman', route: 'ppdb.pengumuman' },
                                { name: 'Cek Status', route: 'ppdb.cek-status' },
                            ]"
                            :key="item.route"
                            :href="route(item.route)"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                            :class="route().current(item.route)
                                ? (scrolled ? 'bg-[--color-primary]/10 text-[--color-primary]' : 'bg-white/20 text-white')
                                : (scrolled ? 'text-slate-600 hover:text-[--color-primary] hover:bg-[--color-primary]/5' : 'text-white/80 hover:text-white hover:bg-white/10')"
                        >
                            {{ item.name }}
                        </Link>
                        <Link
                            :href="route('ppdb.daftar')"
                            class="ml-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200"
                            :style="{ background: `linear-gradient(135deg, ${primary}, ${secondary})` }"
                        >
                            Daftar Sekarang
                        </Link>
                    </nav>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl" :class="scrolled ? 'text-slate-700' : 'text-white'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                <div v-if="mobileMenuOpen" class="md:hidden bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-xl">
                    <nav class="px-4 py-4 space-y-1">
                        <Link v-for="item in [{ name: 'Beranda', route: 'home' }, { name: 'Jurusan', route: 'ppdb.jurusan' }, { name: 'Pengumuman', route: 'ppdb.pengumuman' }, { name: 'Cek Status', route: 'ppdb.cek-status' }]" :key="item.route" :href="route(item.route)" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-sm font-medium transition-colors" :class="route().current(item.route) ? 'bg-[--color-primary]/10 text-[--color-primary]' : 'text-slate-700 hover:bg-slate-50'">
                            {{ item.name }}
                        </Link>
                        <Link :href="route('ppdb.daftar')" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-sm font-semibold text-white text-center mt-2" :style="{ background: `linear-gradient(135deg, ${primary}, ${secondary})` }">
                            Daftar Sekarang
                        </Link>
                    </nav>
                </div>
            </Transition>
        </header>

        <!-- Flash -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-full" enter-to-class="opacity-100 translate-y-0">
            <div v-if="flash.success" class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2 max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ flash.success }}
            </div>
        </Transition>
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-full" enter-to-class="opacity-100 translate-y-0">
            <div v-if="flash.error" class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-rose-500 text-white px-6 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2 max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ flash.error }}
            </div>
        </Transition>

        <main class="flex-1"><slot /></main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-300 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950/50"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div v-if="logoUrl" class="w-10 h-10 rounded-xl overflow-hidden shadow-lg flex-shrink-0 bg-white/10"><img :src="logoUrl" :alt="appName" class="w-full h-full object-contain" /></div>
                            <div v-else class="w-10 h-10 rounded-xl text-white flex items-center justify-center font-bold shadow-lg" :style="{ background: `linear-gradient(135deg, ${primary}, ${secondary})` }">{{ initial }}</div>
                            <div>
                                <div class="font-bold text-white">{{ appName }}</div>
                                <div class="text-xs text-slate-500">Sistem PPDB Online</div>
                            </div>
                        </div>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ branding.footer_text || 'Sistem Penerimaan Peserta Didik Baru secara online. Daftar mudah, cepat, dan tanpa antri.' }}</p>
                        <div v-if="branding.alamat" class="mt-3 text-sm text-slate-500">{{ branding.alamat }}</div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Menu</h4>
                        <nav class="space-y-2">
                            <Link v-for="item in [{ name: 'Beranda', r: 'home' }, { name: 'Jurusan', r: 'ppdb.jurusan' }, { name: 'Pengumuman', r: 'ppdb.pengumuman' }, { name: 'Cek Status', r: 'ppdb.cek-status' }, { name: 'Daftar', r: 'ppdb.daftar' }]" :key="item.r" :href="route(item.r)" class="block text-sm text-slate-400 hover:text-white transition-colors">{{ item.name }}</Link>
                        </nav>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Kontak</h4>
                        <div class="space-y-2 text-sm text-slate-400">
                            <div v-if="branding.email">Email: {{ branding.email }}</div>
                            <div v-if="branding.website">Website: {{ branding.website }}</div>
                            <p class="mt-3 text-xs text-slate-500">Didukung oleh AI Claude dari Anthropic.</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-800 mt-8 pt-8 text-center text-sm text-slate-500">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. Hak cipta dilindungi.
                </div>
            </div>
        </footer>

        <AiChatbot />
    </div>
</template>
