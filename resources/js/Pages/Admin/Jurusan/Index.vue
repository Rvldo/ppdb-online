<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    jurusan: Array,
});

const showForm = ref(false);
const editing = ref(null);

const form = useForm({
    kode: '',
    nama: '',
    deskripsi: '',
    kuota: 0,
    aktif: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.aktif = true;
    showForm.value = true;
}

function openEdit(j) {
    editing.value = j;
    form.kode = j.kode;
    form.nama = j.nama;
    form.deskripsi = j.deskripsi || '';
    form.kuota = j.kuota;
    form.aktif = !!j.aktif;
    showForm.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.jurusan.update', editing.value.id), {
            onSuccess: () => (showForm.value = false),
        });
    } else {
        form.post(route('admin.jurusan.store'), {
            onSuccess: () => (showForm.value = false),
        });
    }
}

function destroy(j) {
    if (confirm(`Hapus jurusan "${j.nama}"?`)) {
        router.delete(route('admin.jurusan.destroy', j.id));
    }
}
</script>

<template>
    <AdminLayout>
        <template #header>Jurusan</template>
        <Head title="Jurusan" />

        <div class="flex items-center justify-between mb-4">
            <p class="text-slate-600 text-sm">Kelola jurusan yang dapat dipilih oleh pendaftar.</p>
            <button @click="openCreate" class="btn-primary text-sm">+ Tambah Jurusan</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr class="text-left">
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kuota</th>
                        <th class="px-4 py-3">Diterima</th>
                        <th class="px-4 py-3">Total Daftar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="j in jurusan" :key="j.id" class="border-t">
                        <td class="px-4 py-3 font-mono text-xs">{{ j.kode }}</td>
                        <td class="px-4 py-3 font-medium">{{ j.nama }}</td>
                        <td class="px-4 py-3">{{ j.kuota }}</td>
                        <td class="px-4 py-3 text-emerald-700">{{ j.diterima_count }}</td>
                        <td class="px-4 py-3">{{ j.pendaftar_count }}</td>
                        <td class="px-4 py-3">
                            <span class="badge" :class="j.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'">
                                {{ j.aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <button @click="openEdit(j)" class="text-indigo-600 hover:underline">Edit</button>
                            <button @click="destroy(j)" class="text-rose-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="jurusan.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada jurusan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" @click.self="showForm = false">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold">{{ editing ? 'Edit' : 'Tambah' }} Jurusan</h3>
                    <button @click="showForm = false" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form @submit.prevent="submit" class="p-5 space-y-3">
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Kode *</label>
                            <input v-model="form.kode" type="text" class="form-input" required />
                            <div v-if="form.errors.kode" class="form-error">{{ form.errors.kode }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="form-label">Nama *</label>
                            <input v-model="form.nama" type="text" class="form-input" required />
                            <div v-if="form.errors.nama" class="form-error">{{ form.errors.nama }}</div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="3" class="form-input"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3 items-end">
                        <div>
                            <label class="form-label">Kuota *</label>
                            <input v-model="form.kuota" type="number" min="0" class="form-input" required />
                        </div>
                        <label class="inline-flex items-center gap-2 pb-2">
                            <input v-model="form.aktif" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                            Aktif (tampil di pendaftaran)
                        </label>
                    </div>
                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="showForm = false" class="btn-secondary">Batal</button>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
