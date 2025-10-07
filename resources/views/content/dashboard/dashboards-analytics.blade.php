@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
    <div class="row">
        <!-- Attendence -->
        <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-lg-8">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2"><i class="bx bx-lg bx-history"></i> History</h5>
                            </div>
                        </div>
                        {{-- Table history absensi --}}
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Attendance -->
        <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="badge bg-label-warning fs-5 mb-1">Training</p>
                            <h4 class="card-title fs-1 text-center">2</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="badge bg-label-primary fs-5 mb-1">Cuti</p>
                            <h4 class="card-title fs-1 text-center">12</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-sm-row flex-column gap-10">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="mt-sm-auto">
                                        <h4 class="mb-0">Nama Pegawai</h4>
                                        <span class="badge bg-label-secondary fs-5">Shift Pagi</span>
                                    </div>
                                </div>
                                <button class="btn btn-primary rounded-circle fs-2"
                                    style="width:175px;height:175px;">Masuk</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
