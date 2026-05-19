<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    per_jurusan: Array,
    terbaru: Array,
});

const statusBadge = {
    pending: 'bg-amber-100 text-amber-800',
    diterima: 'bg-emerald-100 text-emerald-800',
    ditolak: 'bg-rose-100 text-rose-800',
};
</script>

<template>
    <AdminLayout>
        <template #header>Dashboard</template>
        <Head title="Dashboard Admin" />

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="text-sm text-slate-500">Total Pendaftar</div>
                <div class="text-3xl font-bold mt-1">{{ stats.total }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-amber-400">
                <div class="text-sm text-slate-500">Menunggu Verifikasi</div>
                <div class="text-3xl font-bold text-amber-600 mt-1">{{ stats.pending }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
                <div class="text-sm text-slate-500">Diterima</div>
                <div class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.diterima }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-rose-500">
                <div class="text-sm text-slate-500">Ditolak</div>
                <div class="text-3xl font-bold text-rose-600 mt-1">{{ stats.ditolak }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold">Pendaftar Terbaru</h2>
                    <Link :href="route('admin.pendaftar.index')" class="text-sm text-indigo-600 hover:text-indigo-700">Lihat Semua →</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-slate-500 border-b">
                                <th class="py-2">No. Pendaftaran</th>
                                <th class="py-2">Nama</th>
                                <th class="py-2">Jurusan</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in terbaru" :key="p.id" class="border-b last:border-b-0">
                                <td class="py-3 font-mono text-xs">{{ p.no_pendaftaran }}</td>
                                <td class="py-3">
                                    <Link :href="route('admin.pendaftar.show', p.id)" class="text-indigo-600 hover:underline">
                                        {{ p.nama_lengkap }}
                                    </Link>
                                </td>
                                <td class="py-3">{{ p.jurusan?.nama }}</td>
                                <td class="py-3">
                                    <span class="badge" :class="statusBadge[p.status]">{{ p.status }}</span>
                                </td>
                            </tr>
                            <tr v-if="terbaru.length === 0">
                                <td colspan="4" class="py-6 text-center text-slate-500">Belum ada pendaftar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-semibold mb-4">Per Jurusan</h2>
                <div class="space-y-3">
                    <div v-for="j in per_jurusan" :key="j.id">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium">{{ j.nama }}</span>
                            <span class="text-slate-500">{{ j.diterima_count }} / {{ j.kuota }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded">
                            <div
                                class="h-2 bg-indigo-500 rounded"
                                :style="{ width: Math.min(100, (j.diterima_count / Math.max(1, j.kuota)) * 100) + '%' }"
                            ></div>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">Total daftar: {{ j.pendaftar_count }}</div>
                    </div>
                    <div v-if="per_jurusan.length === 0" class="text-sm text-slate-500">Belum ada jurusan.</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
