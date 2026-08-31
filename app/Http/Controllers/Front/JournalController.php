<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => 'Direktori Jurnal Ilmiah | ' . $setting_web->name,
            'meta' => [
                'title' => 'Direktori Jurnal Ilmiah | ' . $setting_web->name,
                'description' => Str::limit('Direktori publikasi jurnal ilmiah terakreditasi dan peer-reviewed yang dikelola oleh ' . $setting_web->name, 155),
                'keywords' => 'direktori jurnal ilmiah, jurnal nasional terakreditasi, jurnal sinta, open journal systems, ojs 3, call for papers, submit naskah, publikasi penelitian, ' . $setting_web->name . ', padang, sumatera barat',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('journal.index'),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Jurnal',
                    'link' => route('journal.index')
                ]
            ],
            'journals' => Journal::latest()->get(),
        ];
        return view('front.pages.journal.index', $data);
    }

    public function detail($journal_path)
    {
        $setting_web = SettingWebsite::first();
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            abort(404);
        }

        $indexingStr = is_array($journal->indexing) ? implode(', ', $journal->indexing) : '';

        $data = [
            'title' => $journal->title . ' - Jurnal Ilmiah | ' . $setting_web->name,
            'meta' => [
                'title' => $journal->title . ' - Jurnal Ilmiah | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($journal->description), 155),
                'keywords' => 'jurnal ' . $journal->title . ', submit artikel ' . $journal->title . ', call for papers, peer-reviewed journal, OJS, akreditasi jurnal ' . $indexingStr . ', publikasi ilmiah, ' . $setting_web->name . ', padang',
                'favicon' => $journal->getJournalThumbnail() ?? $setting_web->favicon,
                'og_image' => $journal->getJournalThumbnail(),
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('journal.detail', $journal->url_path),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Jurnal',
                    'link' => route('journal.index')
                ],
                [
                    'name' => Str::limit($journal->title, 35),
                    'link' => route('journal.detail', $journal->url_path)
                ]
            ],
            'journal' => $journal,
            'issues' => $journal->issues()->latest()->paginate(6),
        ];
        return view('front.pages.journal.detail', $data);
    }
}
