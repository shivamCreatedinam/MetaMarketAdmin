@extends('partials.app')
@section('title', 'Users List')
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

                </ul>
            </div>
        </div>

        <div class="card mx-4">
            <div class="card-header">
                @include('status')
            </div>
            <div class="card-body">
                <h4 class="card-title">Users List</h4>

                {{-- <input type="checkbox" checked data-toggle="toggle" data-width="100" data-height="75" data-onlabel="Active" data-offlabel="Block" data-onstyle="success" data-offstyle="danger"> --}}


                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover text-center" id="userListTable"
                        style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Mobile</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(document).ready(function() {
            var table = $('#userListTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                // order: [
                //     [0, "desc"]
                // ],
                ajax: {
                    url: "{{ route('admin_user_list') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile_no',
                        name: 'mobile_no'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
