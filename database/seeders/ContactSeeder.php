<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $settings = ContactSetting::firstOrNew();

        if ($settings->exists) {
            return;
        }

        foreach ($this->translations() as $field => $translations) {
            foreach ($translations as $locale => $value) {
                $settings->setTranslation($field, $locale, $value);
            }
        }

        $settings->save();
    }

    private function translations(): array
    {
        return [
            'title' => [
                'tr' => 'İletişime Geçin',
                'en' => 'Get in Touch',
            ],
            'intro' => [
                'tr' => 'Projeler, iş birlikleri veya teknik konular için benimle iletişime geçebilirsiniz.',
                'en' => 'You can contact me about projects, collaborations or technical topics.',
            ],
            'form_title' => [
                'tr' => 'Mesaj Gönderin',
                'en' => 'Send a Message',
            ],
            'privacy_notice' => [
                'tr' => 'Bazı kişisel iletişim verileri şu anda kullanıcı tarafından gizlenmiştir. Uygun bir iletişim yöntemi bulamıyorsanız formu doldurabilirsiniz.',
                'en' => 'Some personal contact details are currently hidden by the user. If you cannot find a suitable contact method, please use the form.',
            ],
            'success_message' => [
                'tr' => 'Mesajınız alındı. En kısa sürede dönüş yapacağım.',
                'en' => 'Your message has been received. I will get back to you as soon as possible.',
            ],
            'location' => [
                'tr' => 'İzmir, Türkiye',
                'en' => 'Izmir, Turkey',
            ],
        ];
    }
}
