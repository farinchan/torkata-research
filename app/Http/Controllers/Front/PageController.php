<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SettingWebsite;

class PageController extends Controller
{
    public function terms()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Syarat & Ketentuan Layanan | ' . $setting_web->name,
            'meta' => [
                'title' => 'Syarat & Ketentuan Layanan | ' . $setting_web->name,
                'description' => 'Syarat dan ketentuan penggunaan layanan publikasi, penerbitan buku, dan etika karya ilmiah di ' . $setting_web->name,
                'keywords' => 'syarat dan ketentuan, terms of service, aturan publikasi, hak cipta karya, etika publikasi ilmiah, ' . $setting_web->name . ', padang',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('page.terms'),
            ],
            'breadcrumbs' => [
                ['name' => 'Beranda', 'link' => route('home')],
                ['name' => 'Syarat & Ketentuan', 'link' => route('page.terms')],
            ],
            'setting_web' => $setting_web,
        ];

        return view('front.pages.page.terms', $data);
    }

    public function privacy()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Kebijakan Privasi & Perlindungan Data | ' . $setting_web->name,
            'meta' => [
                'title' => 'Kebijakan Privasi & Perlindungan Data | ' . $setting_web->name,
                'description' => 'Kebijakan privasi dan perlindungan data pengguna, penulis, dan peneliti di ' . $setting_web->name,
                'keywords' => 'kebijakan privasi, privacy policy, perlindungan data penulis, privasi pengguna, keamanan akun, ' . $setting_web->name . ', padang',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('page.privacy'),
            ],
            'breadcrumbs' => [
                ['name' => 'Beranda', 'link' => route('home')],
                ['name' => 'Kebijakan Privasi', 'link' => route('page.privacy')],
            ],
            'setting_web' => $setting_web,
        ];

        return view('front.pages.page.privacy', $data);
    }

    public function faq()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'FAQ - Pertanyaan Yang Sering Diajukan | ' . $setting_web->name,
            'meta' => [
                'title' => 'FAQ - Pertanyaan Yang Sering Diajukan | ' . $setting_web->name,
                'description' => 'Pertanyaan yang sering diajukan seputar alur submit jurnal ilmiah, penerbitan buku ber-ISBN, dan layanan publikasi di ' . $setting_web->name,
                'keywords' => 'faq ' . $setting_web->name . ', tanya jawab publikasi, cara submit jurnal, cara terbitkan buku, biaya publikasi, bantuan pelanggan',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('page.faq'),
            ],
            'breadcrumbs' => [
                ['name' => 'Beranda', 'link' => route('home')],
                ['name' => 'FAQ', 'link' => route('page.faq')],
            ],
            'setting_web' => $setting_web,
            'list_faq' => Faq::active()->ordered()->get(),
        ];

        return view('front.pages.page.faq', $data);
    }
}
