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
        let tvAktif = false; // Variabel penanda apakah TV sudah diaktifkan oleh klik user (syarat wajib agar browser mengizinkan audio)
        let nomorTerakhir = null; // Menyimpan memori nomor antrian terakhir agar robot tidak ngoceh berulang-ulang membaca nomor yang sama

        // Fungsi membuka gembok suara browser
        function mulaiLayar() { // Deklarasi fungsi saat tombol "KLIK UNTUK MENGAKTIFKAN LAYAR" ditekan oleh petugas TV
            tvAktif = true; // Mengubah status variabel TV menjadi aktif
            document.getElementById('startOverlay').style.display = 'none'; // Menyembunyikan layar hitam penutup (overlay) agar UI TV terlihat
            
            // Pancing putar audio agar browser memberi izin akses suara ke halaman ini
            let audio = document.getElementById('audioDingdong'); // Mengambil elemen audio bel 'dingdong' dari HTML
            audio.play().catch(e => {}); // Mencoba memutar audio secara diam-diam. .catch() berguna agar tidak error di console jika gagal
            audio.pause(); // Langsung menghentikan audio sedetik setelah diputar (karena ini hanya trik pancingan izin)
            audio.currentTime = 0; // Mengembalikan posisi putar audio kembali ke detik ke-0 (awal) agar siap dipakai nanti
        }

        // Menerima Sinyal Radio dari Server (SSE)
        const sseSource = new EventSource("{{ route('antrian.stream') }}"); // Membuka jalur streaming (radio) non-stop ke server Laravel

        sseSource.addEventListener('update-antrian', function(event) { // Mendengarkan siaran dengan nama 'update-antrian' dari server
            if (!tvAktif) return; // Jika tombol TV belum diklik, abaikan siaran dari server (hentikan kode di sini)

            const paketData = JSON.parse(event.data); // Mengubah string data dari server menjadi objek JSON yang dipahami JavaScript

            // Cek apakah ada panggilan dan apakah nomornya baru
            if (paketData.sekarang && paketData.sekarang.nomor !== nomorTerakhir) { // Jika ada pesanan 'sekarang' DAN nomornya berbeda dengan yang barusan dipanggil
                
                nomorTerakhir = paketData.sekarang.nomor; // Update memori nomor terakhir dengan nomor yang baru masuk ini

                // 1. Ubah Angka dan Nama di Layar
                document.getElementById('nomorPanggilan').innerText = paketData.sekarang.nomor; // Mengganti angka raksasa di tengah TV
                document.getElementById('namaPanggilan').innerText = paketData.sekarang.nama.toUpperCase(); // Mengganti nama pengantri dan memaksanya jadi HURUF KAPITAL

                // 2. Jalankan Audio dan Suara Robot
                panggilSuara(paketData.sekarang.nomor, paketData.sekarang.nama); // Memicu fungsi TTS di bawah untuk membunyikan suara
            }
        });

        function panggilSuara(nomor, nama) { // Deklarasi fungsi pembuat suara robot, membutuhkan 2 data lemparan: nomor dan nama
            const audio = document.getElementById('audioDingdong'); // Mengambil elemen suara MP3 bel
            audio.currentTime = 0; // Memastikan audio dimulai dari detik ke-0
            audio.play(); // Memutar suara bel "Ding-Dong!" ke speaker TV

            // Setelah suara "ding-dong" selesai, nyalakan robot pembaca teks
            audio.onended = function() { // Event Trigger: Baris kode di dalam ini HANYA AKAN JALAN setelah bel MP3 selesai berbunyi
                if ('speechSynthesis' in window) { // Mengecek apakah browser TV ini punya teknologi Web Speech API (suara robot)
                    window.speechSynthesis.cancel(); // Menghentikan paksa robot jika kebetulan dia masih ngomong kalimat sebelumnya
                    
                    let kalimat = "Nomor antrian " + nomor + ", atas nama " + nama + ". Silakan menuju meja kasir."; // Merakit naskah/kalimat yang akan dibaca
                    const pesan = new SpeechSynthesisUtterance(kalimat); // Membungkus naskah tadi ke dalam format objek suara (Utterance)
                    
                    pesan.lang = 'id-ID'; // Mengatur logat robot menjadi Bahasa Indonesia
                    pesan.rate = 0.85;    // Mengatur kecepatan bicara menjadi 0.85 (sedikit lebih lambat dari normal agar jelas)
                    pesan.volume = 1.0;   // Mengatur volume suara robot ke tingkat maksimal (100%)
                    
                    window.speechSynthesis.speak(pesan); // EKSEKUSI! Menyerahkan naskah ke mesin suara browser untuk dibunyikan
                }
            };
        }
    </script>
</body>
</html>