<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Journal;
use App\Models\News;
use App\Models\SettingWebsite;
use App\Models\Visitor;
use Illuminate\Support\Str;
use Jenssegers\Agent\Facades\Agent;
use Stevebauman\Location\Facades\Location;

class HomeController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $siteName = $setting_web->name ?? 'Nagari Sastra';
        $defaultDesc = 'Nagari Sastra - Lembaga Riset, Publikasi Jurnal Ilmiah Terakreditasi, Penerbitan Buku Ber-ISBN, dan Pusat Pendidikan di Indonesia.';

        $data = [
            'title' => $siteName . ' - Publikasi Jurnal Ilmiah, Penerbitan Buku & Riset',
            'meta' => [
                'title' => $siteName . ' - Publikasi Jurnal Ilmiah, Penerbitan Buku & Riset',
                'description' => !empty($setting_web->about) ? Str::limit(strip_tags($setting_web->about), 155) : $defaultDesc,
                'keywords' => 'Nagari Sastra, publikasi jurnal ilmiah, penerbitan buku ber-isbn, jurnal terakreditasi sinta, open journal systems, call for papers, layanan riset akademik, konsultasi penelitian, workshop penulisan karya ilmiah, penerbit padang, sumatera barat, indonesia',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('home'),
            ],
            'setting_web' => $setting_web,
            'list_news' => News::latest()->where('status', 'published')->limit(10)->get(),
            'list_journal' => Journal::limit(3)->get(),
            'list_announcement' => Announcement::latest()->where('is_active', true)->limit(6)->get(),
            'list_event' => Event::latest()->where('is_active', true)->where('access', 'terbuka')->limit(8)->get(),

            // Dynamic Stats
            'count_book' => \App\Models\Book::count(),
            'count_submission_published' => \App\Models\Submission::where('status', '3')->orWhere('status', 'published')->orWhere('status_label', 'Published')->count(),
            'count_journal' => \App\Models\Journal::count(),

            // Latest Books
            'list_book' => \App\Models\Book::where('status', 'published')->with(['category', 'bookAuthors'])->latest()->take(8)->get(),

            // Testimonials
            'list_testimonial' => \App\Models\Testimonial::active()->ordered()->take(6)->get(),

            // Latest Products
            'list_product' => \App\Models\Product::where('is_active', true)->with('category')->latest()->take(8)->get(),
        ];

        return view('front.pages.home.index', $data);
    }

    public function vistWebsite()
    {
        try {
            $currentUserInfo = Location::get(request()->ip());
            $visitor = new Visitor;
            $visitor->ip = request()->ip();
            if ($currentUserInfo) {
                $visitor->country = $currentUserInfo->countryName;
                $visitor->city = $currentUserInfo->cityName;
                $visitor->region = $currentUserInfo->regionName;
                $visitor->postal_code = $currentUserInfo->postalCode;
                $visitor->latitude = $currentUserInfo->latitude;
                $visitor->longitude = $currentUserInfo->longitude;
                $visitor->timezone = $currentUserInfo->timezone;
            }
            $visitor->user_agent = Agent::getUserAgent();
            $visitor->platform = Agent::platform();
            $visitor->browser = Agent::browser();
            $visitor->device = Agent::device();
            $visitor->save();

            return response()->json(['status' => 'success', 'message' => 'Visitor has been saved'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }
}
