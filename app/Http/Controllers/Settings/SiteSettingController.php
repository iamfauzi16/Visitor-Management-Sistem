<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/SiteSetting', [
            'settings' => SiteSetting::allSettings(),
        ]);
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        SiteSetting::set('site_name', $request->validated('site_name'));
        SiteSetting::set('primary_color', $request->validated('primary_color'));

        if ($request->boolean('remove_logo')) {
            $oldPath = SiteSetting::get('logo_path');
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            SiteSetting::set('logo_path', null);
        }

        if ($request->hasFile('logo')) {
            $oldPath = SiteSetting::get('logo_path');
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('logos', 'public');
            SiteSetting::set('logo_path', $path);
        }

        return redirect()->route('site-settings.edit')
            ->with('success', 'Pengaturan situs berhasil disimpan.');
    }
}
