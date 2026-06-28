@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUrls }}</h3>
                    <p>Total URLs Shortened</p>
                </div>
                <div class="icon">
                    <i class="fas fa-link"></i>
                </div>
                <a href="{{ route('urls.index') }}" class="small-box-footer">
                    View all <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
