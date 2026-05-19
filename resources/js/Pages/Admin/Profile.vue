<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function updateProfile() {
    profileForm.put(route('admin.profile.update'), { preserveScroll: true });
}

function updatePassword() {
    passwordForm.put(route('admin.profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="Profil Admin" />
        <template #header>Profil Admin</template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">
            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-semibold text-lg mb-1">Informasi Akun</h2>
                <p class="text-sm text-slate-500 mb-5">Perbarui nama dan email akun.</p>

                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="form-label">Nama</label>
                        <input v-model="profileForm.name" type="text" class="form-input" required />
                        <div v-if="profileForm.errors.name" class="form-error">{{ profileForm.errors.name }}</div>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="profileForm.email" type="email" class="form-input" required />
                        <div v-if="profileForm.errors.email" class="form-error">{{ profileForm.errors.email }}</div>
                    </div>
                    <div>
                        <label class="form-label">Role</label>
                        <input :value="user.role" type="text" class="form-input bg-slate-50" readonly />
                    </div>
                    <button type="submit" :disabled="profileForm.processing" class="btn-primary">
                        {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                    </button>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-semibold text-lg mb-1">Ganti Password</h2>
                <p class="text-sm text-slate-500 mb-5">Pakai password yang kuat & jangan dibagikan.</p>

                <form @submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="form-label">Password Saat Ini</label>
                        <input v-model="passwordForm.current_password" type="password" class="form-input" required autocomplete="current-password" />
                        <div v-if="passwordForm.errors.current_password" class="form-error">{{ passwordForm.errors.current_password }}</div>
                    </div>
                    <div>
                        <label class="form-label">Password Baru</label>
                        <input v-model="passwordForm.password" type="password" class="form-input" required autocomplete="new-password" />
                        <div v-if="passwordForm.errors.password" class="form-error">{{ passwordForm.errors.password }}</div>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password</label>
                        <input v-model="passwordForm.password_confirmation" type="password" class="form-input" required autocomplete="new-password" />
                    </div>
                    <button type="submit" :disabled="passwordForm.processing" class="btn-primary">
                        {{ passwordForm.processing ? 'Menyimpan...' : 'Ubah Password' }}
                    </button>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>
