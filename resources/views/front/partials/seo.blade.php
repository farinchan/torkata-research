{{-- SEO Meta Tags & Structured Data Partial --}}
@php
    $siteName = $setting_web->name ?? config('app.name');
    $defaultDesc = Str::limit(strip_tags($setting_web?->about ?? 'Lembaga Riset, Publikasi Ilmiah, Penerbitan Buku dan Pendidikan'), 160);
    $metaDesc = !empty($meta['description']) ? Str::limit(strip_tags($meta['description']), 160) : $defaultDesc;
    $metaKeywords = $meta['keywords'] ?? ($setting_web->keywords ?? 'jurnal ilmiah, publikasi, buku, penelitian, nagari sastra, padang, sumatera barat');
    $metaRobots = $meta['robots'] ?? 'index, follow';
    $metaAuthor = $meta['author'] ?? $siteName;
    $metaCanonical = $meta['canonical'] ?? url()->current();
    $metaTitle = Str::limit($meta['title'] ?? ($title ?? $siteName), 70);
    $metaOgType = $meta['og_type'] ?? 'website';

    // Ensure og_image is always a full absolute URL
    $ogImage = $meta['og_image'] ?? ($setting_web?->getLogo() ?? null);
    if ($ogImage && !Str::startsWith($ogImage, ['http://', 'https://'])) {
        $ogImage = url($ogImage);
    }

    // Build Organization & WebSite JSON-LD Schema
    $orgSchema = [
        '@type' => 'Organization',
        '@id' => url('/') . '#organization',
        'name' => $siteName,
        'url' => url('/'),
    ];

    if ($setting_web?->getLogo()) {
        $logoUrl = Str::startsWith($setting_web->getLogo(), ['http://', 'https://']) ? $setting_web->getLogo() : url($setting_web->getLogo());
        $orgSchema['logo'] = [
            '@type' => 'ImageObject',
            '@id' => url('/') . '#logo',
            'url' => $logoUrl,
            'caption' => $siteName,
        ];
        $orgSchema['image'] = ['@id' => url('/') . '#logo'];
    }

    if ($setting_web?->phone || $setting_web?->email) {
        $contactPoint = [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'areaServed' => 'ID',
            'availableLanguage' => ['id', 'en'],
        ];
        if ($setting_web->phone) $contactPoint['telephone'] = $setting_web->phone;
        if ($setting_web->email) $contactPoint['email'] = $setting_web->email;
        $orgSchema['contactPoint'] = $contactPoint;
    }

    $sameAs = array_values(array_filter([
        $setting_web?->facebook,
        $setting_web?->instagram,
        $setting_web?->linkedin,
        $setting_web?->whatsapp ? 'https://wa.me/' . $setting_web->whatsapp : null,
    ]));
    if (!empty($sameAs)) {
        $orgSchema['sameAs'] = $sameAs;
    }

    $websiteSchema = [
        '@type' => 'WebSite',
        '@id' => url('/') . '#website',
        'url' => url('/'),
        'name' => $siteName,
        'description' => $defaultDesc,
        'publisher' => ['@id' => url('/') . '#organization'],
        'inLanguage' => 'id-ID',
    ];

    $globalSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [$orgSchema, $websiteSchema],
    ];
@endphp

{{-- Basic Meta Tags --}}
<meta name="description" content="{{ $metaDesc }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="robots" content="{{ $metaRobots }}">
<meta name="author" content="{{ $metaAuthor }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $metaCanonical }}">

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:type" content="{{ $metaOgType }}">
<meta property="og:url" content="{{ $metaCanonical }}">
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $metaTitle }}">
@endif
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="id_ID">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDesc }}">
@if ($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif

{{-- Schema.org Organization & WebSite JSON-LD --}}
<script type="application/ld+json">
{!! json_encode($globalSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
