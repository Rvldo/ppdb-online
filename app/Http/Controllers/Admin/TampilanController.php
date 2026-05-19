<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPpdb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
