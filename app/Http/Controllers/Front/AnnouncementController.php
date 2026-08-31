<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Pengumuman & Informasi Resmi | ' . $setting_web->name,
            'meta' => [
                'title' => 'Pengumuman & Informasi Resmi | ' . $setting_web->name,
                'description' => Str::limit('Pengumuman resmi, call for papers, dan edaran terbaru dari ' . $setting_web->name, 155),
                'keywords' => 'pengumuman resmi, call for papers, edaran akademik, informasi penerbitan, publikasi jurnal, ' . $setting_web->name . ', padang',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('announcement.index'),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Pengumuman',
                    'link' => route('announcement.index')
                ]
            ],
            'setting_web' => $setting_web,

            'list_announcement' => Announcement::latest()->paginate(10),
        ];

        return view('front.pages.announcement.index', $data);
    }

    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $announcement = Announcement::where('slug', $slug)->first();
        if (!$announcement) {
            abort(404);
        }
        $data = [
            'title' => $announcement->title . ' | ' . $setting_web->name,
            'meta' => [
                'title' => $announcement->title . ' | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($announcement->content), 155),
                'keywords' => $announcement->title . ', pengumuman resmi, surat edaran, informasi publikasi, ' . $setting_web->name,
                'favicon' => $announcement->image ?? $setting_web->favicon,
                'og_image' => $announcement->image ?? ($setting_web->logo ?? $setting_web->favicon),
                'og_type' => 'article',
                'robots' => 'index, follow',
                'canonical' => route('announcement.show', $announcement->slug),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Pengumuman',
                    'link' => route('announcement.index')
                ],
                [
                    'name' => Str::limit($announcement->title, 35),
                    'link' => route('announcement.show', $announcement->slug)
                ]
            ],
            'setting_web' => $setting_web,

            'announcement' => $announcement,
        ];

        return view('front.pages.announcement.show', $data);
    }
}
