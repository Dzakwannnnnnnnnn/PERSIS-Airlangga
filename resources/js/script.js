document.addEventListener("DOMContentLoaded", function() {
    const nav = document.querySelector(".glass");
    
    // Pastikan nav ada sebelum menjalankan kode
    if (!nav) return;

    let lastScrollY = window.scrollY;
    let timer = null;
    const threshold = 5; // Jarak minimal scroll (pixel) untuk memicu aksi

    // Inisialisasi awal agar nav terlihat
    nav.classList.add("nav-visible");

    window.addEventListener("scroll", () => {
        const currentScrollY = window.scrollY;
        const scrollDiff = Math.abs(currentScrollY - lastScrollY);

        // 1. Jika di paling atas, paksa muncul
        if (currentScrollY <= 10) {
            nav.classList.remove("nav-hidden");
            nav.classList.add("nav-visible");
            clearTimeout(timer);
            lastScrollY = currentScrollY;
            return;
        }

        // 2. Hanya proses jika scroll lebih besar dari threshold (biar nggak sensitif banget)
        if (scrollDiff >= threshold) {
            if (currentScrollY > lastScrollY) {
                // Scroll ke Bawah
                nav.classList.add("nav-hidden");
                nav.classList.remove("nav-visible");
            } else {
                // Scroll ke Atas
                nav.classList.remove("nav-hidden");
                nav.classList.add("nav-visible");
            }
            lastScrollY = currentScrollY;
        }

        // 3. Logika Diam 1 Detik (Muncul lagi)
        clearTimeout(timer);
        timer = setTimeout(() => {
            nav.classList.remove("nav-hidden");
            nav.classList.add("nav-visible");
        }, 1000); 
    }, { passive: true }); // Passive true untuk performa scroll yang lebih ringan
});

// Tambahkan ini di dalam DOMContentLoaded pada script.js
const observerOptions = {
    threshold: 0.15 // Elemen akan muncul jika 15% bagiannya sudah terlihat
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            // Opsional: unobserve jika ingin animasi hanya sekali saja
            // observer.unobserve(entry.target); 
        } else {
            // Hapus baris ini jika kamu tidak ingin animasi berulang saat scroll ke atas
            entry.target.classList.remove('active'); 
        }
    });
}, observerOptions);

// Daftarkan semua elemen dengan class 'reveal' ke observer
document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));