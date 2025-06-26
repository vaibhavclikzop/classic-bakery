@extends('layouts.main')
@section('main-section')
    @push('title')
        <title>Profile</title>
    @endpush
    <style>
        .profile-contentimg {
            height: 130px !important;
            width: 130px !important;
        }
    </style>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="page-title">
                <h4>Profile</h4>
            </div>
            <div class="">




            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('SaveProfile') }}" method="POST" class="needs-validation" novalidate>

@csrf
                <div class="profile-set">
                    <div class="profile-head">
                    </div>
                    <div class="profile-top">
                        <div class="profile-content">
                            <div class="profile-contentimg">
                                <img src="/logo/{{ $setting->img }}" alt="img" id="blah">

                            </div>
                            <div class="profile-contentname">
                                <h2> {{ $user->name }} </h2>
                                <h4> {{ $user->email }}</h4>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" name="name" required>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label>Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" name="email" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Phone</label>
                            <input type="text" value="{{ $user->phone }}" name="phone" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" value="{{ $user->address }}" name="address" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Password</label>
                            <div class="pass-group">
                                <input type="password" value="{{ $user->password }}" name="password"
                                    class="pass-input form-control" required>
                                <span class="fas toggle-password fa-eye-slash"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-submit me-2" type="submit">Update</button>

                    </div>
                </div>

            </form>

        </div>
    </div>
@endsection
