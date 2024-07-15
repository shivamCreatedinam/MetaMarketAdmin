@extends('partials.app')
@section('title', 'User View')
@section('container')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Dashboard</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="{{ route('dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin_user_list') }}">Users</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">User View</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mx-4">
            <div class="card-header">
                <h5><a href="{{ route('admin_user_list') }}"><i class="fas fa-arrow-alt-circle-left"
                            title="Back To User List"></i></a> User Status : @if ($user->user_status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($user->user_status === 'block' || $user->user_status === 'ban')
                        <span class="badge bg-danger">{{ ucfirst($user->user_status) }}</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>1. Personal Details</h4>
                        <table class="table table-hover">
                            <tr>
                                <th>Full Name :</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email :</th>
                                <td><a href="mailto:{{ $user->email }}"
                                        style="text-decorations:none; color:inherit;">{{ $user->email }}</a></td>
                            </tr>
                            <tr>
                                <th>Mobile :</th>
                                <td><a href="tel:{{ $user->mobile_no }}"
                                        style="text-decorations:none; color:inherit;">{{ $user->mobile_no }}</a></td>
                            </tr>

                            <tr>
                                <th>2FA Verification :</th>
                                <td>{!! $user->google2fa_enable != 'no'
                                    ? "<span class='badge bg-success'>Enable</span>"
                                    : "<span class='badge bg-danger'>Disabled</span>" !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>2. KYC Details</h4>
                        <table class="table table-hover">
                            <tr>
                                <th>Email Verification :</th>
                                <td>{!! $user->email_verified_at != null
                                    ? "<span class='badge bg-success'>Verified</span>"
                                    : "<span class='badge bg-danger'>Not Verified</span>" !!}</td>
                            </tr>
                            <tr>
                                <th>Mobile Verification :</th>
                                <td>{!! $user->mobile_verified_at != null
                                    ? "<span class='badge bg-success'>Verified</span>"
                                    : "<span class='badge bg-danger'>Not Verified</span>" !!}</td>
                            </tr>
                            <tr>
                                <th>Aadhar Verification :</th>
                                <td>
                                    @if ($user->aadhar_verified != 0)
                                        <span class="badge bg-success">Verified</span> |
                                        <a class="badge bg-warning" href="{{ asset('public/assets/img/arashmil.jpg') }}"
                                            target="_blank">View</a>
                                    @else
                                        <span class="badge bg-danger">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>PAN Verification :</th>
                                <td>
                                    @if ($user->pan_verified != 0)
                                        <span class="badge bg-success">Verified</span> |
                                        <a class="badge bg-warning" href="{{ asset('public/assets/img/arashmil.jpg') }}"
                                            target="_blank">View</a>
                                    @else
                                        <span class="badge bg-danger">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Bank Verification :</th>
                                <td>
                                    @if ($user->bank_verified != 0)
                                        <span class="badge bg-success">Verified</span> |
                                        <a class="badge bg-warning" href="{{ asset('public/assets/img/arashmil.jpg') }}"
                                            target="_blank">View</a>
                                    @else
                                        <span class="badge bg-danger">Not Verified</span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th>VPA Verification :</th>
                                <td>
                                    @if ($user->vpa_verified != 0)
                                        <span class="badge bg-success">Verified</span> |
                                        <a class="badge bg-warning" href="{{ asset('public/assets/img/arashmil.jpg') }}"
                                            target="_blank">View</a>
                                    @else
                                        <span class="badge bg-danger">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>KYC Verification :</th>
                                <td>
                                    @if ($user->kyc_verified != 0)
                                        <span class="badge bg-success">Verified</span> |
                                        <a class="badge bg-warning" href="{{ asset('public/assets/img/arashmil.jpg') }}"
                                            target="_blank">View</a>
                                    @else
                                        <span class="badge bg-danger">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
