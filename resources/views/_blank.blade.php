@extends('partials.app')
@section('title', 'Home')
@section('container')
    <div class="container">

        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Dashboard</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="#">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Pages</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Starter Page</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"></div> {{-- If need --}}
            <div class="card-body">
                {{-- Put Your Code --}}
            </div>
        </div>

    </div>
@endsection
