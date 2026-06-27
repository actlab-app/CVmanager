<?php

namespace Database\Seeders;

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use Illuminate\Database\Seeder;

class PortfolioProjectSeeder extends Seeder
{
    private const TECHNOLOGIES = [
        'php',
        'laravel',
        'livewire',
        'flux-ui',
        'tailwindcss',
        'alpinejs',
        'vite',
        'spatie-translatable',
        'laravel-fortify',
        'mysql',
        'lucide',
    ];

    public function run(): void
    {
        $project = PortfolioProject::firstOrNew(['slug' => 'cv-manager']);

        $project->fill([
            'status' => 'active',
            'project_date' => '2026-06-13',
            'live_url' => $project->live_url ?? '/cv',
            'repository_url' => $project->repository_url,
            'technologies' => $this->availableTechnologySlugs(),
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        foreach ($this->translations() as $field => $translations) {
            foreach ($translations as $locale => $value) {
                $project->setTranslation($field, $locale, $value);
            }
        }

        $project->save();

        if ($project->images()->doesntExist()) {
            foreach ($this->images() as $index => $image) {
                $record = $project->images()->create([
                    'path' => $image['path'],
                    'sort_order' => $index,
                ]);

                foreach (['title', 'description'] as $field) {
                    foreach ($image[$field] as $locale => $value) {
                        $record->setTranslation($field, $locale, $value);
                    }
                }

                $record->save();
            }
        }
    }

    private function translations(): array
    {
        return [
            'title' => [
                'tr' => 'CV Manager',
                'en' => 'CV Manager',
            ],
            'short_description' => [
                'tr' => 'Livewire tabanlı SPA yönetim paneli, çok dilli CV/portfolio yönetimi, modern-klasik CV çıktıları ve referans token analitiğini tek uygulamada birleştiren kişisel portfolio yönetim sistemi.',
                'en' => 'A personal portfolio management system combining a Livewire-powered SPA admin panel, multilingual CV/portfolio management, modern-classic CV outputs and reference token analytics.',
            ],
            'detailed_description' => [
                'tr' => 'CV Manager; kişisel CV, hakkımda, iletişim, teknoloji kataloğu, portfolio projeleri ve referans token yönetimini aynı Laravel uygulamasında toplayan full-stack bir yönetim paneli olarak geliştirildi. Admin paneli tam sayfa Livewire bileşenlerinden oluşur ve `wire:navigate` bağlantılarıyla sayfa geçişleri SPA hissinde gerçekleşir. Public tarafta da CV, hakkımda, portfolio ve iletişim sayfaları aynı navigasyon yaklaşımıyla daha akıcı bir deneyim sunar. Sistem Türkçe/İngilizce içerik yönetimi, modern ve ATS odaklı klasik CV teması, yazdırılabilir CV çıktısı, QR/portfolio bağlantısı, Flux UI tabanlı yönetim arayüzü ve Lucide ikon seçicileriyle içerik üretimini hızlandırır. Referans token modülü, kişiye özel public linkler üretir; ziyaretleri hashlenmiş IP ve user agent bilgisiyle kaydeder, tarih aralığına göre token performansı, sayfa dağılımı, IP/user agent kırılımı ve ziyaretçi temizleme akışlarını yönetir. Proje, kişisel portfolio sitesini sadece vitrin olmaktan çıkarıp ölçülebilir, yönetilebilir ve hızlı güncellenebilir bir ürün haline getirmeye odaklanır.',
                'en' => 'CV Manager was built as a full-stack Laravel admin system that brings CV content, about content, contact data, technology catalog, portfolio projects and reference token management into a single application. The admin panel is composed of full-page Livewire components, and route transitions use `wire:navigate` to provide an SPA-like experience. The public CV, about, portfolio and contact pages use the same navigation approach for a smoother visitor flow. The system supports Turkish/English content management, modern and ATS-oriented classic CV themes, printable CV output, QR/portfolio linking, a Flux UI based admin interface and Lucide icon pickers to speed up content editing. The reference token module creates personalized public links; records visits with hashed IP and user agent data; and provides date-filtered token performance, page distribution, IP/user-agent breakdowns and visitor cleanup flows. The project focuses on turning a personal portfolio site from a static showcase into a measurable, maintainable and quickly editable product.',
            ],
            'project_type' => [
                'tr' => 'Kişisel Portfolio ve CV Yönetim Sistemi',
                'en' => 'Personal Portfolio and CV Management System',
            ],
            'role' => [
                'tr' => 'Full-Stack Laravel / Livewire Geliştiricisi',
                'en' => 'Full-Stack Laravel / Livewire Developer',
            ],
            'duration' => [
                'tr' => 'İteratif ürün geliştirme',
                'en' => 'Iterative product development',
            ],
            'platform' => [
                'tr' => 'SPA hissinde admin panel + public portfolio sitesi',
                'en' => 'SPA-like admin panel + public portfolio website',
            ],
            'features' => [
                'tr' => [
                    ['icon' => 'layout-dashboard', 'title' => 'Livewire Admin Paneli', 'description' => 'CV, hakkımda, iletişim, portfolio, teknoloji ve referans token yönetimi tam sayfa Livewire bileşenleriyle yönetilir.'],
                    ['icon' => 'route', 'title' => 'SPA Hissinde Navigasyon', 'description' => 'Admin paneli ve public web arayüzündeki ana geçişler `wire:navigate` ile daha hızlı ve kesintisiz hale getirildi.'],
                    ['icon' => 'languages', 'title' => 'Çok Dilli İçerik', 'description' => 'CV, portfolio, hakkımda ve iletişim içerikleri Türkçe ve İngilizce olarak aynı kayıtlar üzerinde yönetilir.'],
                    ['icon' => 'file-text', 'title' => 'Modern ve Klasik CV', 'description' => 'Görsel modern CV yanında ATS odaklı, yazdırılabilir klasik CV teması panelden seçilebilir.'],
                    ['icon' => 'chart-column', 'title' => 'Referans Token Analitiği', 'description' => 'Kişiye özel referans linkleri, tarih filtreli ziyaret grafikleri, sayfa dağılımı ve IP/user agent kırılımlarıyla takip edilir.'],
                    ['icon' => 'shield-check', 'title' => 'Kontrollü Public Erişim', 'description' => 'Tokensiz ziyaretleri engelleme, noindex, iletişim gizliliği ve signup kapatma gibi güvenlik odaklı ayarlar bulunur.'],
                ],
                'en' => [
                    ['icon' => 'layout-dashboard', 'title' => 'Livewire Admin Panel', 'description' => 'CV, about, contact, portfolio, technology and reference token management are handled through full-page Livewire components.'],
                    ['icon' => 'route', 'title' => 'SPA-Like Navigation', 'description' => 'Main transitions across the admin panel and public website use `wire:navigate` for a faster and more continuous experience.'],
                    ['icon' => 'languages', 'title' => 'Multilingual Content', 'description' => 'CV, portfolio, about and contact content are managed in Turkish and English on the same records.'],
                    ['icon' => 'file-text', 'title' => 'Modern and Classic CV', 'description' => 'A visual modern CV and an ATS-oriented printable classic CV theme can be selected from the panel.'],
                    ['icon' => 'chart-column', 'title' => 'Reference Token Analytics', 'description' => 'Personalized reference links are tracked with date-filtered visit charts, page distribution and IP/user-agent breakdowns.'],
                    ['icon' => 'shield-check', 'title' => 'Controlled Public Access', 'description' => 'Security-focused controls include token-only public access, noindex, contact privacy and disabled signup.'],
                ],
            ],
            'technical_decisions' => [
                'tr' => [
                    ['label' => 'Mimari', 'value' => 'Laravel üzerinde admin ve public tarafı ayrı tam sayfa Livewire bileşenleriyle kurgulandı'],
                    ['label' => 'SPA Deneyimi', 'value' => 'Admin menüleri ve public navigasyon `wire:navigate` ile sayfa yenilemeden ilerleyen bir akışa dönüştürüldü'],
                    ['label' => 'İçerik Modeli', 'value' => 'Spatie Translatable ile CV ve portfolio içerikleri JSON kolonlarda TR/EN olarak tutuldu'],
                    ['label' => 'CV Çıktısı', 'value' => 'Modern görsel CV ve ATS odaklı klasik CV aynı veri kaynağından üretilir'],
                    ['label' => 'Referans Takibi', 'value' => 'Referans token ziyaretleri hashlenmiş IP/user agent, sayfa ve tarih bilgisiyle kaydedilir'],
                    ['label' => 'Analitik', 'value' => 'Dashboard ve token detay modalında tarih filtreli token, sayfa, IP ve user agent dağılımları gösterilir'],
                    ['label' => 'UI Sistemi', 'value' => 'Flux UI, Tailwind ve Lucide ikon seçicileriyle tutarlı bir yönetim deneyimi oluşturuldu'],
                    ['label' => 'Doğrulama', 'value' => 'Kritik yönetim ve public akışlar Pest feature testleriyle korunur'],
                ],
                'en' => [
                    ['label' => 'Architecture', 'value' => 'Admin and public surfaces are structured as separate full-page Livewire components on Laravel'],
                    ['label' => 'SPA Experience', 'value' => 'Admin menus and public navigation use `wire:navigate` to move through the app without full page reloads'],
                    ['label' => 'Content Model', 'value' => 'CV and portfolio content is stored as TR/EN JSON translations with Spatie Translatable'],
                    ['label' => 'CV Output', 'value' => 'Modern visual CV and ATS-oriented classic CV are generated from the same data source'],
                    ['label' => 'Reference Tracking', 'value' => 'Reference token visits store hashed IP/user-agent data with page and timestamp metadata'],
                    ['label' => 'Analytics', 'value' => 'Dashboard and token detail modal show date-filtered token, page, IP and user-agent distributions'],
                    ['label' => 'UI System', 'value' => 'Flux UI, Tailwind and Lucide icon pickers provide a consistent admin editing experience'],
                    ['label' => 'Verification', 'value' => 'Critical admin and public flows are covered with Pest feature tests'],
                ],
            ],
            'metrics' => [
                'tr' => [
                    ['icon' => 'route', 'label' => 'Admin + public geçiş', 'value' => 'SPA'],
                    ['icon' => 'file-text', 'label' => 'CV tema seçeneği', 'value' => '2'],
                    ['icon' => 'chart-column', 'label' => 'Referans analitiği', 'value' => 'Token'],
                    ['icon' => 'languages', 'label' => 'İçerik dili', 'value' => 'TR/EN'],
                ],
                'en' => [
                    ['icon' => 'route', 'label' => 'Admin + public flow', 'value' => 'SPA'],
                    ['icon' => 'file-text', 'label' => 'CV theme options', 'value' => '2'],
                    ['icon' => 'chart-column', 'label' => 'Reference analytics', 'value' => 'Token'],
                    ['icon' => 'languages', 'label' => 'Content languages', 'value' => 'TR/EN'],
                ],
            ],
        ];
    }

    private function availableTechnologySlugs(): array
    {
        $availableSlugs = PortfolioTechnology::query()
            ->active()
            ->whereIn('slug', self::TECHNOLOGIES)
            ->pluck('slug')
            ->all();

        return array_values(array_intersect(self::TECHNOLOGIES, $availableSlugs));
    }

    private function images(): array
    {
        return [
            [
                'path' => 'images/portfolio/cv-manager/admin-dashboard.png',
                'title' => ['tr' => 'Çok Dilli Yönetim Paneli', 'en' => 'Multilingual Admin Panel'],
                'description' => [
                    'tr' => 'CV bölümleri, sıralama kontrolleri ve ikon seçicileri tek ekranda yönetilir.',
                    'en' => 'CV sections, ordering controls and icon pickers are managed on one screen.',
                ],
            ],
            [
                'path' => 'images/portfolio/cv-manager/public-cv.png',
                'title' => ['tr' => 'Yazdırılabilir Public CV', 'en' => 'Printable Public CV'],
                'description' => [
                    'tr' => 'A4 çıktıya uyumlu, açık ve koyu tema destekli özgeçmiş görünümü.',
                    'en' => 'An A4-compatible resume view supporting light and dark themes.',
                ],
            ],
            [
                'path' => 'images/portfolio/cv-manager/mobile-showcase.png',
                'title' => ['tr' => 'Responsive Deneyim', 'en' => 'Responsive Experience'],
                'description' => [
                    'tr' => 'Yönetim ve görüntüleme akışları mobil ekranlarda da kullanılabilir kalır.',
                    'en' => 'Admin and presentation flows remain usable on mobile screens.',
                ],
            ],
        ];
    }
}
