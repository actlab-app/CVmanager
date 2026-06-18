<?php

namespace Database\Seeders;

use App\Models\CvRecord;
use Illuminate\Database\Seeder;

class CvRecordSeeder extends Seeder
{
    public function run(): void
    {
        $demoRecord = new CvRecord();
        $demoRecord->full_name = 'Jane Doe';

        $tr = [
            'job_title' => 'Kıdemli Yazılım Geliştirici',
            'about_content' => 'Projelerin uçtan uca teslim süreçlerinde aktif olarak yer alıyorum. Takım çalışmasına yatkınım ve yeni teknolojilere hızlıca adapte olabilirim.<br><br>Yüksek performanslı backend servisleri ve kullanıcı dostu arayüzler inşa etmekten keyif alıyorum.',
            'quick_infos' => [
                ['icon' => 'briefcase', 'title' => 'Deneyim', 'value' => '5+ Yıl'],
                ['icon' => 'map-pin', 'title' => 'Konum', 'value' => 'İstanbul / Türkiye'],
                ['icon' => 'graduation-cap', 'title' => 'Eğitim', 'value' => 'Bilgisayar Mühendisliği'],
                ['icon' => 'languages', 'title' => 'Dil', 'value' => 'İngilizce (C1)'],
            ],
            'educations' => [
                ['icon' => 'graduation-cap', 'degree' => 'Lisans', 'school' => 'Bilgisayar Mühendisliği | Teknik Üniversite'],
                ['icon' => 'graduation-cap', 'degree' => 'Lise', 'school' => 'Fen Lisesi'],
            ],
            'experiences' => [
                ['icon' => 'building', 'company' => 'Tech Corp', 'description' => 'Backend Geliştirici (2020 - Günümüz). API mimarisi tasarımı ve geliştirilmesi.'],
                ['icon' => 'layout', 'company' => 'Creative Agency', 'description' => 'Junior Full-Stack Geliştirici (2018 - 2020). Kurumsal firma web siteleri.'],
            ],
            'skills' => [
                ['icon' => 'code', 'category' => 'Frontend', 'details' => 'React, Vue, Tailwind, JavaScript ES6+'],
                ['icon' => 'server', 'category' => 'Backend', 'details' => 'Laravel, Node.js, Express, RESTful API'],
                ['icon' => 'database', 'category' => 'Veritabanı', 'details' => 'PostgreSQL, MySQL, Redis, MongoDB'],
            ],
            'project_types' => [
                ['icon' => 'shopping-cart', 'type' => 'E-Ticaret', 'description' => 'Büyük ölçekli, mikroservis mimarili alışveriş platformları.'],
                ['icon' => 'users', 'type' => 'İnsan Kaynakları', 'description' => 'Kurum içi performans ve süreç yönetim yazılımları.'],
            ]
        ];

        $en = [
            'job_title' => 'Senior Software Developer',
            'about_content' => 'I actively participate in the end-to-end delivery processes of projects. I am a team player and can quickly adapt to new technologies.<br><br>I enjoy building high-performance backend services and user-friendly interfaces.',
            'quick_infos' => [
                ['icon' => 'briefcase', 'title' => 'Experience', 'value' => '5+ Years'],
                ['icon' => 'map-pin', 'title' => 'Location', 'value' => 'Istanbul / Turkey'],
                ['icon' => 'graduation-cap', 'title' => 'Education', 'value' => 'Computer Engineering'],
                ['icon' => 'languages', 'title' => 'Languages', 'value' => 'English (C1)'],
            ],
            'educations' => [
                ['icon' => 'graduation-cap', 'degree' => 'Bachelor', 'school' => 'Computer Engineering | Technical University'],
                ['icon' => 'graduation-cap', 'degree' => 'High School', 'school' => 'Science High School'],
            ],
            'experiences' => [
                ['icon' => 'building', 'company' => 'Tech Corp', 'description' => 'Backend Developer (2020 - Present). API architecture design and development.'],
                ['icon' => 'layout', 'company' => 'Creative Agency', 'description' => 'Junior Full-Stack Developer (2018 - 2020). Corporate websites.'],
            ],
            'skills' => [
                ['icon' => 'code', 'category' => 'Frontend', 'details' => 'React, Vue, Tailwind, JavaScript ES6+'],
                ['icon' => 'server', 'category' => 'Backend', 'details' => 'Laravel, Node.js, Express, RESTful API'],
                ['icon' => 'database', 'category' => 'Database', 'details' => 'PostgreSQL, MySQL, Redis, MongoDB'],
            ],
            'project_types' => [
                ['icon' => 'shopping-cart', 'type' => 'E-Commerce', 'description' => 'Large scale shopping platforms with microservice architecture.'],
                ['icon' => 'users', 'type' => 'Human Resources', 'description' => 'Internal performance and process management software.'],
            ]
        ];

        $fields = ['job_title', 'about_content', 'quick_infos', 'educations', 'experiences', 'skills', 'project_types'];

        foreach ($fields as $field) {
            $demoRecord->setTranslation($field, 'tr', $tr[$field]);
            $demoRecord->setTranslation($field, 'en', $en[$field]);
        }

        $demoRecord->save();
    }
}
