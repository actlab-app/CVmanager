<?php

namespace Database\Seeders;

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use Illuminate\Database\Seeder;

class PortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $project = PortfolioProject::firstOrNew(['slug' => 'cv-manager']);

        $project->fill([
            'status' => 'active',
            'project_date' => '2026-06-13',
            'live_url' => '/cv',
            'repository_url' => null,
            'technologies' => PortfolioTechnology::query()->ordered()->pluck('slug')->all(),
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
                'tr' => 'Çok dilli özgeçmiş yönetimi, dinamik bölüm düzenleme ve yazdırılabilir public CV deneyimini tek uygulamada birleştiren kişisel portfolio projesi.',
                'en' => 'A personal portfolio project combining multilingual resume management, dynamic section editing and a printable public CV experience.',
            ],
            'detailed_description' => [
                'tr' => 'CV Manager; özgeçmiş içeriğini Türkçe ve İngilizce olarak yönetmek, bölümleri sıralamak ve A4 uyumlu bir public CV üretmek için geliştirildi.',
                'en' => 'CV Manager was built to manage resume content in Turkish and English, reorder sections and generate an A4-compatible public CV.',
            ],
            'project_type' => [
                'tr' => 'Portfolio Yönetim Aracı',
                'en' => 'Portfolio Management Tool',
            ],
            'role' => [
                'tr' => 'Full-Stack Developer',
                'en' => 'Full-Stack Developer',
            ],
            'duration' => [
                'tr' => '2 hafta / iteratif',
                'en' => '2 weeks / iterative',
            ],
            'platform' => [
                'tr' => 'Responsive web',
                'en' => 'Responsive web',
            ],
            'features' => [
                'tr' => [
                    ['icon' => 'languages', 'title' => 'Çoklu Dil', 'description' => 'Türkçe ve İngilizce içerikler aynı kayıt üzerinde yönetilir.'],
                    ['icon' => 'list-restart', 'title' => 'Dinamik Bölümler', 'description' => 'CV satırları eklenebilir, silinebilir ve iki dilde birlikte sıralanabilir.'],
                    ['icon' => 'sparkles', 'title' => 'İkon Seçici', 'description' => 'Lucide ikonları arama ve anlık önizleme ile seçilir.'],
                    ['icon' => 'printer', 'title' => 'A4 Baskı', 'description' => 'Public CV görünümü tek sayfa A4 olarak yazdırılabilir.'],
                    ['icon' => 'moon-star', 'title' => 'Tema Desteği', 'description' => 'Arayüz açık ve koyu tema arasında geçiş yapar.'],
                    ['icon' => 'smartphone', 'title' => 'Responsive Tasarım', 'description' => 'Yönetim ve sunum sayfaları farklı ekran boyutlarına uyum sağlar.'],
                ],
                'en' => [
                    ['icon' => 'languages', 'title' => 'Multilingual', 'description' => 'Turkish and English content is managed on the same record.'],
                    ['icon' => 'list-restart', 'title' => 'Dynamic Sections', 'description' => 'Resume rows can be added, removed and reordered across both languages.'],
                    ['icon' => 'sparkles', 'title' => 'Icon Picker', 'description' => 'Lucide icons are selected with search and instant preview.'],
                    ['icon' => 'printer', 'title' => 'A4 Printing', 'description' => 'The public CV can be printed as a single A4 page.'],
                    ['icon' => 'moon-star', 'title' => 'Theme Support', 'description' => 'The interface switches between light and dark themes.'],
                    ['icon' => 'smartphone', 'title' => 'Responsive Design', 'description' => 'Admin and presentation pages adapt to different screen sizes.'],
                ],
            ],
            'technical_decisions' => [
                'tr' => [
                    ['label' => 'Mimari', 'value' => 'Laravel full-page Livewire bileşenleri'],
                    ['label' => 'Çeviri', 'value' => 'Spatie Translatable ile JSON kolonları'],
                    ['label' => 'UI Sistemi', 'value' => 'Flux UI ve ortak Blade bileşenleri'],
                    ['label' => 'İkonlar', 'value' => 'Lucide ikon kataloğu ve dinamik seçici'],
                    ['label' => 'Dosya Yapısı', 'value' => 'Admin ve web katmanları ayrı Livewire modülleri'],
                    ['label' => 'Doğrulama', 'value' => 'Pest feature testleri ve production Vite build'],
                ],
                'en' => [
                    ['label' => 'Architecture', 'value' => 'Laravel full-page Livewire components'],
                    ['label' => 'Translations', 'value' => 'JSON columns with Spatie Translatable'],
                    ['label' => 'UI System', 'value' => 'Flux UI and shared Blade components'],
                    ['label' => 'Icons', 'value' => 'Lucide icon catalog and dynamic picker'],
                    ['label' => 'Structure', 'value' => 'Separate Livewire modules for admin and web'],
                    ['label' => 'Verification', 'value' => 'Pest feature tests and production Vite build'],
                ],
            ],
            'metrics' => [
                'tr' => [
                    ['value' => '2', 'label' => 'Desteklenen dil', 'icon' => 'languages'],
                    ['value' => '5', 'label' => 'Dinamik CV bölümü', 'icon' => 'layout-list'],
                    ['value' => '36', 'label' => 'Geçen otomatik test', 'icon' => 'test-tube-diagonal'],
                    ['value' => 'A4', 'label' => 'Yazdırma uyumu', 'icon' => 'printer'],
                ],
                'en' => [
                    ['value' => '2', 'label' => 'Supported languages', 'icon' => 'languages'],
                    ['value' => '5', 'label' => 'Dynamic CV sections', 'icon' => 'layout-list'],
                    ['value' => '36', 'label' => 'Passing automated tests', 'icon' => 'test-tube-diagonal'],
                    ['value' => 'A4', 'label' => 'Print compatibility', 'icon' => 'printer'],
                ],
            ],
        ];
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
