/* ============================================
   I18N.JS — Bilingual Support (ID / EN)
   Language switcher & translations
   ============================================ */

const translations = {
  id: {
    // Navbar
    'nav.home': 'Beranda',
    'nav.about': 'Tentang',
    'nav.skills': 'Keahlian',
    'nav.projects': 'Proyek',
    'nav.services': 'Layanan',
    'nav.contact': 'Kontak',
    'nav.cta': 'Hubungi Saya',

    // Hero
    'hero.greeting': 'Halo, saya',
    'hero.tagline': 'Mahasiswa Ilmu Komputer & Web Developer yang bersemangat membangun solusi digital kreatif dan fungsional.',
    'hero.cta': 'Hubungi Saya →',

    // About
    'about.label': 'Tentang Saya',
    'about.title': 'Membangun Solusi Digital yang Bermakna',
    'about.desc': 'Saya Pandita Lasman, mahasiswa Ilmu Komputer di Universitas Lampung angkatan 2023. Saya memiliki passion dalam web development dan teknologi informasi. Dengan pengalaman membangun berbagai proyek, saya siap membantu mewujudkan ide digital Anda menjadi kenyataan.',
    'about.desc2': 'Saya percaya bahwa teknologi yang baik harus sederhana, fungsional, dan memberikan dampak nyata bagi penggunanya.',
    'about.stat1': 'Proyek Selesai',
    'about.stat2': 'Teknologi',
    'about.stat3': 'Tahun Pengalaman',
    'about.stat4': 'Klien Puas',

    // Skills
    'skills.label': 'Keahlian',
    'skills.title': 'Teknologi yang Saya Kuasai',
    'skills.frontend': 'Frontend',
    'skills.backend': 'Backend',
    'skills.tools': 'Tools & Lainnya',

    // Projects
    'projects.label': 'Proyek',
    'projects.title': 'Karya Terbaru Saya',
    'projects.all': 'Semua',
    'projects.web': 'Website',
    'projects.system': 'Sistem',
    'projects.view': 'Lihat Proyek →',
    'projects.p1.title': 'Sistem Cuti DKP Lampung',
    'projects.p1.desc': 'Sistem manajemen cuti online untuk Dinas Kelautan dan Perikanan Provinsi Lampung. Fitur approval multi-level dan dashboard admin.',
    'projects.p2.title': 'E-Commerce Platform',
    'projects.p2.desc': 'Platform toko online modern dengan fitur keranjang belanja, pembayaran, dan manajemen produk.',
    'projects.p3.title': 'Portfolio Website',
    'projects.p3.desc': 'Website portfolio personal dengan desain modern dan animasi interaktif.',
    'projects.p4.title': 'Sistem Informasi Akademik',
    'projects.p4.desc': 'Sistem informasi untuk manajemen data akademik mahasiswa termasuk KRS, nilai, dan jadwal.',

    // Services
    'services.label': 'Layanan',
    'services.title': 'Apa yang Bisa Saya Bantu',
    'services.s1.title': 'Web Development',
    'services.s1.desc': 'Pembuatan website modern, responsif, dan cepat sesuai kebutuhan bisnis Anda.',
    'services.s2.title': 'Sistem Informasi',
    'services.s2.desc': 'Pengembangan sistem informasi custom untuk efisiensi operasional organisasi Anda.',
    'services.s3.title': 'UI/UX Design',
    'services.s3.desc': 'Desain antarmuka yang intuitif dan pengalaman pengguna yang menyenangkan.',
    'services.s4.title': 'Maintenance & Support',
    'services.s4.desc': 'Perawatan berkala, update keamanan, dan dukungan teknis untuk website Anda.',

    // Testimonials
    'testimonials.label': 'Testimoni',
    'testimonials.title': 'Apa Kata Mereka',
    'testimonials.t1.quote': 'Hasil kerjanya sangat memuaskan. Website yang dibuat modern, cepat, dan sesuai dengan kebutuhan kami. Sangat profesional!',
    'testimonials.t1.author': 'Ahmad Rizki',
    'testimonials.t1.role': 'Pemilik Toko Online',
    'testimonials.t2.quote': 'Pandita sangat responsif dan kreatif. Sistem informasi yang dibuat sangat membantu efisiensi kerja di kantor kami.',
    'testimonials.t2.author': 'Siti Nurhaliza',
    'testimonials.t2.role': 'Staff Admin Instansi',
    'testimonials.t3.quote': 'Desain website-nya keren dan profesional. Proses pengerjaan cepat dan komunikasinya sangat baik. Recommended!',
    'testimonials.t3.author': 'Budi Santoso',
    'testimonials.t3.role': 'Pengusaha UMKM',

    // Contact
    'contact.label': 'Kontak',
    'contact.title': 'Mari Bekerja Sama',
    'contact.subtitle': 'Punya proyek atau ide? Jangan ragu untuk menghubungi saya.',
    'contact.email': 'Email',
    'contact.phone': 'Telepon',
    'contact.location': 'Lokasi',
    'contact.location.value': 'Bandar Lampung, Indonesia',
    'contact.form.name': 'Nama Anda',
    'contact.form.email': 'Email Anda',
    'contact.form.message': 'Pesan Anda',
    'contact.form.submit': 'Kirim Pesan →',
    'contact.form.name.placeholder': 'Masukkan nama...',
    'contact.form.email.placeholder': 'Masukkan email...',
    'contact.form.message.placeholder': 'Tulis pesan Anda...',

    // Footer
    'footer.copyright': '© 2024 Pandita Lasman. Hak cipta dilindungi.',
  },

  en: {
    // Navbar
    'nav.home': 'Home',
    'nav.about': 'About',
    'nav.skills': 'Skills',
    'nav.projects': 'Projects',
    'nav.services': 'Services',
    'nav.contact': 'Contact',
    'nav.cta': 'Get in Touch',

    // Hero
    'hero.greeting': "Hello, I'm",
    'hero.tagline': 'Computer Science student & Web Developer passionate about building creative and functional digital solutions.',
    'hero.cta': 'Get in Touch →',

    // About
    'about.label': 'About Me',
    'about.title': 'Building Meaningful Digital Solutions',
    'about.desc': "I'm Pandita Lasman, a Computer Science student at Universitas Lampung, class of 2023. I have a passion for web development and information technology. With experience building various projects, I'm ready to help turn your digital ideas into reality.",
    'about.desc2': 'I believe that good technology should be simple, functional, and make a real impact for its users.',
    'about.stat1': 'Projects Done',
    'about.stat2': 'Technologies',
    'about.stat3': 'Years Experience',
    'about.stat4': 'Happy Clients',

    // Skills
    'skills.label': 'My Skills',
    'skills.title': 'Technologies I Work With',
    'skills.frontend': 'Frontend',
    'skills.backend': 'Backend',
    'skills.tools': 'Tools & Others',

    // Projects
    'projects.label': 'Projects',
    'projects.title': 'My Recent Works',
    'projects.all': 'All',
    'projects.web': 'Website',
    'projects.system': 'System',
    'projects.view': 'View Project →',
    'projects.p1.title': 'Leave Management System',
    'projects.p1.desc': 'Online leave management system for Lampung Province Marine and Fisheries Department. Multi-level approval and admin dashboard.',
    'projects.p2.title': 'E-Commerce Platform',
    'projects.p2.desc': 'Modern online store platform with shopping cart, payment, and product management features.',
    'projects.p3.title': 'Portfolio Website',
    'projects.p3.desc': 'Personal portfolio website with modern design and interactive animations.',
    'projects.p4.title': 'Academic Information System',
    'projects.p4.desc': 'Information system for student academic data management including course registration, grades, and schedules.',

    // Services
    'services.label': 'Services',
    'services.title': 'What I Can Help With',
    'services.s1.title': 'Web Development',
    'services.s1.desc': 'Building modern, responsive, and fast websites tailored to your business needs.',
    'services.s2.title': 'Information Systems',
    'services.s2.desc': "Custom information system development to improve your organization's operational efficiency.",
    'services.s3.title': 'UI/UX Design',
    'services.s3.desc': 'Intuitive interface design and delightful user experience.',
    'services.s4.title': 'Maintenance & Support',
    'services.s4.desc': 'Regular maintenance, security updates, and technical support for your website.',

    // Testimonials
    'testimonials.label': 'Testimonials',
    'testimonials.title': 'What They Say',
    'testimonials.t1.quote': 'The work results are very satisfying. The website built is modern, fast, and suits our needs. Very professional!',
    'testimonials.t1.author': 'Ahmad Rizki',
    'testimonials.t1.role': 'Online Store Owner',
    'testimonials.t2.quote': 'Pandita is very responsive and creative. The information system built greatly helps work efficiency in our office.',
    'testimonials.t2.author': 'Siti Nurhaliza',
    'testimonials.t2.role': 'Government Staff Admin',
    'testimonials.t3.quote': 'The website design is cool and professional. The work process is fast and communication is excellent. Recommended!',
    'testimonials.t3.author': 'Budi Santoso',
    'testimonials.t3.role': 'SME Business Owner',

    // Contact
    'contact.label': 'Contact',
    'contact.title': "Let's Work Together",
    'contact.subtitle': "Have a project or idea? Don't hesitate to reach out.",
    'contact.email': 'Email',
    'contact.phone': 'Phone',
    'contact.location': 'Location',
    'contact.location.value': 'Bandar Lampung, Indonesia',
    'contact.form.name': 'Your Name',
    'contact.form.email': 'Your Email',
    'contact.form.message': 'Your Message',
    'contact.form.submit': 'Send Message →',
    'contact.form.name.placeholder': 'Enter your name...',
    'contact.form.email.placeholder': 'Enter your email...',
    'contact.form.message.placeholder': 'Write your message...',

    // Footer
    'footer.copyright': '© 2024 Pandita Lasman. All rights reserved.',
  }
};

let currentLang = 'id';

/**
 * Switch all translatable content to the specified language
 */
function switchLanguage(lang) {
  currentLang = lang;
  const langData = translations[lang];
  if (!langData) return;

  // Update all elements with data-i18n attribute
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (langData[key]) {
      el.textContent = langData[key];
    }
  });

  // Update all placeholders with data-i18n-placeholder attribute
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (langData[key]) {
      el.placeholder = langData[key];
    }
  });

  // Update lang toggle display
  const langToggle = document.getElementById('langToggle');
  if (langToggle) {
    const spans = langToggle.querySelectorAll('span');
    spans.forEach(span => span.classList.remove('lang-active'));
    if (lang === 'id') {
      spans[0].classList.add('lang-active');
    } else {
      spans[1].classList.add('lang-active');
    }
  }

  // Update html lang attribute
  document.documentElement.lang = lang === 'id' ? 'id' : 'en';

  // Save preference
  localStorage.setItem('portfolio-lang', lang);
}

document.addEventListener('DOMContentLoaded', () => {
  // Language toggle click handler
  const langToggle = document.getElementById('langToggle');
  if (langToggle) {
    langToggle.addEventListener('click', () => {
      const newLang = currentLang === 'id' ? 'en' : 'id';
      switchLanguage(newLang);
    });
  }

  // Load saved language preference
  const savedLang = localStorage.getItem('portfolio-lang');
  if (savedLang && translations[savedLang]) {
    switchLanguage(savedLang);
  }
});
