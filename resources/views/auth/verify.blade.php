<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Verifikasi OTP - Purple Admin</title>
    
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
      
      .otp-input-container {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 30px 0;
      }
      .otp-input {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #e9e9e9;
        border-radius: 8px;
        transition: all 0.3s;
      }
      .otp-input:focus {
        border-color: #9a55ff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(154, 85, 255, 0.1);
      }
      .otp-input.filled {
        border-color: #9a55ff;
        background-color: #f8f5ff;
      }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-md-8 col-lg-6 mx-auto">
              <div class="auth-form-light text-center p-5 shadow-lg">
                
                @if(session('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                @endif

                @if(session('error'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                @endif

                <div class="mb-4">
                  <div class="d-inline-block p-3 rounded-circle bg-light">
                    <i class="mdi mdi-shield-check text-primary" style="font-size: 48px;"></i>
                  </div>
                </div>

                <h4 class="font-weight-bold mb-2">Verifikasi OTP</h4>
                <p class="text-muted mb-4">
                  Masukkan kode 6 digit yang telah dikirim ke email Anda
                </p>

                <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                  @csrf
                  
                  <div class="otp-input-container">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="0">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="1">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="2">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="3">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="4">
                    <input type="text" class="otp-input form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="5">
                  </div>

                  <input type="hidden" name="otp" id="otpValue" required>

                  @error('otp')
                    <div class="text-danger mb-3">
                      <strong>{{ $message }}</strong>
                    </div>
                  @enderror

                  <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-gradient-primary btn-lg text-white" id="verifyBtn" disabled>
                      <i class="mdi mdi-check-circle me-2"></i>Verifikasi OTP
                    </button>
                  </div>
                </form>

                <div class="mt-4">
                  <p class="text-muted small mb-2">Tidak menerima kode?</p>
                  <form method="POST" action="{{ route('otp.resend') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-primary p-0">
                      <i class="mdi mdi-refresh me-1"></i>Kirim Ulang OTP
                    </button>
                  </form>
                </div>

                <div class="mt-4">
                  <a href="{{ route('login') }}" class="text-muted small">
                    <i class="mdi mdi-arrow-left me-1"></i>Kembali ke Login
                  </a>
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

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const otpValue = document.getElementById('otpValue');
        const verifyBtn = document.getElementById('verifyBtn');
        const form = document.getElementById('otpForm');

        function updateOTPValue() {
          let otp = '';
          inputs.forEach(input => {
            otp += input.value;
            if (input.value) {
              input.classList.add('filled');
            } else {
              input.classList.remove('filled');
            }
          });
          otpValue.value = otp;
          
          if (otp.length === 6) {
            verifyBtn.disabled = false;
          } else {
            verifyBtn.disabled = true;
          }
        }

        inputs.forEach((input, index) => {
          input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 1) {
              if (index < inputs.length - 1) {
                inputs[index + 1].focus();
              }
            }
            
            updateOTPValue();
          });

          input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
              inputs[index - 1].focus();
            }
          });

          input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text');
            const digits = pasteData.replace(/[^0-9]/g, '').split('');
            
            digits.forEach((digit, i) => {
              if (index + i < inputs.length) {
                inputs[index + i].value = digit;
              }
            });
            
            updateOTPValue();
            
            const lastIndex = Math.min(index + digits.length - 1, inputs.length - 1);
            inputs[lastIndex].focus();
          });
        });

        inputs[0].focus();
      });
    </script>
  </body>
</html>