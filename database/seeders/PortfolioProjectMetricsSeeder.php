<?php

namespace Database\Seeders;

use App\Models\PortfolioProject;
use Illuminate\Database\Seeder;

class PortfolioProjectMetricsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->metricsBySlug() as $slug => $translations) {
            $project = PortfolioProject::query()->where('slug', $slug)->first();

            if (! $project) {
                continue;
            }

            foreach ($translations as $locale => $metrics) {
                $project->setTranslation('metrics', $locale, $metrics);
            }

            $project->save();
        }
    }

    private function metricsBySlug(): array
    {
        return [
            'etkilesimli-animasyonlu-landing-page' => [
                'tr' => [
                    ['icon' => 'layers-3', 'label' => 'Katmanlı sahne', 'value' => 'Parallax'],
                    ['icon' => 'mouse-pointer-2', 'label' => 'Scroll kontrollü akış', 'value' => 'Custom'],
                    ['icon' => 'palette', 'label' => 'Manuel görsel üretim', 'value' => 'PS'],
                    ['icon' => 'image', 'label' => 'Responsive medya', 'value' => 'WebP'],
                ],
                'en' => [
                    ['icon' => 'layers-3', 'label' => 'Layered scene', 'value' => 'Parallax'],
                    ['icon' => 'mouse-pointer-2', 'label' => 'Scroll-driven flow', 'value' => 'Custom'],
                    ['icon' => 'palette', 'label' => 'Manual visual design', 'value' => 'PS'],
                    ['icon' => 'image', 'label' => 'Responsive media', 'value' => 'WebP'],
                ],
            ],
            'qrpos' => [
                'tr' => [
                    ['icon' => 'qr-code', 'label' => 'Masadan sipariş', 'value' => 'QR'],
                    ['icon' => 'building-2', 'label' => 'Çok kiracılı yapı', 'value' => 'Tenant'],
                    ['icon' => 'sliders-horizontal', 'label' => 'Varyant/seçenek', 'value' => 'Menu'],
                    ['icon' => 'credit-card', 'label' => 'Ödeme ve fiyat', 'value' => 'POS'],
                ],
                'en' => [
                    ['icon' => 'qr-code', 'label' => 'Table ordering', 'value' => 'QR'],
                    ['icon' => 'building-2', 'label' => 'Multi-tenant setup', 'value' => 'Tenant'],
                    ['icon' => 'sliders-horizontal', 'label' => 'Variants/options', 'value' => 'Menu'],
                    ['icon' => 'credit-card', 'label' => 'Payment and pricing', 'value' => 'POS'],
                ],
            ],
            'networking-ve-uye-yonetim-platformu' => [
                'tr' => [
                    ['icon' => 'users', 'label' => 'Üye organizasyonu', 'value' => 'CRM'],
                    ['icon' => 'calendar-days', 'label' => 'Toplantı akışı', 'value' => 'Event'],
                    ['icon' => 'shuffle', 'label' => 'Karma masa eşleşmesi', 'value' => 'Match'],
                    ['icon' => 'badge-dollar-sign', 'label' => 'Üyelik/ödeme takibi', 'value' => 'Finance'],
                ],
                'en' => [
                    ['icon' => 'users', 'label' => 'Member organization', 'value' => 'CRM'],
                    ['icon' => 'calendar-days', 'label' => 'Meeting workflow', 'value' => 'Event'],
                    ['icon' => 'shuffle', 'label' => 'Mixed-table matching', 'value' => 'Match'],
                    ['icon' => 'badge-dollar-sign', 'label' => 'Membership/payment tracking', 'value' => 'Finance'],
                ],
            ],
            'danismanlik-portal' => [
                'tr' => [
                    ['icon' => 'calendar-clock', 'label' => 'Uygunluk takvimi', 'value' => 'Slot'],
                    ['icon' => 'video', 'label' => 'Online görüşme', 'value' => 'Session'],
                    ['icon' => 'credit-card', 'label' => 'Randevu ödemesi', 'value' => 'Pay'],
                    ['icon' => 'users-round', 'label' => 'Danışman/danışan rolleri', 'value' => 'Roles'],
                ],
                'en' => [
                    ['icon' => 'calendar-clock', 'label' => 'Availability calendar', 'value' => 'Slot'],
                    ['icon' => 'video', 'label' => 'Online session', 'value' => 'Session'],
                    ['icon' => 'credit-card', 'label' => 'Appointment payment', 'value' => 'Pay'],
                    ['icon' => 'users-round', 'label' => 'Consultant/client roles', 'value' => 'Roles'],
                ],
            ],
            'gorsel-ai-destekli-kurumsal-web' => [
                'tr' => [
                    ['icon' => 'sparkles', 'label' => 'AI görsel üretimi', 'value' => 'AI'],
                    ['icon' => 'layout-template', 'label' => 'Kurumsal sayfa yapısı', 'value' => 'CMS'],
                    ['icon' => 'images', 'label' => 'Galeri/referans sunumu', 'value' => 'Media'],
                    ['icon' => 'package', 'label' => 'Ürün koleksiyonları', 'value' => 'Catalog'],
                ],
                'en' => [
                    ['icon' => 'sparkles', 'label' => 'AI image generation', 'value' => 'AI'],
                    ['icon' => 'layout-template', 'label' => 'Corporate page structure', 'value' => 'CMS'],
                    ['icon' => 'images', 'label' => 'Gallery/reference showcase', 'value' => 'Media'],
                    ['icon' => 'package', 'label' => 'Product collections', 'value' => 'Catalog'],
                ],
            ],
            'kurumsal-proje-operasyon-yonetim-sistemi' => [
                'tr' => [
                    ['icon' => 'workflow', 'label' => 'Uçtan uca akış', 'value' => 'Brief → Proje'],
                    ['icon' => 'factory', 'label' => 'Üretim kontrolü', 'value' => 'Stage'],
                    ['icon' => 'kanban-square', 'label' => 'Görev takibi', 'value' => 'Task'],
                    ['icon' => 'file-text', 'label' => 'Teklif/sözleşme çıktısı', 'value' => 'PDF'],
                ],
                'en' => [
                    ['icon' => 'workflow', 'label' => 'End-to-end flow', 'value' => 'Brief → Project'],
                    ['icon' => 'factory', 'label' => 'Production control', 'value' => 'Stage'],
                    ['icon' => 'kanban-square', 'label' => 'Task tracking', 'value' => 'Task'],
                    ['icon' => 'file-text', 'label' => 'Offer/contract output', 'value' => 'PDF'],
                ],
            ],
            'urun-sihirbazli-e-ticaret-platformu' => [
                'tr' => [
                    ['icon' => 'wand-sparkles', 'label' => 'Uyumlu ürün bulma', 'value' => 'Wizard'],
                    ['icon' => 'smartphone', 'label' => 'Cihaz/model eşleşmesi', 'value' => 'Fit'],
                    ['icon' => 'boxes', 'label' => 'Varyant stok/fiyat', 'value' => 'SKU'],
                    ['icon' => 'credit-card', 'label' => 'Online ödeme', 'value' => 'PayTR'],
                ],
                'en' => [
                    ['icon' => 'wand-sparkles', 'label' => 'Compatible product finder', 'value' => 'Wizard'],
                    ['icon' => 'smartphone', 'label' => 'Device/model matching', 'value' => 'Fit'],
                    ['icon' => 'boxes', 'label' => 'Variant stock/pricing', 'value' => 'SKU'],
                    ['icon' => 'credit-card', 'label' => 'Online payment', 'value' => 'PayTR'],
                ],
            ],
        ];
    }
}
