# CVmanager

![Laravel](https://img.shields.io/badge/Laravel-12-ff2d20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-fb70a9?logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-38bdf8?logo=tailwindcss&logoColor=white)

CVmanager; CV, portfolyo ve kişisel profil içeriklerini yönetmek için geliştirilmiş çok dilli bir web uygulamasıdır.

**Canlı uygulama:** [cvm.actlab.app](https://cvm.actlab.app/cv?rt=GH)

## Neler sunar?

- Çok dilli içerik yönetimi
- CV bilgileri ve sıralanabilir içerik alanları
- Hakkımda, iletişim ve portfolyo sayfaları
- Portfolyo projeleri, teknoloji etiketleri ve proje görselleri
- Teknoloji logoları ve görseller için dosya yükleme
- İletişim formu ve gelen mesajların yönetimi
- Laravel Fortify ile kimlik doğrulama

## Teknolojiler

| Alan | Teknoloji |
| --- | --- |
| Uygulama | Laravel 12, PHP 8.2+ |
| Arayüz | Livewire, Flux, Tailwind CSS, Vite |
| Kimlik doğrulama | Laravel Fortify |
| Veritabanı | SQLite, MySQL veya Laravel destekli diğer veritabanları |
| Test | Pest, PHPUnit |
| Ek paketler | Spatie Permission, Translatable, Media Library, Laravel Excel |

## Gereksinimler

- PHP 8.2+
- Composer
- Node.js ve npm
- SQLite, MySQL veya Laravel tarafından desteklenen bir veritabanı

## Kurulum

```bash
git clone https://github.com/actlab-app/CVmanager.git
cd CVmanager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Yerel geliştirme ortamını başlatmak için:

```bash
composer run dev
```

Bu komut uygulama sunucusunu, kuyruk dinleyicisini ve Vite geliştirme sunucusunu birlikte çalıştırır.

## Testler

```bash
composer test
```

## Ortam ayarları

`.env.example` dosyasını `.env` olarak kopyaladıktan sonra uygulamaya uygun veritabanı, e-posta ve URL ayarlarını güncelleyin.

Örnek temel yapılandırma:

```env
APP_NAME=CVmanager
APP_URL=http://localhost
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Canlı ortamda özellikle `APP_ENV`, `APP_DEBUG`, `APP_URL`, veritabanı bağlantısı, e-posta ayarları ve `APP_KEY` değerlerinin güvenli şekilde yapılandırılması gerekir.

## Katkı

Hata bildirimi, iyileştirme önerisi veya kod katkısı için issue ya da pull request açabilirsiniz. Göndermeden önce testlerin çalıştığından emin olun.

## Lisans

Bu projede kullanılan üçüncü taraf ve lisanslı paketlerin kendi lisans koşulları geçerlidir.
