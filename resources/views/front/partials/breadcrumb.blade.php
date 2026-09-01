   <!-- PAGE HERO
   ============================================= -->
    <style>
        .page-hero-section {
            padding-top: 90px !important;
            padding-bottom: 70px !important;
        }
        #breadcrumb {
            margin-bottom: 12px !important;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            font-size: 0.85rem !important;
        }
    </style>
   <div id="blogs-listing-page" class="page-hero-section division" >
       <div class="container">
           <div class="row">
               <div class="col-lg-10 offset-lg-1">
                   <div class="hero-txt text-center white-color">

                       <!-- Breadcrumb -->
                       <div id="breadcrumb">
                           <div class="row">
                               <div class="col">
                                   <div class="breadcrumb-nav">
                                       <nav aria-label="breadcrumb">
                                           <ol class="breadcrumb">
                                              @isset($breadcrumbs)
                                                   @foreach ($breadcrumbs as $breadcrumb)
                                                    @if ($loop->last)
                                                       <li class="breadcrumb-item active" aria-current="page" style="font-size: 0.9rem;">{{ $breadcrumb['name'] ?? '' }}</li>
                                                       @else
                                                       <li class="breadcrumb-item"><a
                                                               href="{{ $breadcrumb['link'] ?? '' }}" style="font-size: 0.9rem;">{{ $breadcrumb['name'] ?? '' }}</a>
                                                       </li>
                                                       @endif
                                                   @endforeach
                                               @endisset
                                           </ol>
                                       </nav>
                                   </div>
                               </div>
                           </div>
                       </div>

                        <!-- Title -->
                        @if(request()->routeIs('news.detail', 'book.show', 'product.show', 'event.show', 'announcement.show', 'journal.detail'))
                            <p class="h2-sm font-weight-bold mb-0" style="font-size: 1.4rem; color: #fff;">
                                @isset($title)
                                    {{ $title }}
                                @endisset
                            </p>
                        @else
                            <h1 class="h2-sm mb-0" style="font-size: 1.4rem; color: #fff;">
                                @isset($title)
                                    {{ $title }}
                                @endisset
                            </h1>
                        @endif

                   </div>
               </div>
           </div> <!-- End row -->
       </div> <!-- End container -->
   </div> <!-- END PAGE HERO -->
