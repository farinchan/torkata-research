   <!-- PAGE HERO
   ============================================= -->
   <div id="blogs-listing-page" class="page-hero-section division">
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
                                                       <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] ?? '' }}</li>
                                                       @else
                                                       <li class="breadcrumb-item"><a
                                                               href="{{ $breadcrumb['link'] ?? '' }}">{{ $breadcrumb['name'] ?? '' }}</a>
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
                       <h2 class="h2-sm">
                           @isset($title)
                               {{ $title }}
                           @endisset
                       </h2>

                   </div>
               </div>
           </div> <!-- End row -->
       </div> <!-- End container -->
   </div> <!-- END PAGE HERO -->
