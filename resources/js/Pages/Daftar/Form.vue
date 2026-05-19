<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AiFormTip from '@/Components/AiFormTip.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    pengaturan: Object,
    jurusan: Array,
});

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    nama_lengkap: '',
    nisn: '',
    nik: '',
    jenis_kelamin: 'L',
    tempat_lahir: '',
    tanggal_lahir: '',
    agama: 'Islam',
    alamat: '',
    no_hp: '',
    email: '',
    asal_sekolah: '',
    tahun_lulus: new Date().getFullYear().toString(),
    nilai_rata_rata: '',
    nama_ayah: '',
    nama_ibu: '',
    pekerjaan_ayah: '',
    pekerjaan_ibu: '',
    no_hp_ortu: '',
    jurusan_id: '',
    berkas: null,
});

const aiTipRefs = ref({});

function submit() {
    form.post(route('ppdb.store'), { forceFormData: true });
}

function nextStep() { if (currentStep.value < totalSteps) currentStep.value++; }
function prevStep() { if (currentStep.value > 1) currentStep.value--; }

const stepConfig = [
    { label: 'Data Pribadi', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', color: 'violet' },
    { label: 'Data Akademik', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', color: 'indigo' },
    { label: 'Data Orang Tua', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'fuchsia' },
    { label: 'Berkas & Kirim', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', color: 'emerald' },
];

// Completion progress
const completionPct = computed(() => {
    let filled = 0;
    let total = 12;
    const required = ['nama_lengkap','jenis_kelamin','tempat_lahir','tanggal_lahir','agama','alamat','no_hp','asal_sekolah','tahun_lulus','nama_ayah','nama_ibu','no_hp_ortu'];
    required.forEach(k => { if (form[k]) filled++; });
    if (form.jurusan_id) filled++;
    return Math.round((filled / (total + 1)) * 100);
});
</script>

<template>
    <PublicLayout>
        <Head title="Form Pendaftaran" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-950 via-indigo-950 to-slate-900"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-violet-600/10 rounded-full blur-[100px]"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-32">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 bg-white/[0.07] backdrop-blur-sm border border-white/[0.08] px-3 py-1.5 rounded-full text-xs font-medium text-violet-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.301 24.301 0 014.5 0" /></svg>
                        AI-Assisted Form
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-white">Formulir Pendaftaran</h1>
                <p class="text-slate-300 mt-3 text-lg">Isi data dengan lengkap dan benar. AI assistant akan membantumu mengisi form.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 pb-16">
            <!-- Step Indicator -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100/80 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div v-for="(step, i) in stepConfig" :key="i" class="flex items-center" :class="i < stepConfig.length - 1 ? 'flex-1' : ''">
                        <button @click="currentStep = i + 1" class="flex items-center gap-2 group">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                :class="currentStep > i + 1
                                    ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20'
                                    : currentStep === i + 1
                                        ? 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25'
                                        : 'bg-slate-100 text-slate-400'"
                            >
                                <svg v-if="currentStep > i + 1" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" :d="step.icon" /></svg>
                            </div>
                            <span class="text-xs font-semibold hidden sm:inline" :class="currentStep === i + 1 ? 'text-violet-700' : currentStep > i + 1 ? 'text-emerald-600' : 'text-slate-400'">{{ step.label }}</span>
                        </button>
                        <div v-if="i < stepConfig.length - 1" class="flex-1 h-0.5 mx-3 rounded-full transition-colors" :class="currentStep > i + 1 ? 'bg-emerald-400' : 'bg-slate-100'"></div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full transition-all duration-700 ease-out" :style="{ width: completionPct + '%' }"></div>
                </div>
                <div class="text-right mt-1">
                    <span class="text-[11px] text-slate-400 font-medium">{{ completionPct }}% terisi</span>
                </div>
            </div>

            <form @submit.prevent="submit">
                <!-- Step 1: Data Pribadi -->
                <section v-show="currentStep === 1" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100/80 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        Data Pribadi
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="form-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input v-model="form.nama_lengkap" type="text" class="form-input" required placeholder="Sesuai ijazah/akte kelahiran" />
                            <div v-if="form.errors.nama_lengkap" class="form-error">{{ form.errors.nama_lengkap }}</div>
                            <AiFormTip field="Nama Lengkap" :value="form.nama_lengkap" context="Pendaftaran siswa baru SMK" ref="tipNama" />
                        </div>
                        <div>
                            <label class="form-label">NISN</label>
                            <input v-model="form.nisn" type="text" class="form-input" placeholder="10 digit" maxlength="20" />
                            <div v-if="form.errors.nisn" class="form-error">{{ form.errors.nisn }}</div>
                        </div>
                        <div>
                            <label class="form-label">NIK</label>
                            <input v-model="form.nik" type="text" class="form-input" placeholder="16 digit" maxlength="20" />
                            <div v-if="form.errors.nik" class="form-error">{{ form.errors.nik }}</div>
                        </div>
                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select v-model="form.jenis_kelamin" class="form-input" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Agama <span class="text-rose-500">*</span></label>
                            <select v-model="form.agama" class="form-input" required>
                                <option>Islam</option><option>Kristen</option><option>Katolik</option>
                                <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tempat Lahir <span class="text-rose-500">*</span></label>
                            <input v-model="form.tempat_lahir" type="text" class="form-input" required placeholder="Contoh: Jakarta" />
                            <div v-if="form.errors.tempat_lahir" class="form-error">{{ form.errors.tempat_lahir }}</div>
                        </div>
                        <div>
                            <label class="form-label">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input v-model="form.tanggal_lahir" type="date" class="form-input" required />
                            <div v-if="form.errors.tanggal_lahir" class="form-error">{{ form.errors.tanggal_lahir }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Alamat Lengkap <span class="text-rose-500">*</span></label>
                            <textarea v-model="form.alamat" rows="2" class="form-input" required placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota"></textarea>
                            <div v-if="form.errors.alamat" class="form-error">{{ form.errors.alamat }}</div>
                        </div>
                        <div>
                            <label class="form-label">No. HP <span class="text-rose-500">*</span></label>
                            <input v-model="form.no_hp" type="tel" class="form-input" required placeholder="08xxxxxxxxxx" />
                            <div v-if="form.errors.no_hp" class="form-error">{{ form.errors.no_hp }}</div>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input v-model="form.email" type="email" class="form-input" placeholder="email@contoh.com" />
                            <div v-if="form.errors.email" class="form-error">{{ form.errors.email }}</div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <button type="button" @click="nextStep" class="px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all flex items-center gap-2 active:scale-[0.98]">
                            Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </div>
                </section>

                <!-- Step 2: Data Akademik -->
                <section v-show="currentStep === 2" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100/80 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        Data Akademik
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="form-label">Asal Sekolah <span class="text-rose-500">*</span></label>
                            <input v-model="form.asal_sekolah" type="text" class="form-input" required placeholder="Contoh: SMP Negeri 1 Jakarta" />
                            <div v-if="form.errors.asal_sekolah" class="form-error">{{ form.errors.asal_sekolah }}</div>
                        </div>
                        <div>
                            <label class="form-label">Tahun Lulus <span class="text-rose-500">*</span></label>
                            <input v-model="form.tahun_lulus" type="number" min="2000" max="2099" class="form-input" required />
                            <div v-if="form.errors.tahun_lulus" class="form-error">{{ form.errors.tahun_lulus }}</div>
                        </div>
                        <div>
                            <label class="form-label">Nilai Rata-rata</label>
                            <input v-model="form.nilai_rata_rata" type="number" step="0.01" min="0" max="100" class="form-input" placeholder="0 - 100" />
                            <div v-if="form.errors.nilai_rata_rata" class="form-error">{{ form.errors.nilai_rata_rata }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Pilihan Jurusan <span class="text-rose-500">*</span></label>
                            <select v-model="form.jurusan_id" class="form-input" required>
                                <option value="">-- Pilih Jurusan --</option>
                                <option v-for="j in jurusan" :key="j.id" :value="j.id" :disabled="j.sisa_kuota === 0">
                                    {{ j.kode }} - {{ j.nama }} ({{ j.sisa_kuota === 0 ? 'PENUH' : 'sisa ' + j.sisa_kuota + '/' + j.kuota }})
                                </option>
                            </select>
                            <div v-if="form.errors.jurusan_id" class="form-error">{{ form.errors.jurusan_id }}</div>
                            <AiFormTip field="Pilihan Jurusan" :value="jurusan.find(j => j.id === form.jurusan_id)?.nama || ''" context="Siswa memilih jurusan SMK. Jurusan tersedia: RPL, TKJ, MM, AKL, BDP" />
                        </div>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="prevStep" class="px-6 py-3 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all flex items-center gap-2 active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                            Kembali
                        </button>
                        <button type="button" @click="nextStep" class="px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all flex items-center gap-2 active:scale-[0.98]">
                            Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </div>
                </section>

                <!-- Step 3: Data Orang Tua -->
                <section v-show="currentStep === 3" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100/80 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-fuchsia-100 text-fuchsia-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        Data Orang Tua / Wali
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">Nama Ayah <span class="text-rose-500">*</span></label>
                            <input v-model="form.nama_ayah" type="text" class="form-input" required />
                            <div v-if="form.errors.nama_ayah" class="form-error">{{ form.errors.nama_ayah }}</div>
                        </div>
                        <div>
                            <label class="form-label">Nama Ibu <span class="text-rose-500">*</span></label>
                            <input v-model="form.nama_ibu" type="text" class="form-input" required />
                            <div v-if="form.errors.nama_ibu" class="form-error">{{ form.errors.nama_ibu }}</div>
                        </div>
                        <div>
                            <label class="form-label">Pekerjaan Ayah</label>
                            <input v-model="form.pekerjaan_ayah" type="text" class="form-input" placeholder="Contoh: PNS, Wiraswasta" />
                        </div>
                        <div>
                            <label class="form-label">Pekerjaan Ibu</label>
                            <input v-model="form.pekerjaan_ibu" type="text" class="form-input" placeholder="Contoh: Guru, Ibu Rumah Tangga" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">No. HP Orang Tua <span class="text-rose-500">*</span></label>
                            <input v-model="form.no_hp_ortu" type="tel" class="form-input" required placeholder="08xxxxxxxxxx" />
                            <div v-if="form.errors.no_hp_ortu" class="form-error">{{ form.errors.no_hp_ortu }}</div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="prevStep" class="px-6 py-3 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all flex items-center gap-2 active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                            Kembali
                        </button>
                        <button type="button" @click="nextStep" class="px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all flex items-center gap-2 active:scale-[0.98]">
                            Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </div>
                </section>

                <!-- Step 4: Berkas & Submit -->
                <section v-show="currentStep === 4" class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100/80 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        Berkas Pendukung
                    </h2>
                    <div class="mb-6">
                        <label class="form-label">Upload Berkas (PDF/JPG/PNG, maks 5MB)</label>
                        <div class="mt-2 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-violet-300 hover:bg-violet-50/30 transition-all duration-300 cursor-pointer group">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 group-hover:bg-violet-100 flex items-center justify-center mb-3 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-300 group-hover:text-violet-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            </div>
                            <input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                @input="form.berkas = $event.target.files[0]"
                                class="text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 file:transition-colors file:cursor-pointer"
                            />
                            <p class="text-xs text-slate-400 mt-3">Gabung berkas (ijazah/akte/KK) ke dalam 1 file PDF</p>
                        </div>
                        <div v-if="form.errors.berkas" class="form-error">{{ form.errors.berkas }}</div>
                        <div v-if="form.progress" class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full transition-all" :style="{ width: form.progress.percentage + '%' }"></div>
                        </div>
                    </div>

                    <!-- Review summary -->
                    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-violet-100/60 rounded-2xl p-6 mb-6">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-violet-900">Ringkasan Data</div>
                                <p class="text-xs text-violet-700 mt-0.5">Pastikan semua data sudah benar sebelum mengirim</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs">
                            <div><span class="text-slate-500">Nama:</span> <span class="font-medium text-slate-700">{{ form.nama_lengkap || '-' }}</span></div>
                            <div><span class="text-slate-500">NISN:</span> <span class="font-medium text-slate-700">{{ form.nisn || '-' }}</span></div>
                            <div><span class="text-slate-500">Asal Sekolah:</span> <span class="font-medium text-slate-700">{{ form.asal_sekolah || '-' }}</span></div>
                            <div><span class="text-slate-500">Jurusan:</span> <span class="font-medium text-slate-700">{{ jurusan.find(j => j.id === form.jurusan_id)?.nama || '-' }}</span></div>
                            <div><span class="text-slate-500">No. HP:</span> <span class="font-medium text-slate-700">{{ form.no_hp || '-' }}</span></div>
                            <div><span class="text-slate-500">No. HP Ortu:</span> <span class="font-medium text-slate-700">{{ form.no_hp_ortu || '-' }}</span></div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="prevStep" class="px-6 py-3 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all flex items-center gap-2 active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                            Kembali
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-8 py-3.5 rounded-xl font-black bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:from-emerald-600 hover:to-teal-600 disabled:opacity-50 transition-all flex items-center gap-2 active:scale-[0.98]">
                            <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <svg v-else class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pendaftaran' }}
                        </button>
                    </div>
                </section>
            </form>
        </div>
    </PublicLayout>
</template>
