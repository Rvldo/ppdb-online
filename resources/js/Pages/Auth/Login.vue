<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login Admin" />
    <div class="min-h-screen bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center text-white mb-6">
                <Link href="/" class="inline-flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center font-bold">P</div>
                    <span class="font-bold text-lg">PPDB Online</span>
                </Link>
                <h1 class="text-2xl font-bold">Login Admin</h1>
                <p class="text-indigo-100 text-sm mt-1">Masuk untuk mengelola pendaftaran</p>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-xl p-6 space-y-4">
                <div>
                    <label class="form-label">Email</label>
                    <input v-model="form.email" type="email" class="form-input" autofocus required />
                    <div v-if="form.errors.email" class="form-error">{{ form.errors.email }}</div>
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input v-model="form.password" type="password" class="form-input" required />
                    <div v-if="form.errors.password" class="form-error">{{ form.errors.password }}</div>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    Ingat saya
                </label>
                <button type="submit" :disabled="form.processing" class="btn-primary w-full">
                    {{ form.processing ? 'Memproses...' : 'Masuk' }}
                </button>
                <div class="text-center text-xs text-slate-500 pt-2 border-t">
                    Default: <strong>admin@ppdb.test</strong> / <strong>password</strong>
                </div>
            </form>
        </div>
    </div>
</template>
