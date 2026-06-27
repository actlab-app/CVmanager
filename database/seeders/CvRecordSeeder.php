<?php

namespace Database\Seeders;

use App\Models\CvRecord;
use Illuminate\Database\Seeder;

class CvRecordSeeder extends Seeder
{
    public function run(): void
    {
        $demoRecord = CvRecord::query()->firstOrNew();
        $demoRecord->full_name = 'Deniz Yilmaz';
        $demoRecord->qr_url = route('portfolio.index');

        $tr = [
            'job_title' => 'Kidemli Full-Stack Yazilim Gelistirici',
            'about_content' => 'Laravel, Livewire ve modern JavaScript ekosistemiyle olceklenebilir web uygulamalari gelistiren full-stack yazilimciyim. Urun ekipleriyle yakin calisip is ihtiyacini teknik cozumlere donusturur, yonetim panelleri, API servisleri ve kullanici odakli arayuzler insa ederim.<br><br>Performans, surdurulebilir mimari, test edilebilirlik ve temiz teslimat sureclerine odaklanirim. Fikir asamasindan canliya alma ve bakim surecine kadar tum urun yasam dongusunde aktif rol alirim.',
            'classic_profile_summary' => 'Laravel, Livewire ve modern frontend teknolojileriyle olceklenebilir web uygulamalari, yonetim panelleri ve API entegrasyonlari gelistiren full-stack yazilim gelistirici. Is gereksinimlerini sade, test edilebilir ve surdurulebilir teknik cozumlere donusturur. Performans, kullanilabilirlik ve teslimat kalitesi odakli calisir.',
            'quick_infos' => [
                ['icon' => 'briefcase', 'title' => 'Deneyim', 'value' => '6+ yil'],
                ['icon' => 'map-pin', 'title' => 'Konum', 'value' => 'Istanbul / Turkiye'],
                ['icon' => 'graduation-cap', 'title' => 'Egitim', 'value' => 'Bilgisayar Muhendisligi'],
                ['icon' => 'languages', 'title' => 'Dil', 'value' => 'Turkce, Ingilizce'],
                ['icon' => 'monitor-smartphone', 'title' => 'Odak', 'value' => 'Web uygulamalari ve paneller'],
            ],
            'educations' => [
                ['icon' => 'graduation-cap', 'degree' => 'Lisans', 'school' => 'Bilgisayar Muhendisligi | Istanbul Teknik Universitesi'],
                ['icon' => 'award', 'degree' => 'Sertifika', 'school' => 'Modern Laravel ve Test Odakli Gelistirme Programi'],
            ],
            'experiences' => [
                [
                    'icon' => 'building',
                    'company' => 'Nova Digital',
                    'description' => 'Kidemli Full-Stack Gelistirici (2022 - Gunumuz). SaaS urunleri, yonetim panelleri ve musteri portallari.',
                    'detailed_description' => "- Laravel ve Livewire ile cok dilli, yetki kontrollu yonetim panelleri gelistirdi.\n- Raporlama, dosya yukleme, bildirim ve entegrasyon sureclerini moduler hale getirdi.\n- Kritik is akislari icin feature testleri ve regresyon kontrolleri ekledi.",
                ],
                [
                    'icon' => 'layers',
                    'company' => 'Atlas Software Studio',
                    'description' => 'Backend Gelistirici (2019 - 2022). API servisleri, entegrasyonlar ve operasyonel araclar.',
                    'detailed_description' => "- REST API servisleri, kuyruk tabanli isler ve veri senkronizasyon surecleri tasarladi.\n- Odeme, CRM ve e-posta servisleriyle entegrasyonlar gelistirdi.\n- Eski kod parcalarini kademeli olarak test edilebilir servislere ayirdi.",
                ],
                [
                    'icon' => 'layout-dashboard',
                    'company' => 'Freelance Projeler',
                    'description' => 'Full-Stack Danisman (2018 - 2019). Kurumsal web siteleri ve ozel panel gelistirmeleri.',
                    'detailed_description' => "- Kucuk ve orta olcekli isletmeler icin hizli yayina alinabilir web cozumleri gelistirdi.\n- Icerik yonetimi, teklif formlari ve portfolyo modulleri hazirladi.",
                ],
            ],
            'skills' => [
                ['icon' => 'server', 'category' => 'Backend', 'details' => 'PHP, Laravel, Livewire, REST API, queue jobs, policy ve middleware yapilari'],
                ['icon' => 'code-xml', 'category' => 'Frontend', 'details' => 'Blade, Alpine.js, Tailwind CSS, responsive arayuzler, form deneyimi'],
                ['icon' => 'database', 'category' => 'Veritabani', 'details' => 'MySQL, PostgreSQL, SQLite, migration, indeksleme ve veri modelleme'],
                ['icon' => 'test-tube-diagonal', 'category' => 'Kalite', 'details' => 'Pest/PHPUnit feature testleri, validasyon, regresyon senaryolari'],
                ['icon' => 'cloud', 'category' => 'Operasyon', 'details' => 'Linux, deployment scriptleri, cache, queue ve temel sunucu optimizasyonlari'],
            ],
            'project_types' => [
                ['icon' => 'layout-dashboard', 'type' => 'Yonetim Panelleri', 'description' => 'Yetki kontrollu, cok dilli, raporlama ve medya yonetimi iceren operasyon panelleri.'],
                ['icon' => 'shopping-cart', 'type' => 'E-Ticaret ve Siparis', 'description' => 'Urun, stok, siparis, odeme ve kargo sureclerini yoneten web uygulamalari.'],
                ['icon' => 'plug', 'type' => 'API Entegrasyonlari', 'description' => 'CRM, odeme, e-posta, raporlama ve ucuncu parti servislerle guvenilir veri akislari.'],
                ['icon' => 'file-user', 'type' => 'Kisisel Portfolio / CV', 'description' => 'Dinamik icerik yonetimi, yazdirilabilir CV ve referans takip akislari.'],
            ],
        ];

        $en = [
            'job_title' => 'Senior Full-Stack Software Developer',
            'about_content' => 'I build scalable web applications with Laravel, Livewire and the modern JavaScript ecosystem. I work closely with product teams, translate business needs into technical solutions, and deliver admin panels, API services and user-focused interfaces.<br><br>My work is centered on performance, maintainable architecture, testability and reliable delivery. I contribute across the full product lifecycle from discovery to production support.',
            'classic_profile_summary' => 'Full-stack software developer building scalable web applications, admin panels and API integrations with Laravel, Livewire and modern frontend tooling. Turns business requirements into simple, testable and maintainable technical solutions. Focused on performance, usability and delivery quality.',
            'quick_infos' => [
                ['icon' => 'briefcase', 'title' => 'Experience', 'value' => '6+ Years'],
                ['icon' => 'map-pin', 'title' => 'Location', 'value' => 'Istanbul / Turkey'],
                ['icon' => 'graduation-cap', 'title' => 'Education', 'value' => 'Computer Engineering'],
                ['icon' => 'languages', 'title' => 'Languages', 'value' => 'Turkish, English'],
                ['icon' => 'monitor-smartphone', 'title' => 'Focus', 'value' => 'Web apps and dashboards'],
            ],
            'educations' => [
                ['icon' => 'graduation-cap', 'degree' => 'Bachelor', 'school' => 'Computer Engineering | Istanbul Technical University'],
                ['icon' => 'award', 'degree' => 'Certificate', 'school' => 'Modern Laravel and Test-Driven Development Program'],
            ],
            'experiences' => [
                [
                    'icon' => 'building',
                    'company' => 'Nova Digital',
                    'description' => 'Senior Full-Stack Developer (2022 - Present). SaaS products, admin dashboards and customer portals.',
                    'detailed_description' => "- Built multilingual, permission-aware admin panels with Laravel and Livewire.\n- Modularized reporting, file uploads, notifications and integration workflows.\n- Added feature tests and regression checks for critical business flows.",
                ],
                [
                    'icon' => 'layers',
                    'company' => 'Atlas Software Studio',
                    'description' => 'Backend Developer (2019 - 2022). API services, integrations and operational tooling.',
                    'detailed_description' => "- Designed REST API services, queue-based jobs and data synchronization workflows.\n- Built integrations with payment, CRM and email providers.\n- Gradually separated legacy code paths into testable services.",
                ],
                [
                    'icon' => 'layout-dashboard',
                    'company' => 'Freelance Projects',
                    'description' => 'Full-Stack Consultant (2018 - 2019). Corporate websites and custom dashboard builds.',
                    'detailed_description' => "- Delivered web solutions that small and mid-sized businesses could launch quickly.\n- Built content management, quote forms and portfolio modules.",
                ],
            ],
            'skills' => [
                ['icon' => 'server', 'category' => 'Backend', 'details' => 'PHP, Laravel, Livewire, REST API, queue jobs, policies and middleware'],
                ['icon' => 'code-xml', 'category' => 'Frontend', 'details' => 'Blade, Alpine.js, Tailwind CSS, responsive interfaces and form UX'],
                ['icon' => 'database', 'category' => 'Database', 'details' => 'MySQL, PostgreSQL, SQLite, migrations, indexing and data modeling'],
                ['icon' => 'test-tube-diagonal', 'category' => 'Quality', 'details' => 'Pest/PHPUnit feature tests, validation and regression scenarios'],
                ['icon' => 'cloud', 'category' => 'Operations', 'details' => 'Linux, deployment scripts, cache, queues and basic server optimization'],
            ],
            'project_types' => [
                ['icon' => 'layout-dashboard', 'type' => 'Admin Dashboards', 'description' => 'Permission-aware, multilingual operation panels with reporting and media management.'],
                ['icon' => 'shopping-cart', 'type' => 'E-Commerce and Orders', 'description' => 'Web applications managing products, stock, orders, payments and shipping workflows.'],
                ['icon' => 'plug', 'type' => 'API Integrations', 'description' => 'Reliable data flows with CRM, payment, email, reporting and third-party services.'],
                ['icon' => 'file-user', 'type' => 'Personal Portfolio / CV', 'description' => 'Dynamic content management, printable CV output and reference tracking flows.'],
            ],
        ];

        $fields = [
            'job_title',
            'about_content',
            'classic_profile_summary',
            'quick_infos',
            'educations',
            'experiences',
            'skills',
            'project_types',
        ];

        foreach ($fields as $field) {
            $demoRecord->setTranslation($field, 'tr', $tr[$field]);
            $demoRecord->setTranslation($field, 'en', $en[$field]);
        }

        $demoRecord->save();
    }
}
