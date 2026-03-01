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

                      <div class="my-3 d-flex align-items-center">
                        <hr class="flex-grow-1">
                        <span class="text-muted mx-3 small">atau</span>
                        <hr class="flex-grow-1">
                      </div>

                      <div class="d-grid">
                        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark btn-lg d-flex align-items-center justify-content-center">
                          <svg width="20" height="20" viewBox="0 0 20 20" class="me-2">
                            <path d="M19.6 10.23c0-.82-.1-1.42-.25-2.05H10v3.72h5.5c-.15.96-.74 2.31-2.04 3.22v2.45h3.16c1.89-1.73 2.98-4.3 2.98-7.34z" fill="#4285F4"/>
                            <path d="M13.46 15.13c-.83.59-1.96 1-3.46 1-2.64 0-4.88-1.74-5.68-4.15H1.07v2.52C2.72 17.75 6.09 20 10 20c2.7 0 4.96-.89 6.62-2.42l-3.16-2.45z" fill="#34A853"/>
                            <path d="M3.99 10c0-.69.12-1.35.32-1.97V5.51H1.07A9.973 9.973 0 000 10c0 1.61.39 3.14 1.07 4.49l3.24-2.52c-.2-.62-.32-1.28-.32-1.97z" fill="#FBBC05"/>
                            <path d="M10 3.88c1.88 0 3.13.81 3.85 1.48l2.84-2.76C14.96.99 12.7 0 10 0 6.09 0 2.72 2.25 1.07 5.51l3.24 2.52C5.12 5.62 7.36 3.88 10 3.88z" fill="#EA4335"/>
                          </svg>
                          <span>Masuk dengan Google</span>
                        </a>
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