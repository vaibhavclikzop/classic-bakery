<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title>Login </title>

    <link rel="shortcut icon" type="image/x-icon" href="/favicon.png">

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <link rel="stylesheet" href="/css/fontawesome.min.css">
    <link rel="stylesheet" href="/css/all.min.css">

    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper bg-img">
                <div class="login-content">
                    <form method="POST" action="{{ route('SuperAdminLogin') }}">
                        @csrf
                        <div class="login-userset">
                            <div class="login-logo logo-normal">
                                <img src="/logo/{{$setting->img}}" alt="img">
                            </div>
                            <a href="index" class="login-logo logo-white">
                                <img src="/logo/{{$setting->img}}" alt="">
                            </a>
                            <div class="login-userheading">
                                <h3>Sign In</h3>
                                <h4>Access the Inventory panel using your email and password.</h4>
                            </div>
                            <div class="form-login mb-3">
                                <label class="form-label">Email</label>
                                <div class="form-addons">
                                    <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                                    <img src="/images/mail.svg" alt="img">
                                </div>
                            </div>
                            <div class="form-login mb-3">
                                <label class="form-label">Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control" name="password" placeholder="Enter Password" required>
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>

                            <div class="form-login">
                                <button type="submit" class="btn btn-login">Sign In</button>
                            </div>


                            <div class="form-sociallink">

                                <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                                    <p>Copyright © <?= date('Y') ?>  {{$setting->company_name}} All rights reserved</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="customizer-links" id="setdata">
        <ul class="sticky-sidebar">
            <li class="sidebar-icons">
                <a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="Theme">
                    <i data-feather="settings" class="feather-five"></i>
                </a>
            </li>
        </ul>
    </div>

    <script src="/js/jquery-3.7.1.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>

    <script src="/js/feather.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>

    <script src="/js/bootstrap.bundle.min.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="/js/theme-script.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="/js/script.js" type="c5db94e3416bf781b99349f4-text/javascript"></script>
    <script src="/js/rocket-loader.min.js" data-cf-settings="c5db94e3416bf781b99349f4-|49" defer=""></script>
</body>

</html>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    
    @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}');
    @elseif (Session::has('success'))
        toastr.success('{{ Session::get('success') }}');
    @elseif (Session::has('warning'))
        toastr.warning('{{ Session::get('warning') }}');
    @endif
</script>