@extends('partials.app')
@section('title', 'User Edit')
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
                        <a href="#">User Edit</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mx-4">
            <div class="card-header">
                <h5><a href="{{ route('admin_user_list') }}"><i class="fas fa-arrow-alt-circle-left"
                            title="Back To User List"></i></a> User Edit</h5>
            </div>
            <div class="card-body">

                @include('status')
                {{-- <div class="btns">
                    <div class="row text-center">

                        <div class="col-md-2 form-group">
                            <label for="">User Status</label><br>
                            <input type="checkbox" {{ $user->user_status == 'active' ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Active" data-offlabel="Block"
                                data-onstyle="success" data-offstyle="danger">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">User BAN</label><br>
                            <input type="checkbox" {{ $user->user_status == 'ban' ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="BAN" data-offlabel="Not BAN"
                                data-onstyle="success" data-offstyle="danger">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Email Verification</label><br>
                            <input type="checkbox" {{ $user->email_verified_at != null ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Mobile Verification</label><br>
                            <input type="checkbox" {{ $user->mobile_verified_at != null ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">2FA Enable</label><br>
                            <input type="checkbox" {{ $user->google2fa_enable == 'yes' ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Enable" data-offlabel="Disable"
                                data-onstyle="success" data-offstyle="danger">
                        </div>

                    </div>
                </div>

                <hr> --}}
                <div class="editFormDiv">
                    <h5><u>Update Personal Details : </u></h5>
                    <form action="{{ route('admin_user_update') }}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->uuid }}">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ $user->name }}" placeholder="Enter Name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ $user->email }}" placeholder="Enter Email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="mobile_no">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_no" id="mobile_no" class="form-control"
                                    value="{{ $user->mobile_no }}" placeholder="Enter Mobile Number">
                                @error('mobile_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="sb_btn text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

                <hr>

                <div class="btns">
                    <h5><u>Update Status & KYC Details : </u></h5>
                    <div class="row text-center">

                        <div class="col-md-2 form-group">
                            <label for="">User Status</label><br>
                            <input type="checkbox" {{ $user->user_status == 'active' ? 'checked' : '' }}
                                data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Active"
                                data-offlabel="Block" data-onstyle="success" data-offstyle="danger" data-type="user_status">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">User BAN</label><br>
                            <input type="checkbox" {{ $user->user_status == 'ban' ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="BAN" data-offlabel="Not BAN"
                                data-onstyle="success" data-offstyle="danger" data-type="user_ban">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Email Verification</label><br>
                            <input type="checkbox" {{ $user->email_verified_at != null ? 'checked' : '' }}
                                data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Verified"
                                data-offlabel="Not Verify" data-onstyle="success" data-offstyle="danger"
                                data-type="email_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Mobile Verification</label><br>
                            <input type="checkbox" {{ $user->mobile_verified_at != null ? 'checked' : '' }}
                                data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Verified"
                                data-offlabel="Not Verify" data-onstyle="success" data-offstyle="danger"
                                data-type="mobile_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Aadhar Verification</label><br>
                            <input type="checkbox" {{ $user->aadhar_verified != 0 ? 'checked' : '' }}
                                data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Verified"
                                data-offlabel="Not Verify" data-onstyle="success" data-offstyle="danger"
                                data-type="aadhar_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">PAN Verification</label><br>
                            <input type="checkbox" {{ $user->pan_verified != 0 ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger" data-type="pan_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">Bank Verification</label><br>
                            <input type="checkbox" {{ $user->bank_verified != 0 ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger" data-type="bank_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">VPA Verification</label><br>
                            <input type="checkbox" {{ $user->vpa_verified != 0 ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger" data-type="vpa_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">KYC Verification</label><br>
                            <input type="checkbox" {{ $user->kyc_verified != 0 ? 'checked' : '' }} data-toggle="toggle"
                                data-width="100" data-height="75" data-onlabel="Verified" data-offlabel="Not Verify"
                                data-onstyle="success" data-offstyle="danger" data-type="kyc_verify">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">2FA Enable</label><br>
                            <input type="checkbox" {{ $user->google2fa_enable == 'yes' ? 'checked' : '' }}
                                data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Enable"
                                data-offlabel="Disable" data-onstyle="success" data-offstyle="danger" data-type="2fa">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            // Attach event listener to elements with data-toggle="toggle"
            $('input[type="checkbox"]').change(function() {
                let type = $(this).data('type');
                updateStatus(this, type);
            });

            // Initialize toggle buttons if needed
            // $('[data-toggle="toggle"]').bootstrapToggle();
        });

        function updateStatus(input, type, other = null) {
            let inputData = $(this)
            let user_id = $('#user_id').val()
            $.ajax({
                type: "post",
                url: "{{ route('admin_user_status_update') }}",
                data: {
                    "status_type": type,
                    "user_id": user_id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endpush
