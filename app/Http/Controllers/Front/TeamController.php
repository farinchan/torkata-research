<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function editor(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.editor", ['journal' => $journal_first]);
        }
        $setting_web = SettingWebsite::first();

         $journals = Journal::get();
        $journalData = [];

        foreach ($journals as $journal) {
            $journalData[] = $this->editorCache($request, $journal->url_path);
        }

        $editorMap = [];

        foreach ($journalData as $journalEntry) {
            $journalName = $journalEntry['journal'];
            $urlPath = $journalEntry['url_path'];
            foreach ($journalEntry['editor'] as $editor) {
                $id = $editor['id'];

                if (!isset($editorMap[$id])) {
                    // Salin data editor dan buat array jurnal
                    $editorMap[$id] = $editor;
                    $editorMap[$id]['jurnal'] = [];
                }

                $editorMap[$id]['jurnal'][] = [
                    'name' => $journalName,
                    'path' => $urlPath
                ];
            }
        }

        $targetEditors = [];
        foreach ($journalData as $journalEntry) {
            if ($journalEntry['url_path'] === $path) {
                foreach ($journalEntry['editor'] as $editor) {
                    $id = $editor['id'];
                    $targetEditors[] = $editorMap[$id];
                }

                $finalOutput = [
                    'journal' => $journalEntry['journal'],
                    'url_path' => $journalEntry['url_path'],
                    'editor' => $targetEditors,
                ];
                break;
            }
        }

        $journalName = $finalOutput['journal'] ?? ($setting_web->name ?? 'Jurnal');

        $data = [
            'title' => 'Dewan Editorial - ' . $journalName . ' | ' . $setting_web->name,
            'meta' => [
                'title' => 'Dewan Editorial - ' . $journalName . ' | ' . $setting_web->name,
                'description' => Str::limit('Daftar tim dewan editor jurnal ilmiah ' . $journalName . ' yang bertanggung jawab atas penelaahan naskah', 155),
                'keywords' => 'dewan editorial ' . $journalName . ', tim editor jurnal, editor in chief, dewan penyunting, penelaah, publikasi ilmiah, ' . $setting_web->name,
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('team.editor', ['journal' => $path]),
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
                    'name' => 'Dewan Editorial',
                    'link' => route('team.editor', ['journal' => $path])
                ]
            ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'editors' => $targetEditors,
        ];

        return view('front.pages.team.editor', $data);
    }

    private function editorCache(Request $request, $url_path)
    {
        $jurnal = Journal::where('url_path', $url_path)->first();

        try {
            $cacheKey = $url_path . '_editor_list_cache';
            $cachedData = cache()->get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }

            $editors = User::role('editor')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'fullName' => $user->name,
                    'email' => $user->email,
                    'userName' => $user->username,
                    'affiliation' => null,
                ];
            })->all();

            $data = [
                'journal' => $jurnal->title,
                'url_path' => $jurnal->url_path,
                'message' => 'Success get editor list',
                'editor' => $editors,
            ];

            cache()->put($cacheKey, $data, now()->addMinutes(120));

            return $data;
        } catch (\Throwable $th) {
            return [
                'journal' => $jurnal->title ?? '',
                'url_path' => $url_path,
                'message' => 'Error: ' . $th->getMessage(),
                'editor' => [],
            ];
        }
    }


    public function reviewer(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.reviewer", ['journal' => $journal_first]);
        }
        $setting_web = SettingWebsite::first();

        $journals = Journal::get();
        $journalData = [];

        foreach ($journals as $journal) {
            $journalData[] = $this->reviewerCache($request, $journal->url_path);
        }

        $reviewerMap = [];

        foreach ($journalData as $journalEntry) {
            $journalName = $journalEntry['journal'];
            $urlPath = $journalEntry['url_path'];
            foreach ($journalEntry['reviewer'] as $reviewer) {
                $id = $reviewer['id'];

                if (!isset($reviewerMap[$id])) {
                    // Salin data reviewer dan buat array jurnal
                    $reviewerMap[$id] = $reviewer;
                    $reviewerMap[$id]['jurnal'] = [];
                }

                $reviewerMap[$id]['jurnal'][] = [
                    'name' => $journalName,
                    'path' => $urlPath
                ];
            }
        }

        $targetReviewers = [];
        $finalOutput = [];
        foreach ($journalData as $journalEntry) {
            if ($journalEntry['url_path'] === $path) {
                foreach ($journalEntry['reviewer'] as $reviewer) {
                    $id = $reviewer['id'];
                    $targetReviewers[] = $reviewerMap[$id];
                }

                $finalOutput = [
                    'journal' => $journalEntry['journal'],
                    'url_path' => $journalEntry['url_path'],
                    'reviewer' => $targetReviewers,
                ];
                break;
            }
        }

        $reviewerJournalName = $finalOutput['journal'] ?? ($setting_web->name ?? 'Jurnal');

        $data = [
            'title' => 'Mitra Bestari (Reviewer) - ' . $reviewerJournalName . ' | ' . $setting_web->name,
            'meta' => [
                'title' => 'Mitra Bestari (Reviewer) - ' . $reviewerJournalName . ' | ' . $setting_web->name,
                'description' => Str::limit('Daftar pakar dan akademisi mitra bestari penelaah naskah jurnal ilmiah ' . $reviewerJournalName, 155),
                'keywords' => 'mitra bestari ' . $reviewerJournalName . ', peer reviewer, penelaah naskah ahli, reviewer jurnal ilmiah, evaluasi artikel, ' . $setting_web->name,
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('team.reviewer', ['journal' => $path]),
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
                    'name' => 'Mitra Bestari',
                    'link' => route('team.reviewer', ['journal' => $path])
                ]
            ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'reviewers' => $targetReviewers,
        ];

        return view('front.pages.team.reviewer', $data);
    }

    private function reviewerCache(Request $request, $url_path)
    {
        $jurnal = Journal::where('url_path', $url_path)->first();
        // cache()->forget($url_path . '_reviewer_list_cache');

        try {
            $cacheKey =  $url_path . '_reviewer_list_cache';
            $cachedData = cache()->get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/users/reviewers', [
                'orderBy' => 'id',
                'count' => 100,
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                $data = [
                    'journal' => $jurnal->title,
                    'url_path' => $jurnal->url_path,
                    'message' => 'Success get reviewer list',
                    'reviewer' => collect($response->json()["items"] ?? [])->map(function ($item) {
                        return [
                            'id' => $item['id'] ?? null,
                            'fullName' => $item['fullName'] ?? null,
                            'email' => $item['email'] ?? null,
                            'userName' => $item['userName'] ?? null,
                            'affiliation' => $item['affiliation']['en_US'] ?? null,
                        ];
                    })->all(),
                ];

                cache()->put($cacheKey, $data, now()->addMinutes(120));

                return $data;
            } else {
                return [
                    'message' => 'Error: ' . $response->status(),
                ];
            }
        } catch (\Throwable $th) {
            return [
                'message' => 'Error: ' . $th->getMessage(),
            ];
        }
    }
}
