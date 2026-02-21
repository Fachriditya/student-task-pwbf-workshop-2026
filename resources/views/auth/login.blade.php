<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Purple Admin</title>
    
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <style>
      .auth-form-light {
        background: #ffffff;
        border-radius: 15px;
      }
      .btn-gradient-primary {
        background: linear-gradient(to right, #da8cff, #9a55ff) !important;
        border: none;
        font-weight: bold;
      }
      .content-wrapper.auth {
        background: #f2edf3;
      }
      @media (min-width: 768px) {
        .border-right-md {
          border-right: 1px solid #e9e9e9;
        }
      }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-md-10 col-lg-8 mx-auto">
              <div class="auth-form-light text-left p-4 p-sm-5 shadow-lg">
                
                <div class="row align-items-center">
                  <div class="col-md-5 text-center mb-4 mb-md-0 border-right-md">
                    <div class="brand-logo mb-3">
                      <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                    </div>
                    <h4 class="font-weight-light">Selamat Datang!</h4>
                    <p class="text-muted small">Silakan masuk untuk mengelola dashboard aplikasi Anda.</p>
                  </div>

                  <div class="col-md-7">
                    <form class="pt-3 px-md-3" method="POST" action="{{ route('login') }}">
                      @csrf
                      
                      <div class="form-group mb-3">
                        <label class="small font-weight-bold">Alamat Email</label>
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="email@domain.com">
                        @error('email')
                          <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                      </div>

                      <div class="form-group mb-3">
                        <label class="small font-weight-bold">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required placeholder="********">
                        @error('password')
                          <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                      </div>

                      <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                          <label class="form-check-label text-muted" style="font-size: 0.8rem;">
                            <input type="checkbox" name="remember" class="form-check-input"> Ingat Saya </label>
                        </div>
                        @if (Route::has('password.request'))
                          <a href="{{ route('password.request') }}" class="auth-link text-primary small">Lupa password?</a>
                        @endif
                      </div>

                      <div class="mt-3 d-grid">
                        <button type="submit" class="btn btn-gradient-primary btn-lg auth-form-btn text-white">MASUK</button>
                      </div>

                      <div class="text-center mt-4 font-weight-light">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-weight-bold">Daftar Sekarang</a>
                      </div>
                    </form>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        </div>
      </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
  </body>
</html>