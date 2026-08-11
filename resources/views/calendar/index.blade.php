@extends('layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<section class="page" id="page-calendar">
    <div class="two-col">
        <div class="card card-pad">
            <h3 style="margin:0 0 14px;font-size:15px;" id="cal-month-label"></h3>
            <div class="cal-grid" id="cal-dow-row"></div>
            <div class="cal-grid" id="cal-grid" style="margin-top:6px;"></div>
        </div>
        <div>
            <div class="section-head" style="margin-top:0;">
                <h3>Kegiatan Mendatang</h3>
            </div>
            <div class="card card-pad" id="events-list"></div>
        </div>
    </div>
</section>
@endsection