<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});
const appName = computed(() => page.props.app_name || 'PPDB Online');
const branding = computed(() => page.props.branding || {});
const isSuperAdmin = computed(() => user.value?.is_superadmin);
const primary = computed(() => branding.value.warna_primary || '#7c3aed');
const secondary = computed(() => branding.value.warna_secondary || '#4f46e5');

const sidebarOpen = ref(false);

const nav = computed(() => {
    const items = [
        { name: 'Dashboard', route: 'admin.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        { name: 'Pendaftar', route: 'admin.pendaftar.index', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
        { name: 'Jurusan', route: 'admin.jurusan.index', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
        { name: 'Pengaturan', route: 'admin.pengaturan.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    ];

    if (isSuperAdmin.value) {
        items.push({ name: 'Tampilan', route: 'admin.tampilan.index', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' });
    }

    items.push({ name: 'Profil', route: 'admin.profile.edit', icon: 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zM21 12a9 9 0 11-18 0 9 9 0 0118 0z' });

    return items;
});

function isActive(name) {
    return route().current(name) || route().current(name + '.*');
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-slate-50/50 flex">
        <aside
            class="w-[270px] fixed inset-y-0 left-0 transform z-40 transition-all duration-300 lg:translate-x-0 lg:relative lg:flex lg:flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950/80 rounded-r-2xl lg:rounded-none"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>

            <div class="relative flex flex-col h-full">
                <div class="px-6 py-6 flex items-center gap-3">
                    <div v-if="branding.logo_url" class="w-10 h-10 rounded-xl overflow-hidden shadow-lg bg-white/10 flex-shrink-0"><img :src="branding.logo_url" :alt="appName" class="w-full h-full object-contain" /></div>
                    <div v-else class="w-10 h-10 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-lg" :style="{ background: `linear-gradient(135deg, ${primary}, ${secondary})` }">{{ branding.singkatan || 'A' }}</div>
                    <div>
                        <div class="font-bold text-white text-sm truncate max-w-[160px]">{{ appName }}</div>
                        <div class="text-[10px] font-medium uppercase tracking-wider" :style="{ color: primary }">Admin Panel</div>
                    </div>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1">
                    <Link
                        v-for="item in nav"
                        :key="item.route"
                        :href="route(item.route)"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200"
                        :class="isActive(item.route.split('.').slice(0, 2).join('.'))
                            ? 'text-white shadow-lg'
                            : 'text-slate-400 hover:bg-white/[0.06] hover:text-white'"
                        :style="isActive(item.route.split('.').slice(0, 2).join('.')) ? { background: `linear-gradient(135deg, ${primary}e6, ${secondary}e6)` } : {}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
                        <span>{{ item.name }}</span>
                        <span v-if="item.name === 'Tampilan'" class="ml-auto text-[9px] font-bold uppercase tracking-wider bg-white/10 px-1.5 py-0.5 rounded">Super</span>
                    </Link>
                </nav>

                <div class="px-4 py-5 border-t border-white/[0.06]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold ring-1 ring-white/10" :style="{ background: `${primary}33` }">
                            {{ user?.name?.charAt(0) || 'A' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-white font-medium truncate">{{ user?.name }}</div>
                            <div class="text-[11px] capitalize" :class="isSuperAdmin ? 'text-amber-400 font-semibold' : 'text-slate-500'">{{ user?.role }}</div>
                        </div>
                    </div>
                    <button @click="logout" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </button>
                </div>
            </div>
        </aside>

        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"></div>
        </Transition>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 px-4 sm:px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <h1 class="font-bold text-slate-900"><slot name="header">Panel Admin</slot></h1>
                </div>
                <Link :href="route('home')" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    Lihat Situs
                </Link>
            </header>

            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                <div v-if="flash.success" class="mx-4 sm:mx-6 mt-4 bg-emerald-50 border border-emerald-200/60 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ flash.success }}
                </div>
            </Transition>
            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                <div v-if="flash.error" class="mx-4 sm:mx-6 mt-4 bg-rose-50 border border-rose-200/60 text-rose-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ flash.error }}
                </div>
            </Transition>

            <main class="flex-1 p-4 sm:p-6"><slot /></main>
        </div>
    </div>
</template>
