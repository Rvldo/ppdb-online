<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    pengaturan: Object,
    logo_url: String,
    favicon_url: String,
    hero_bg_url: String,
    ai_config: Object,
});

// Branding form
const form = useForm({
    singkatan_sekolah: props.pengaturan.singkatan_sekolah || '',
    alamat_sekolah: props.pengaturan.alamat_sekolah || '',
    website_sekolah: props.pengaturan.website_sekolah || '',
    email_sekolah: props.pengaturan.email_sekolah || '',
    warna_primary: props.pengaturan.warna_primary || '#7c3aed',
    warna_secondary: props.pengaturan.warna_secondary || '#4f46e5',
    warna_accent: props.pengaturan.warna_accent || '#d946ef',
    hero_title: props.pengaturan.hero_title || '',
    hero_subtitle: props.pengaturan.hero_subtitle || '',
    footer_text: props.pengaturan.footer_text || '',
});

function submitBranding() {
    form.put(route('admin.tampilan.branding'));
}

// Logo upload
const logoForm = useForm({ logo: null });
const logoPreview = ref(props.logo_url);

function handleLogoChange(e) {
    const file = e.target.files[0];
    if (file) {
        logoForm.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

function uploadLogo() {
    logoForm.post(route('admin.tampilan.logo'), {
        forceFormData: true,
        onSuccess: () => { logoForm.reset(); },
    });
}

function deleteLogo() {
    if (confirm('Yakin hapus logo?')) {
        router.delete(route('admin.tampilan.logo.delete'));
        logoPreview.value = null;
    }
}

// Favicon upload
const faviconForm = useForm({ favicon: null });

function uploadFavicon(e) {
    faviconForm.favicon = e.target.files[0];
    faviconForm.post(route('admin.tampilan.favicon'), { forceFormData: true });
}

// Hero BG upload
const heroBgForm = useForm({ hero_bg: null });
const heroBgPreview = ref(props.hero_bg_url);

function handleHeroBgChange(e) {
    const file = e.target.files[0];
    if (file) {
        heroBgForm.hero_bg = file;
        heroBgPreview.value = URL.createObjectURL(file);
    }
}

function uploadHeroBg() {
    heroBgForm.post(route('admin.tampilan.hero-bg'), {
        forceFormData: true,
        onSuccess: () => { heroBgForm.reset(); },
    });
}

function deleteHeroBg() {
    if (confirm('Yakin hapus background hero?')) {
        router.delete(route('admin.tampilan.hero-bg.delete'));
        heroBgPreview.value = null;
    }
}

// Preset themes
const presets = [
    { name: 'Violet (Default)', primary: '#7c3aed', secondary: '#4f46e5', accent: '#d946ef' },
    { name: 'Biru Ocean', primary: '#2563eb', secondary: '#1d4ed8', accent: '#06b6d4' },
    { name: 'Hijau Emerald', primary: '#059669', secondary: '#047857', accent: '#10b981' },
    { name: 'Merah Rose', primary: '#e11d48', secondary: '#be123c', accent: '#f43f5e' },
    { name: 'Orange Sunset', primary: '#ea580c', secondary: '#c2410c', accent: '#f59e0b' },
    { name: 'Slate Professional', primary: '#475569', secondary: '#334155', accent: '#6366f1' },
    { name: 'Teal Modern', primary: '#0d9488', secondary: '#0f766e', accent: '#2dd4bf' },
    { name: 'Pink Sakura', primary: '#db2777', secondary: '#be185d', accent: '#f472b6' },
];

function applyPreset(preset) {
    form.warna_primary = preset.primary;
    form.warna_secondary = preset.secondary;
    form.warna_accent = preset.accent;
}

// AI Config
const aiForm = useForm({
    ai_provider: props.ai_config?.provider || 'deepseek',
    ai_api_key: props.ai_config?.api_key_masked || '',
    ai_model: props.ai_config?.model || '',
});

const aiProviders = [
    { value: 'deepseek', name: 'DeepSeek', desc: 'Murah & bagus Bahasa Indonesia ($0.14/1M tokens)', url: 'https://platform.deepseek.com', placeholder: 'sk-...' },
    { value: 'groq', name: 'Groq', desc: 'Gratis & super cepat (Llama 3.3 70B)', url: 'https://console.groq.com', placeholder: 'gsk_...' },
    { value: 'openrouter', name: 'OpenRouter', desc: 'Banyak model gratis (DeepSeek, Llama, dll)', url: 'https://openrouter.ai', placeholder: 'sk-or-...' },
    { value: 'anthropic', name: 'Anthropic (Claude)', desc: 'Premium, paling cerdas (berbayar)', url: 'https://console.anthropic.com', placeholder: 'sk-ant-...' },
];

const selectedProvider = computed(() => aiProviders.find(p => p.value === aiForm.ai_provider));
const aiKeySet = computed(() => props.ai_config?.api_key_set);

function submitAi() {
    aiForm.put(route('admin.tampilan.ai'));
}

const aiTestLoading = ref(false);
const aiTestResult = ref(null);

async function testAi() {
    aiTestLoading.value = true;
    aiTestResult.value = null;
    try {
        const res = await fetch(route('admin.tampilan.ai.test'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        });
        aiTestResult.value = await res.json();
    } catch {
        aiTestResult.value = { success: false, message: 'Gagal terhubung ke server.' };
    }
    aiTestLoading.value = false;
}
</script>

<template>
    <AdminLayout>
        <template #header>Tampilan & Branding</template>
        <Head title="Tampilan & Branding" />

        <div class="max-w-4xl space-y-6">
            <!-- Info Banner -->
            <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-violet-200/40 rounded-2xl p-5 flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                </div>
                <div>
                    <h3 class="font-bold text-violet-900 text-sm">Super Admin: Kustomisasi Tampilan</h3>
                    <p class="text-xs text-violet-700 mt-1">Ubah logo, warna, dan tampilan agar sesuai dengan identitas sekolah/instansi Anda. Perubahan langsung terlihat di seluruh halaman publik.</p>
                </div>
            </div>

            <!-- ===== LOGO ===== -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <h2 class="font-bold text-slate-900 text-lg mb-1">Logo Sekolah</h2>
                <p class="text-sm text-slate-500 mb-5">Upload logo PNG/JPG/SVG (maks 2MB). Ditampilkan di navbar dan footer.</p>

                <div class="flex items-start gap-6">
                    <div class="w-24 h-24 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                        <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain" alt="Logo" />
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div class="flex-1 space-y-3">
                        <input type="file" accept="image/*" @change="handleLogoChange" class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 file:cursor-pointer" />
                        <div v-if="form.errors.logo" class="form-error">{{ form.errors.logo }}</div>
                        <div class="flex gap-2">
                            <button v-if="logoForm.logo" @click="uploadLogo" :disabled="logoForm.processing" class="btn-primary text-sm">
                                {{ logoForm.processing ? 'Uploading...' : 'Upload Logo' }}
                            </button>
                            <button v-if="logoPreview" @click="deleteLogo" class="btn-danger text-sm">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== FAVICON ===== -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <h2 class="font-bold text-slate-900 text-lg mb-1">Favicon</h2>
                <p class="text-sm text-slate-500 mb-5">Icon kecil yang muncul di tab browser (PNG/ICO, maks 512KB).</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                        <img v-if="favicon_url" :src="favicon_url" class="w-8 h-8 object-contain" alt="Favicon" />
                        <span v-else class="text-slate-300 text-lg font-bold">P</span>
                    </div>
                    <input type="file" accept=".png,.ico,.svg" @change="uploadFavicon" class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer" />
                </div>
            </div>

            <!-- ===== WARNA TEMA ===== -->
            <form @submit.prevent="submitBranding">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                    <h2 class="font-bold text-slate-900 text-lg mb-1">Warna Tema</h2>
                    <p class="text-sm text-slate-500 mb-5">Pilih tema warna atau kustom sendiri. Warna diterapkan di seluruh halaman publik.</p>

                    <!-- Presets -->
                    <div class="mb-6">
                        <label class="form-label">Tema Cepat</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button
                                v-for="preset in presets"
                                :key="preset.name"
                                type="button"
                                @click="applyPreset(preset)"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl border border-slate-200 hover:border-violet-300 hover:bg-violet-50/50 transition-all text-left group"
                                :class="form.warna_primary === preset.primary && form.warna_secondary === preset.secondary ? 'border-violet-400 bg-violet-50 ring-1 ring-violet-400' : ''"
                            >
                                <div class="flex -space-x-1">
                                    <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm" :style="{ backgroundColor: preset.primary }"></div>
                                    <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm" :style="{ backgroundColor: preset.secondary }"></div>
                                    <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm" :style="{ backgroundColor: preset.accent }"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-600 truncate">{{ preset.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Custom colors -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="form-label">Warna Primary</label>
                            <div class="flex items-center gap-2">
                                <input v-model="form.warna_primary" type="color" class="w-10 h-10 rounded-lg cursor-pointer border border-slate-200" />
                                <input v-model="form.warna_primary" type="text" class="form-input font-mono text-sm" maxlength="7" />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Warna Secondary</label>
                            <div class="flex items-center gap-2">
                                <input v-model="form.warna_secondary" type="color" class="w-10 h-10 rounded-lg cursor-pointer border border-slate-200" />
                                <input v-model="form.warna_secondary" type="text" class="form-input font-mono text-sm" maxlength="7" />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Warna Accent</label>
                            <div class="flex items-center gap-2">
                                <input v-model="form.warna_accent" type="color" class="w-10 h-10 rounded-lg cursor-pointer border border-slate-200" />
                                <input v-model="form.warna_accent" type="text" class="form-input font-mono text-sm" maxlength="7" />
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="rounded-2xl border border-slate-200 overflow-hidden mb-6">
                        <div class="text-xs text-slate-400 font-medium px-4 py-2 bg-slate-50 border-b border-slate-100">PREVIEW</div>
                        <div class="p-6 space-y-4">
                            <div class="h-32 rounded-xl flex items-center justify-center text-white font-bold text-lg" :style="{ background: `linear-gradient(135deg, ${form.warna_primary}, ${form.warna_secondary})` }">
                                {{ pengaturan.nama_sekolah }}
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="px-4 py-2 rounded-xl text-white text-sm font-bold" :style="{ background: `linear-gradient(135deg, ${form.warna_primary}, ${form.warna_secondary})` }">Tombol Primary</button>
                                <button type="button" class="px-4 py-2 rounded-xl text-white text-sm font-bold" :style="{ backgroundColor: form.warna_accent }">Tombol Accent</button>
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-white" :style="{ backgroundColor: form.warna_primary }">Badge</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== INFO SEKOLAH ===== -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 mt-6">
                    <h2 class="font-bold text-slate-900 text-lg mb-1">Informasi Instansi</h2>
                    <p class="text-sm text-slate-500 mb-5">Identitas yang ditampilkan di halaman publik.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Singkatan / Inisial</label>
                            <input v-model="form.singkatan_sekolah" type="text" class="form-input" placeholder="Contoh: SMKN1" maxlength="10" />
                            <p class="text-xs text-slate-400 mt-1">Ditampilkan di logo jika belum upload gambar logo</p>
                        </div>
                        <div>
                            <label class="form-label">Email Sekolah</label>
                            <input v-model="form.email_sekolah" type="email" class="form-input" placeholder="info@sekolah.sch.id" />
                        </div>
                        <div>
                            <label class="form-label">Website</label>
                            <input v-model="form.website_sekolah" type="text" class="form-input" placeholder="https://sekolah.sch.id" />
                        </div>
                        <div>
                            <label class="form-label">Alamat</label>
                            <input v-model="form.alamat_sekolah" type="text" class="form-input" placeholder="Jl. Pendidikan No. 1" />
                        </div>
                    </div>
                </div>

                <!-- ===== HERO SECTION ===== -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 mt-6">
                    <h2 class="font-bold text-slate-900 text-lg mb-1">Hero Section</h2>
                    <p class="text-sm text-slate-500 mb-5">Kustomisasi tampilan hero di halaman utama.</p>

                    <!-- Hero BG -->
                    <div class="mb-5">
                        <label class="form-label">Background Image (opsional)</label>
                        <div class="flex items-start gap-4">
                            <div class="w-40 h-24 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0">
                                <img v-if="heroBgPreview" :src="heroBgPreview" class="w-full h-full object-cover" alt="Hero BG" />
                                <div v-else class="w-full h-full flex items-center justify-center text-slate-300 text-xs">No image</div>
                            </div>
                            <div class="space-y-2">
                                <input type="file" accept="image/*" @change="handleHeroBgChange" class="text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer" />
                                <div class="flex gap-2">
                                    <button v-if="heroBgForm.hero_bg" type="button" @click="uploadHeroBg" :disabled="heroBgForm.processing" class="btn-primary text-xs py-1.5 px-3">Upload</button>
                                    <button v-if="heroBgPreview" type="button" @click="deleteHeroBg" class="btn-danger text-xs py-1.5 px-3">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="form-label">Judul Hero (opsional)</label>
                            <input v-model="form.hero_title" type="text" class="form-input" placeholder="Biarkan kosong untuk default: 'Penerimaan Peserta Didik Baru'" />
                        </div>
                        <div>
                            <label class="form-label">Subtitle Hero (opsional)</label>
                            <input v-model="form.hero_subtitle" type="text" class="form-input" placeholder="Biarkan kosong untuk default" />
                        </div>
                    </div>
                </div>

                <!-- ===== FOOTER ===== -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 mt-6">
                    <h2 class="font-bold text-slate-900 text-lg mb-1">Footer</h2>
                    <p class="text-sm text-slate-500 mb-5">Teks tambahan di footer halaman publik.</p>
                    <div>
                        <label class="form-label">Teks Footer (opsional)</label>
                        <textarea v-model="form.footer_text" rows="3" class="form-input" placeholder="Contoh: Jl. Pendidikan No. 1 | Telp: 021-xxx | Email: info@sekolah.sch.id"></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end mt-6">
                    <button type="submit" :disabled="form.processing" class="btn-primary px-8">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Semua Pengaturan' }}
                    </button>
                </div>
            </form>

            <!-- ===== AI ASSISTANT ===== -->
            <form @submit.prevent="submitAi" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-500 text-white flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3" /></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">AI Assistant</h2>
                        <p class="text-sm text-slate-500">Konfigurasi provider AI untuk chatbot & form assistant</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-5 mb-6 p-4 rounded-xl flex items-center gap-3" :class="aiKeySet ? 'bg-emerald-50 border border-emerald-200/60' : 'bg-amber-50 border border-amber-200/60'">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="aiKeySet ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'">
                        <svg v-if="aiKeySet" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="text-sm" :class="aiKeySet ? 'text-emerald-800' : 'text-amber-800'">
                        <span v-if="aiKeySet"><strong>AI Aktif</strong> — Provider: {{ selectedProvider?.name }}, Key: {{ ai_config.api_key_masked }}</span>
                        <span v-else><strong>AI Belum Aktif</strong> — Masukkan API key di bawah untuk mengaktifkan chatbot</span>
                    </div>
                </div>

                <!-- Provider Selection -->
                <div class="mb-5">
                    <label class="form-label">Provider AI</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button
                            v-for="p in aiProviders"
                            :key="p.value"
                            type="button"
                            @click="aiForm.ai_provider = p.value"
                            class="text-left px-4 py-3 rounded-xl border transition-all"
                            :class="aiForm.ai_provider === p.value ? 'border-violet-400 bg-violet-50 ring-1 ring-violet-400' : 'border-slate-200 hover:border-slate-300'"
                        >
                            <div class="font-semibold text-sm text-slate-900">{{ p.name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ p.desc }}</div>
                        </button>
                    </div>
                </div>

                <!-- API Key -->
                <div class="mb-5">
                    <label class="form-label">API Key</label>
                    <input
                        v-model="aiForm.ai_api_key"
                        type="text"
                        class="form-input font-mono text-sm"
                        :placeholder="selectedProvider?.placeholder || 'Masukkan API key...'"
                    />
                    <p class="text-xs text-slate-400 mt-1.5">
                        Daftar gratis di
                        <a :href="selectedProvider?.url" target="_blank" class="text-violet-600 underline">{{ selectedProvider?.url }}</a>.
                        API key disimpan terenkripsi di database, tidak akan ditampilkan utuh.
                    </p>
                    <div v-if="aiForm.errors.ai_api_key" class="form-error">{{ aiForm.errors.ai_api_key }}</div>
                </div>

                <!-- Model Override -->
                <div class="mb-6">
                    <label class="form-label">Model (opsional)</label>
                    <input v-model="aiForm.ai_model" type="text" class="form-input text-sm" placeholder="Kosongkan untuk model default" />
                    <p class="text-xs text-slate-400 mt-1.5">
                        Default:
                        <span v-if="aiForm.ai_provider === 'deepseek'" class="font-mono">deepseek-chat</span>
                        <span v-else-if="aiForm.ai_provider === 'groq'" class="font-mono">llama-3.3-70b-versatile</span>
                        <span v-else-if="aiForm.ai_provider === 'openrouter'" class="font-mono">deepseek/deepseek-chat-v3-0324:free</span>
                        <span v-else class="font-mono">claude-haiku-4-5</span>
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button type="submit" :disabled="aiForm.processing" class="btn-primary">
                        {{ aiForm.processing ? 'Menyimpan...' : 'Simpan AI Config' }}
                    </button>
                    <button type="button" @click="testAi" :disabled="aiTestLoading" class="btn-secondary flex items-center gap-2">
                        <svg v-if="aiTestLoading" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ aiTestLoading ? 'Testing...' : 'Test Koneksi' }}
                    </button>
                </div>

                <!-- Test Result -->
                <div v-if="aiTestResult" class="mt-4 p-4 rounded-xl text-sm" :class="aiTestResult.success ? 'bg-emerald-50 border border-emerald-200/60 text-emerald-800' : 'bg-rose-50 border border-rose-200/60 text-rose-800'">
                    <div class="font-semibold">{{ aiTestResult.success ? 'Berhasil!' : 'Gagal' }}</div>
                    <div class="mt-1">{{ aiTestResult.message }}</div>
                    <div v-if="aiTestResult.reply" class="mt-2 p-3 bg-white rounded-lg border border-emerald-100 text-slate-700 text-xs">
                        <span class="text-slate-400">AI Response:</span> {{ aiTestResult.reply }}
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
