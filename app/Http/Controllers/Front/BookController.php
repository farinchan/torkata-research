<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $setting_web = SettingWebsite::first();
        $search = strip_tags(trim($request->q ?? ''));
        $search = mb_substr($search, 0, 100); // Limit search length

        $books = Book::where('status', 'published')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('publisher', 'like', '%' . $search . '%')
                      ->orWhereHas('bookAuthors', function ($sub) use ($search) {
                          $sub->where('name', 'like', '%' . $search . '%')
                              ->orWhere('name_with_title', 'like', '%' . $search . '%');
                      });
                });
            })
            ->with('category')
            ->latest()
            ->paginate(6);
        $books->appends(['q' => $search]);

        $data = [
            'title' => 'Katalog Buku & Penerbitan | ' . $setting_web->name,
            'meta' => [
                'title' => 'Katalog Buku & Penerbitan | ' . $setting_web->name,
                'description' => Str::limit('Katalog buku ilmiah, monograf, buku ajar ber-ISBN yang diterbitkan oleh ' . $setting_web->name, 155),
                'keywords' => 'katalog buku, penerbit buku ber-isbn, buku ilmiah, monograf, buku ajar, referensi akademik, cetak buku, beli buku, ' . $setting_web->name . ', padang, sumatera barat',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('book.index'),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Buku',
                    'link' => route('book.index')
                ]
            ],
            'books' => $books,
            'categories' => BookCategory::withCount('books')->get(),
        ];

        return view('front.pages.book.index', $data);
    }

    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $book = Book::with(['category', 'bookAuthors'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Get related books from same category
        $related_books = $book->category->books()
            ->where('id', '!=', $book->id)
            ->where('status', 'published')
            ->limit(4)
            ->get();

        $authorsStr = $book->bookAuthors->pluck('name')->filter()->implode(', ');
        if (empty($authorsStr) && !empty($book->author)) {
            $authorsStr = $book->author;
        }

        $data = [
            'title' => $book->title . ' | ' . $setting_web->name,
            'meta' => [
                'title' => $book->title . ' | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($book->description), 155),
                'keywords' => 'buku ' . $book->title . ', ' . $authorsStr . ', penerbit ' . ($book->publisher ?? $setting_web->name) . ', ISBN ' . ($book->isbn ?? '-') . ', ' . ($book->category->name ?? 'buku ilmiah') . ', beli buku, ' . $setting_web->name,
                'favicon' => $book->getThumbnail() ?? $setting_web->favicon,
                'author' => $authorsStr ?: $setting_web->name,
                'og_image' => $book->getThumbnail(),
                'og_type' => 'book',
                'robots' => 'index, follow',
                'canonical' => route('book.show', $book->slug),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Buku',
                    'link' => route('book.index')
                ],
                [
                    'name' => $book->category->name ?? 'Kategori',
                    'link' => route('book.category', $book->category->slug ?? '')
                ],
                [
                    'name' => Str::limit($book->title, 35),
                    'link' => route('book.show', $book->slug)
                ]
            ],
            'book' => $book,
            'related_books' => $related_books,
            'categories' => BookCategory::withCount('books')->get(),
        ];

        return view('front.pages.book.show', $data);
    }

    public function preview($slug)
    {
        $setting_web = SettingWebsite::first();
        $book = Book::with(['category', 'bookAuthors'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $data = [
            'title' => $book->title . ' - Preview' . ' | ' . $setting_web->name,
            'meta' => [
                'title' => $book->title . ' - Preview | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($book->description), 155),
                'keywords' => $setting_web->name . ', ' . $book->title . ', Preview Buku, penerbitan, panjelasan, informasi, kota padang',
                'favicon' => $book->getThumbnail() ?? $setting_web->favicon,
                'og_image' => $book->getThumbnail(),
                'og_type' => 'book',
                'robots' => 'noindex, follow',
                'canonical' => route('book.show', $book->slug),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Buku',
                    'link' => route('book.index')
                ],
                [
                    'name' => $book->category->name,
                    'link' => route('book.category', $book->category->slug)
                ],
                [
                    'name' => $book->title,
                    'link' => route('book.show', $book->slug)
                ],
                [
                    'name' => 'Preview',
                    'link' => route('book.preview', $book->slug)
                ]
            ],
            'book' => $book,
            'preview_url' => $book->getPreviewFile(),
            'categories' => BookCategory::withCount('books')->get(),
        ];

        return view('front.pages.book.preview', $data);
    }

    public function category($slug)
    {
        $setting_web = SettingWebsite::first();
        $category = BookCategory::where('slug', $slug)->firstOrFail();

        $books = $category->books()
            ->where('status', 'published')
            ->latest()
            ->paginate(6);

        $data = [
            'title' => 'Buku Kategori ' . $category->name . ' | ' . $setting_web->name,
            'meta' => [
                'title' => 'Buku Kategori ' . $category->name . ' | ' . $setting_web->name,
                'description' => Str::limit('Koleksi buku kategori ' . $category->name . ' yang diterbitkan oleh ' . $setting_web->name, 155),
                'keywords' => 'buku ' . $category->name . ', kategori ' . $category->name . ', penerbitan buku ' . $category->name . ', katalog buku, ' . $setting_web->name . ', padang',
                'favicon' => $setting_web->favicon,
                'og_image' => $setting_web->logo ?? $setting_web->favicon,
                'og_type' => 'website',
                'robots' => 'index, follow',
                'canonical' => route('book.category', $category->slug),
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Buku',
                    'link' => route('book.index')
                ],
                [
                    'name' => $category->name,
                    'link' => route('book.category', $category->slug)
                ]
            ],
            'category' => $category,
            'books' => $books,
            'categories' => BookCategory::withCount('books')->get(),
        ];

        return view('front.pages.book.category', $data);
    }
}
