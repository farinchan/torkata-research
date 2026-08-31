<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MenuProfil;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuProfilController extends Controller
{


    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $menu_profil = MenuProfil::where('slug', $slug)->first();
        if (!$menu_profil) {
            abort(404);
        }
        $data = [
            'title' => $menu_profil->name . ' | ' . $setting_web->name,
            'meta' => [
                'title' => $menu_profil->name . ' | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($menu_profil->content), 155),
                'keywords' => $menu_profil->name . ', profil lembaga, tentang kami, visi misi, ' . $setting_web->name . ', padang, sumatera barat',
                'favicon' => $menu_profil->image ?? $setting_web->favicon,
                'og_image' => $menu_profil->image ?? ($setting_web->logo ?? $setting_web->favicon),
                'og_type' => 'article',
                'robots' => 'index, follow',
                'canonical' => route('profil.show', $menu_profil->slug),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => $menu_profil->name,
                    'link' => route('profil.show', $menu_profil->slug)
                ]
            ],
            'setting_web' => $setting_web,

            'menu_profil' => $menu_profil,
        ];

        return view('front.pages.menu_profil.show', $data);
    }
}
