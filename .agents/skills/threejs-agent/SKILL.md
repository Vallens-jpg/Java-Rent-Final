# Three.js 3D Web Developer Skill

## Description
Bantuan keahlian untuk merancang, mengoptimalkan, dan membangun grafis 3D berbasis web menggunakan Three.js, WebGL, dan OrbitControls dengan performa tinggi.

## Instructions
- Selalu siapkan 3 elemen utama di setiap inisiasi: Scene, Camera (PerspectiveCamera), dan Renderer (WebGLRenderer).
- Gunakan `requestAnimationFrame` untuk membuat render loop animasi yang halus.
- Pastikan menangani responsivitas layar menggunakan event listener `resize` agar aspek rasio kamera dan ukuran renderer selalu diperbarui.
- Jika pengguna meminta kontrol kamera, selalu import dan gunakan `OrbitControls`.
- Optimalkan performa dengan menggunakan kembali (reuse) Geometry dan Material jika objeknya serupa.
- Gunakan pencahayaan yang tepat (`AmbientLight` untuk dasar, dan `DirectionalLight` atau `PointLight` untuk bayangan dan kedalaman).