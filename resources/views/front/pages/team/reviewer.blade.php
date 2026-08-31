@extends('front.app')

@section('content')
    <!-- TEAM REVIEWER SECTION
    ============================================= -->
    <section id="team-reviewer" class="pt-80 pb-60 division">
        <div class="container">

            <!-- SECTION TITLE -->
            <div class="row">
                <div class="col-lg-10 offset-lg-1 section-title text-center mb-40">
                    <h3 class="h3-lg">Mitra Bestari / Reviewer Jurnal</h3>
                    <p class="p-lg">Daftar pakar dan akademisi mitra bestari yang bertindak sebagai reviewer dalam penelaahan naskah ilmiah.</p>
                </div>
            </div>

            <!-- JOURNAL SELECTOR TABS -->
            @if($journals->count() > 0)
                <div class="row mb-40">
                    <div class="col-md-12 text-center">
                        <div class="masonry-filter theme-filter mb-30" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                            @foreach($journals as $j)
                                <a href="{{ route('team.reviewer', ['journal' => $j->url_path]) }}"
                                   class="btn btn-sm {{ request('journal', $journals->first()->url_path) === $j->url_path ? 'btn-theme' : 'btn-tra-grey' }}"
                                   style="border-radius: 20px; padding: 6px 18px; font-size: 13px;">
                                    {{ $j->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- REVIEWERS LIST -->
            <div class="row">
                @forelse($reviewers as $reviewer)
                    <div class="col-md-6 col-lg-4 mb-30">
                        <div class="card h-100 border-0 shadow-sm radius-06 p-4 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="flaticon-user" style="font-size: 48px; color: #2a80b9;"></span>
                                </div>
                                <h5 class="h5-sm mb-1">{{ $reviewer['fullName'] ?? $reviewer['name'] ?? 'Reviewer' }}</h5>
                                @if(!empty($reviewer['affiliation']))
                                    <p class="p-sm text-muted mb-2">{{ $reviewer['affiliation'] }}</p>
                                @endif
                                @if(!empty($reviewer['email']))
                                    <p class="p-sm mb-2"><a href="mailto:{{ $reviewer['email'] }}" class="theme-color">{{ $reviewer['email'] }}</a></p>
                                @endif
                                @if(!empty($reviewer['jurnal']))
                                    <div class="mt-3 pt-2 border-top">
                                        <small class="text-muted d-block mb-1">Reviewer pada:</small>
                                        @foreach($reviewer['jurnal'] as $rj)
                                            <span class="badge badge-light mr-1" style="font-size: 11px;">{{ $rj['name'] }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="p-lg text-muted">Belum ada data reviewer untuk jurnal ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
@endsection
