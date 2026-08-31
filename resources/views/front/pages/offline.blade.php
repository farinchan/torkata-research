@extends('front.app')

@section('content')
    <!-- OFFLINE SECTION
    ============================================= -->
    <section id="offline-page" class="pt-100 pb-100 division text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-sm radius-06 p-5">
                        <div class="mb-4">
                            <span class="flaticon-wifi-1" style="font-size: 64px; color: #64748b;"></span>
                        </div>
                        <h3 class="h3-sm mb-3">Anda Sedang Offline</h3>
                        <p class="p-md grey-color mb-4">
                            Koneksi internet Anda terputus. Beberapa fitur mungkin tidak dapat diakses hingga koneksi internet kembali terhubung.
                        </p>
                        <div>
                            <button onclick="window.location.reload()" class="btn btn-theme mr-2 mb-2" style="border-radius: 25px; padding: 10px 24px;">
                                Coba Muat Ulang
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-tra-grey mb-2" style="border-radius: 25px; padding: 10px 24px;">
                                Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
