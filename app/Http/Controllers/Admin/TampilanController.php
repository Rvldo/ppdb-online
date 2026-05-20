<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPpdb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TampilanController extends Controller
{
    public function index(): Response
    {
        $pengaturan = PengaturanPpdb::current();

        return Inertia::render('Admin/Tampilan', [
            'pengaturan' => $pengaturan,
            'logo_url' => $pengaturan->logo_path ? Storage::disk('public')->url($pengaturan->logo_path) : null,
            'favicon_url' => $pengaturan->favicon_path ? Storage::disk('public')->url($pengaturan->favicon_path) : null,
            'hero_bg_url' => $pengaturan->hero_bg_path ? Storage::disk('public')->url($pengaturan->hero_bg_path) : null,
            'ai_config' => [
                'provider' => $pengaturan->ai_provider ?: 'deepseek',
                'api_key_set' => ! empty($pengaturan->ai_api_key),
                'api_key_masked' => $pengaturan->ai_api_key
                    ? substr($pengaturan->ai_api_key, 0, 8) . '••••••••' . substr($pengaturan->ai_api_key, -4)
                    : '',
                'model' => $pengaturan->ai_model ?: '',
            ],
        ]);
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'singkatan_sekolah' => ['nullable', 'string', 'max:10'],
            'alamat_sekolah' => ['nullable', 'string', 'max:255'],
            'website_sekolah' => ['nullable', 'string', 'max:255'],
            'email_sekolah' => ['nullable', 'email', 'max:255'],
            'warna_primary' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'warna_secondary' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'warna_accent' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
        ]);

        PengaturanPpdb::current()->update($data);

        return back()->with('success', 'Pengaturan tampilan berhasil disimpan.');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $pengaturan = PengaturanPpdb::current();

        // Delete old logo
        if ($pengaturan->logo_path) {
            Storage::disk('public')->delete($pengaturan->logo_path);
        }

        $path = $request->file('logo')->store('branding', 'public');
        $pengaturan->update(['logo_path' => $path]);

        return back()->with('success', 'Logo berhasil diupload.');
    }

    public function deleteLogo(): RedirectResponse
    {
        $pengaturan = PengaturanPpdb::current();

        if ($pengaturan->logo_path) {
            Storage::disk('public')->delete($pengaturan->logo_path);
            $pengaturan->update(['logo_path' => null]);
        }

        return back()->with('success', 'Logo berhasil dihapus.');
    }

    public function uploadFavicon(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => ['required', 'image', 'mimes:png,ico,svg', 'max:512'],
        ]);

        $pengaturan = PengaturanPpdb::current();

        if ($pengaturan->favicon_path) {
            Storage::disk('public')->delete($pengaturan->favicon_path);
        }

        $path = $request->file('favicon')->store('branding', 'public');
        $pengaturan->update(['favicon_path' => $path]);

        return back()->with('success', 'Favicon berhasil diupload.');
    }

    public function uploadHeroBg(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_bg' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $pengaturan = PengaturanPpdb::current();

        if ($pengaturan->hero_bg_path) {
            Storage::disk('public')->delete($pengaturan->hero_bg_path);
        }

        $path = $request->file('hero_bg')->store('branding', 'public');
        $pengaturan->update(['hero_bg_path' => $path]);

        return back()->with('success', 'Background hero berhasil diupload.');
    }

    public function deleteHeroBg(): RedirectResponse
    {
        $pengaturan = PengaturanPpdb::current();

        if ($pengaturan->hero_bg_path) {
            Storage::disk('public')->delete($pengaturan->hero_bg_path);
            $pengaturan->update(['hero_bg_path' => null]);
        }

        return back()->with('success', 'Background hero berhasil dihapus.');
    }

    public function updateAi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ai_provider' => ['required', 'in:deepseek,groq,openrouter,anthropic'],
            'ai_api_key' => ['nullable', 'string', 'max:500'],
            'ai_model' => ['nullable', 'string', 'max:100'],
        ]);

        $pengaturan = PengaturanPpdb::current();

        // Jika api_key kosong / hanya mask, jangan overwrite yang lama
        if (empty($data['ai_api_key']) || str_contains($data['ai_api_key'], '••••')) {
            unset($data['ai_api_key']);
        }

        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan AI berhasil disimpan.');
    }

    public function testAi(): JsonResponse
    {
        $pengaturan = PengaturanPpdb::current();
        $apiKey = $pengaturan->ai_api_key ?: config('services.ai.api_key') ?: config('services.anthropic.api_key');

        if (! $apiKey) {
            return response()->json(['success' => false, 'message' => 'API key belum diisi.']);
        }

        $provider = $pengaturan->ai_provider ?: 'deepseek';

        try {
            // Direct API call to test connection
            $config = match ($provider) {
                'deepseek' => ['url' => 'https://api.deepseek.com/chat/completions', 'model' => $pengaturan->ai_model ?: 'deepseek-chat'],
                'groq' => ['url' => 'https://api.groq.com/openai/v1/chat/completions', 'model' => $pengaturan->ai_model ?: 'llama-3.3-70b-versatile'],
                'openrouter' => ['url' => 'https://openrouter.ai/api/v1/chat/completions', 'model' => $pengaturan->ai_model ?: 'deepseek/deepseek-chat-v3-0324:free'],
                default => null,
            };

            if ($provider === 'anthropic') {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                    'model' => $pengaturan->ai_model ?: 'claude-haiku-4-5-20251001',
                    'max_tokens' => 64,
                    'messages' => [['role' => 'user', 'content' => 'Jawab hanya: "AI aktif!"']],
                ]);

                if ($response->failed()) {
                    $err = $response->json('error.message') ?: $response->body();
                    return response()->json(['success' => false, 'message' => "Anthropic error: {$err}"]);
                }

                $reply = $response->json('content.0.text', '');
                return response()->json(['success' => true, 'message' => 'AI terhubung!', 'reply' => $reply]);
            }

            // OpenAI-compatible providers
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($config['url'], [
                'model' => $config['model'],
                'max_tokens' => 64,
                'messages' => [
                    ['role' => 'user', 'content' => 'Jawab hanya: "AI aktif!"'],
                ],
            ]);

            if ($response->failed()) {
                $err = $response->json('error.message') ?: $response->json('message') ?: $response->body();
                return response()->json(['success' => false, 'message' => "{$provider} error: {$err}"]);
            }

            $reply = $response->json('choices.0.message.content', '');
            return response()->json(['success' => true, 'message' => 'AI terhubung!', 'reply' => $reply]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
