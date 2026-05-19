<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    pendaftar: Object,
    jurusan: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const jurusanId = ref(props.filters.jurusan_id || '');

let timer = null;
function applyFilters() {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            route('admin.pendaftar.index'),
            { search: search.value, status: status.value, jurusan_id: jurusanId.value },
            { preserveState: true, replace: true }
        );
    }, 300);
}

watch([search, status, jurusanId], applyFilters);

const statusBadge = {
    pending: 'bg-amber-100 text-amber-800',
    diterima: 'bg-emerald-100 text-emerald-800',
    ditolak: 'bg-rose-100 text-rose-800',
};

const selected = ref([]);
const allChecked = computed({
    get: () => props.pendaftar.data.length > 0 && selected.value.length === props.pendaftar.data.length,
    set: (val) => {
        selected.value = val ? props.pendaftar.data.map(p => p.id) : [];
    },
});

watch(() => props.pendaftar.data, () => { selected.value = []; });

function exportUrl() {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (status.value) params.set('status', status.value);
    if (jurusanId.value) params.set('jurusan_id', jurusanId.value);
    return route('admin.pendaftar.export') + (params.toString() ? '?' + params.toString() : '');
}

function bulk(action) {
    if (selected.value.length === 0) return;
    const labels = { terima: 'menerima', tolak: 'menolak', reset: 'mereset', hapus: 'menghapus' };
    if (! confirm(`Yakin ${labels[action]} ${selected.value.length} pendaftar terpilih?`)) return;

    router.post(route('admin.pendaftar.bulk'), {
        action,
        ids: selected.value,
    }, {
        preserveScroll: true,
        onSuccess: () => { selected.value = []; },
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>Pendaftar</template>
        <Head title="Pendaftar" />

        <div class="bg-white rounded-xl shadow-sm p-5 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="form-label">Cari</label>
                    <input v-model="search" type="text" class="form-input" placeholder="Nama / No. Pendaftaran / NISN..." />
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select v-model="status" class="form-input">
                        <option value="">Semua</option>
                        <option value="pending">Menunggu</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jurusan</label>
                    <select v-model="jurusanId" class="form-input">
                        <option value="">Semua</option>
                        <option v-for="j in jurusan" :key="j.id" :value="j.id">{{ j.nama }}</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-4 border-t border-slate-100">
                <div v-if="selected.length > 0" class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-slate-600">{{ selected.length }} dipilih:</span>
                    <button @click="bulk('terima')" class="px-3 py-1 rounded text-xs bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Terima</button>
                    <button @click="bulk('tolak')"  class="px-3 py-1 rounded text-xs bg-rose-100 text-rose-700 hover:bg-rose-200">Tolak</button>
                    <button @click="bulk('reset')"  class="px-3 py-1 rounded text-xs bg-amber-100 text-amber-700 hover:bg-amber-200">Reset Pending</button>
                    <button @click="bulk('hapus')"  class="px-3 py-1 rounded text-xs bg-slate-200 text-slate-700 hover:bg-slate-300">Hapus</button>
                </div>
                <div v-else class="text-sm text-slate-500">Centang baris untuk melakukan aksi massal.</div>
                <a :href="exportUrl()" class="text-sm bg-slate-800 text-white px-3 py-1.5 rounded hover:bg-slate-700">⬇ Export CSV</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase text-slate-500">
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" v-model="allChecked" />
                            </th>
                            <th class="px-4 py-3">No. Pendaftaran</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Asal Sekolah</th>
                            <th class="px-4 py-3">Jurusan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in pendaftar.data" :key="p.id" class="border-t">
                            <td class="px-4 py-3"><input type="checkbox" :value="p.id" v-model="selected" /></td>
                            <td class="px-4 py-3 font-mono text-xs">{{ p.no_pendaftaran }}</td>
                            <td class="px-4 py-3 font-medium">{{ p.nama_lengkap }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ p.asal_sekolah }}</td>
                            <td class="px-4 py-3">{{ p.jurusan?.nama }}</td>
                            <td class="px-4 py-3">
                                <span class="badge" :class="statusBadge[p.status]">{{ p.status }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <Link :href="route('admin.pendaftar.show', p.id)" class="text-indigo-600 hover:underline mr-3">Detail</Link>
                                <a :href="route('ppdb.bukti', p.no_pendaftaran)" target="_blank" class="text-slate-600 hover:underline">Cetak</a>
                            </td>
                        </tr>
                        <tr v-if="pendaftar.data.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">Tidak ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between px-4 py-3 border-t bg-slate-50 text-sm">
                <div class="text-slate-600">
                    Menampilkan {{ pendaftar.from || 0 }}–{{ pendaftar.to || 0 }} dari {{ pendaftar.total }}
                </div>
                <div class="flex gap-1 flex-wrap">
                    <Link
                        v-for="link in pendaftar.links"
                        :key="link.label"
                        :href="link.url || ''"
                        v-html="link.label"
                        class="px-3 py-1 rounded border"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white border-slate-200 hover:bg-slate-100'"
                        :preserve-scroll="true"
                        :only="['pendaftar']"
                        :as="link.url ? 'a' : 'span'"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
