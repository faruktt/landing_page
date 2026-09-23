<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::current();

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = Setting::current();

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'dhaka_delivery_charge' => ['required', 'numeric', 'min:0'],
            'default_delivery_charge' => ['required', 'numeric', 'min:0'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:50'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'landing_announcement' => ['nullable', 'string', 'max:500'],
            'short_text' => ['nullable', 'string', 'max:500'],
            'landing_badge_1' => ['nullable', 'string', 'max:100'],
            'landing_badge_2' => ['nullable', 'string', 'max:100'],
            'landing_badge_3' => ['nullable', 'string', 'max:100'],
            'landing_offer_title' => ['nullable', 'string', 'max:255'],
            'landing_offer_subtitle' => ['nullable', 'string', 'max:255'],
            'landing_benefits_title' => ['nullable', 'string', 'max:255'],
            'landing_benefits_subtitle' => ['nullable', 'string', 'max:500'],
            'landing_reviews_title' => ['nullable', 'string', 'max:255'],
            'landing_order_title' => ['nullable', 'string', 'max:255'],
            'landing_order_btn_text' => ['nullable', 'string', 'max:255'],
            'landing_guarantee_note' => ['nullable', 'string', 'max:500'],
            'landing_p1_title' => ['nullable', 'string', 'max:255'],
            'landing_p1_badge_1' => ['nullable', 'string', 'max:100'],
            'landing_p1_badge_2' => ['nullable', 'string', 'max:100'],
            'landing_p1_offer_text' => ['nullable', 'string', 'max:255'],
            'landing_p1_regular_price' => ['nullable', 'string', 'max:50'],
            'landing_p1_sale_price' => ['nullable', 'string', 'max:50'],
            'landing_p1_bullets' => ['nullable', 'string'],
            'landing_p2_title' => ['nullable', 'string', 'max:255'],
            'landing_p2_badge_1' => ['nullable', 'string', 'max:100'],
            'landing_p2_badge_2' => ['nullable', 'string', 'max:100'],
            'landing_p2_regular_price' => ['nullable', 'string', 'max:50'],
            'landing_p2_sale_price' => ['nullable', 'string', 'max:50'],
            'landing_p2_bullets' => ['nullable', 'string'],
            'landing_benefits_list' => ['nullable', 'string'],
            'landing_outlook_btn_text' => ['nullable', 'string', 'max:100'],
            'landing_review_btn_text' => ['nullable', 'string', 'max:100'],
            'landing_p1_image_1' => ['nullable', 'image', 'max:3072'],
            'landing_p1_image_2' => ['nullable', 'image', 'max:3072'],
            'landing_p2_image' => ['nullable', 'image', 'max:3072'],
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo && file_exists(public_path($setting->logo))) {
                unlink(public_path($setting->logo));
            }
        
            $file = $request->file('logo');
            $filename = time() . '_logo_' . $file->getClientOriginalName();
            $file->move(public_path('storage/setting'), $filename);
            $data['logo'] = 'storage/setting/' . $filename;
        }
        
        if ($request->hasFile('favicon')) {
            if ($setting->favicon && file_exists(public_path($setting->favicon))) {
                unlink(public_path($setting->favicon));
            }
        
            $file = $request->file('favicon');
            $filename = time() . '_favicon_' . $file->getClientOriginalName();
            $file->move(public_path('storage/setting'), $filename);
            $data['favicon'] = 'storage/setting/' . $filename;
        }

        if ($request->hasFile('landing_p1_image_1')) {
            if ($setting->landing_p1_image_1 && file_exists(public_path($setting->landing_p1_image_1))) {
                unlink(public_path($setting->landing_p1_image_1));
            }
        
            $file = $request->file('landing_p1_image_1');
            $filename = time() . '_p1_1_' . $file->getClientOriginalName();
            $file->move(public_path('storage/setting'), $filename);
            $data['landing_p1_image_1'] = 'setting/' . $filename;
        }
        
        if ($request->hasFile('landing_p1_image_2')) {
            if ($setting->landing_p1_image_2 && file_exists(public_path($setting->landing_p1_image_2))) {
                unlink(public_path($setting->landing_p1_image_2));
            }
        
            $file = $request->file('landing_p1_image_2');
            $filename = time() . '_p1_2_' . $file->getClientOriginalName();
            $file->move(public_path('storage/setting'), $filename);
            $data['landing_p1_image_2'] = 'setting/' . $filename;
        }
        
        if ($request->hasFile('landing_p2_image')) {
            if ($setting->landing_p2_image && file_exists(public_path($setting->landing_p2_image))) {
                unlink(public_path($setting->landing_p2_image));
            }
        
            $file = $request->file('landing_p2_image');
            $filename = time() . '_p2_' . $file->getClientOriginalName();
            $file->move(public_path('storage/setting'), $filename);
            $data['landing_p2_image'] = 'setting/' . $filename;
        }

        $setting->update($data);

        return redirect()->route('admin.settings.edit')->with('status', 'সেটিংস সফলভাবে আপডেট হয়েছে।');
    }
}
