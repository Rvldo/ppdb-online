<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\PengaturanPpdb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Provider configs: endpoint, headers, body format.
     */
    private function getProviderConfig(): array
    {
        $pengaturan = PengaturanPpdb::current();

        // Prioritas: database > .env
        $provider = $pengaturan->ai_provider ?: config('services.ai.provider', 'deepseek');
        $apiKey = $pengaturan->ai_api_key ?: config('services.ai.api_key');
        $model = $pengaturan->ai_model ?: config('services.ai.model');

        // Fallback ke Anthropic lama
        if (! $apiKey && config('services.anthropic.api_key')) {
            $provider = 'anthropic';
            $apiKey = config('services.anthropic.api_key');
        }

        return match ($provider) {
            'deepseek' => [
                'url' => 'https://api.deepseek.com/chat/completions',
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'model' => $model ?: 'deepseek-chat',
                'format' => 'openai',
            ],
            'groq' => [
                'url' => 'https://api.groq.com/openai/v1/chat/completions',
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'model' => $model ?: 'llama-3.3-70b-versatile',
                'format' => 'openai',
            ],
            'openrouter' => [
                'url' => 'https://openrouter.ai/api/v1/chat/completions',
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'model' => $model ?: 'deepseek/deepseek-chat-v3-0324:free',
                'format' => 'openai',
            ],
            'anthropic' => [
                'url' => 'https://api.anthropic.com/v1/messages',
                'headers' => [
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'model' => $model ?: 'claude-haiku-4-5-20251001',
                'format' => 'anthropic',
            ],
            default => throw new \RuntimeException("AI provider '{$provider}' tidak dikenal."),
        };
    }

    /**
     * Send request sesuai format provider.
     */
    private string $lastError = '';

    private function callAi(string $systemPrompt, array $messages, int $maxTokens = 768): ?string
    {
        $config = $this->getProviderConfig();

        if ($config['format'] === 'openai') {
            // OpenAI-compatible (DeepSeek, Groq, OpenRouter)
            $body = [
                'model' => $config['model'],
                'max_tokens' => $maxTokens,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ...$messages,
                ],
            ];

            $response = Http::withHeaders($config['headers'])
                ->timeout(30)
                ->post($config['url'], $body);

            if ($response->failed()) {
                $this->lastError = $response->json('error.message')
                    ?: $response->json('message')
                    ?: "HTTP {$response->status()}: {$response->body()}";
                \Illuminate\Support\Facades\Log::error('AI API failed', [
                    'provider' => $config['model'],
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('choices.0.message.content');
        }

        // Anthropic format
        $body = [
            'model' => $config['model'],
            'max_tokens' => $maxTokens,
            'system' => $systemPrompt,
            'messages' => $messages,
        ];

        $response = Http::withHeaders($config['headers'])
            ->timeout(30)
            ->post($config['url'], $body);

        if ($response->failed()) {
            $this->lastError = $response->json('error.message')
                ?: $response->json('message')
                ?: "HTTP {$response->status()}: {$response->body()}";
            \Illuminate\Support\Facades\Log::error('AI API failed', [
                'provider' => 'anthropic',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json('content.0.text');
    }

    /**
     * Build system prompt with PPDB context.
     */
    private function buildSystemPrompt(): string
    {
        $pengaturan = PengaturanPpdb::current();
        $jurusan = Jurusan::where('aktif', true)
            ->withCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')])
            ->get(['id', 'kode', 'nama', 'deskripsi', 'kuota']);

        $jurusanInfo = $jurusan->map(fn ($j) => "- {$j->kode}: {$j->nama} (kuota: {$j->kuota}, diterima: {$j->diterima_count}, sisa: " . max(0, $j->kuota - $j->diterima_count) . ") — {$j->deskripsi}")->implode("\n");

        $stats = [
            'total' => Pendaftar::count(),
            'pending' => Pendaftar::where('status', 'pending')->count(),
            'diterima' => Pendaftar::where('status', 'diterima')->count(),
        ];

        $statusBuka = $pengaturan->isOpen() ? 'SEDANG DIBUKA' : 'DITUTUP';
        $hasilPengumuman = $pengaturan->hasil_diumumkan
            ? 'SUDAH diumumkan pada ' . $pengaturan->tanggal_pengumuman?->format('d M Y')
            : 'BELUM diumumkan';

        return <<<PROMPT
Kamu adalah "PPDB AI Assistant" — asisten virtual cerdas untuk {$pengaturan->nama_sekolah}.
Kamu ramah, informatif, dan selalu menjawab dalam Bahasa Indonesia yang sopan.

═══ INFORMASI PPDB ═══
• Nama Sekolah: {$pengaturan->nama_sekolah}
• Tahun Ajaran: {$pengaturan->tahun_ajaran}
• Periode Pendaftaran: {$pengaturan->tanggal_buka?->format('d M Y')} s/d {$pengaturan->tanggal_tutup?->format('d M Y')}
• Status: {$statusBuka}
• Contact Person: {$pengaturan->contact_person}
• Total Pendaftar saat ini: {$stats['total']} orang
• Menunggu verifikasi: {$stats['pending']} orang
• Sudah diterima: {$stats['diterima']} orang

═══ SYARAT PENDAFTARAN ═══
{$pengaturan->syarat_pendaftaran}

═══ JURUSAN TERSEDIA ═══
{$jurusanInfo}

═══ CARA MENDAFTAR ═══
1. Buka halaman "Daftar Sekarang" di website
2. Isi formulir: Data Pribadi → Data Akademik → Data Orang Tua → Upload Berkas
3. Klik "Kirim Pendaftaran"
4. Simpan nomor pendaftaran (format: PPDB-YYYY-XXXXX)
5. Gunakan nomor tersebut untuk cek status di halaman "Cek Status"

═══ PANDUAN MENJAWAB ═══
- Jawab singkat, jelas, dan ramah — maksimal 3 paragraf
- Gunakan bullet points jika perlu
- Jika ditanya tentang jurusan tertentu, beri info detail kuota dan deskripsinya
- Hasil pengumuman: {$hasilPengumuman}
- Jika ditanya hal di luar PPDB, jawab singkat lalu arahkan kembali ke PPDB
- JANGAN mengarang informasi yang tidak ada di data di atas
PROMPT;
    }

    /**
     * AI Chat with conversation history.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        $pengaturan = PengaturanPpdb::current();
        $apiKey = $pengaturan->ai_api_key ?: config('services.ai.api_key') ?: config('services.anthropic.api_key');
        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'AI assistant belum dikonfigurasi.',
            ], 503);
        }

        $messages = [];
        foreach ($request->input('history', []) as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        try {
            $reply = $this->callAi($this->buildSystemPrompt(), $messages);

            if (! $reply) {
                return response()->json([
                    'success' => false,
                    'message' => $this->lastError ?: 'Gagal menghubungi AI assistant.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'data' => ['reply' => $reply],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada AI assistant.',
            ], 500);
        }
    }

    /**
     * AI form assistant - tips singkat per field.
     */
    public function formAssist(Request $request): JsonResponse
    {
        $request->validate([
            'field' => ['required', 'string', 'max:50'],
            'value' => ['nullable', 'string', 'max:500'],
            'context' => ['nullable', 'string', 'max:1000'],
        ]);

        $pengaturan = PengaturanPpdb::current();
        $apiKey = $pengaturan->ai_api_key ?: config('services.ai.api_key') ?: config('services.anthropic.api_key');
        if (! $apiKey) {
            return response()->json(['success' => false, 'message' => 'AI belum dikonfigurasi.'], 503);
        }

        $field = $request->input('field');
        $value = $request->input('value', '');
        $context = $request->input('context', '');

        $systemPrompt = "Kamu adalah form assistant untuk pendaftaran PPDB. Tugasmu membantu pengguna mengisi formulir pendaftaran. Jawab HANYA dalam 1-2 kalimat singkat dalam Bahasa Indonesia. Jangan gunakan markdown. Jawab langsung tanpa basa-basi.";

        $userPrompt = "Field: \"{$field}\"\nNilai: \"{$value}\"\nKonteks: {$context}\n\nBerikan tips singkat untuk mengisi field ini dengan benar.";

        try {
            $reply = $this->callAi($systemPrompt, [['role' => 'user', 'content' => $userPrompt]], 128);

            if (! $reply) {
                return response()->json(['success' => false], 502);
            }

            return response()->json([
                'success' => true,
                'data' => ['tip' => $reply],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
