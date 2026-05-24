<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian Kantin</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* CSS Khusus agar angkanya sangat besar untuk TV */
        .text-huge { font-size: 180px; font-weight: 900; line-height: 1; text-shadow: 4px 4px 10px rgba(0,0,0,0.1); }
        .bg-dark-tv { background-color: #f4f7f6; height: 100vh; overflow: hidden; }
        .overlay-start { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; justify-content: center; align-items: center; }
    </style>
</head>
<body class="bg-dark-tv">
    <audio id="audioDingdong" src="{{ asset('audio/beep.mp3') }}" preload="auto"></audio>

    <div id="startOverlay" class="overlay-start">
        <button onclick="mulaiLayar()" class="btn btn-gradient-info btn-lg p-5" style="font-size: 28px; border-radius: 20px;">
            <i class="mdi mdi-television-play"></i> KLIK UNTUK MENGAKTIFKAN LAYAR TV
        </button>
    </div>

    <div class="container-fluid h-100 d-flex flex-column justify-content-center align-items-center text-center">
        <h2 class="text-uppercase text-muted mb-4" style="letter-spacing: 5px;">Nomor Antrian Dipanggil</h2>
        
        <div class="card border-0 shadow-lg" style="border-radius: 40px; width: 70%; min-width: 350px;">
            <div class="card-body py-5">
                <h4 id="namaPanggilan" class="text-info mb-3" style="font-size: 35px; font-weight: bold;">STANDBY</h4>
                <div id="nomorPanggilan" class="text-huge text-dark">-</div>
            </div>
        </div>
        
        <div class="mt-5 text-muted" style="font-size: 20px;">
            <i class="mdi mdi-information"></i> Silakan menuju meja kasir untuk mengambil pesanan Anda
        </div>
    </div>

    <script>
        let tvAktif = false;
        let nomorTerakhir = null; // Mencegah suara robot ngomong berulang-ulang untuk nomor yang sama

        // Fungsi membuka gembok suara browser
        function mulaiLayar() {
            tvAktif = true;
            document.getElementById('startOverlay').style.display = 'none';
            
            // Pancing putar audio agar browser memberi izin akses suara ke halaman ini
            let audio = document.getElementById('audioDingdong');
            audio.play().catch(e => {});
            audio.pause();
            audio.currentTime = 0;
        }

        // Menerima Sinyal Radio dari Server (SSE)
        const sseSource = new EventSource("{{ route('antrian.stream') }}");

        sseSource.addEventListener('update-antrian', function(event) {
            if (!tvAktif) return; // TV mati, abaikan sinyal

            const paketData = JSON.parse(event.data);

            // Cek apakah ada panggilan dan apakah nomornya baru
            if (paketData.sekarang && paketData.sekarang.nomor !== nomorTerakhir) {
                
                nomorTerakhir = paketData.sekarang.nomor;

                // 1. Ubah Angka dan Nama di Layar
                document.getElementById('nomorPanggilan').innerText = paketData.sekarang.nomor;
                document.getElementById('namaPanggilan').innerText = paketData.sekarang.nama.toUpperCase();

                // 2. Jalankan Audio dan Suara Robot
                panggilSuara(paketData.sekarang.nomor, paketData.sekarang.nama);
            }
        });

        function panggilSuara(nomor, nama) {
            const audio = document.getElementById('audioDingdong');
            audio.currentTime = 0;
            audio.play();

            // Setelah suara "ding-dong" selesai, nyalakan robot pembaca teks
            audio.onended = function() {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    
                    let kalimat = "Nomor antrian " + nomor + ", atas nama " + nama + ". Silakan menuju meja kasir.";
                    const pesan = new SpeechSynthesisUtterance(kalimat);
                    
                    pesan.lang = 'id-ID'; // Bahasa Indonesia
                    pesan.rate = 0.85;    // Kecepatan membaca
                    pesan.volume = 1.0;   // Volume maksimal
                    
                    window.speechSynthesis.speak(pesan);
                }
            };
        }
    </script>
</body>
</html>