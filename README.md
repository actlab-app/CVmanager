# CVmanager

![Laravel](https://img.shields.io/badge/Laravel-12-ff2d20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-fb70a9?logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-38bdf8?logo=tailwindcss&logoColor=white)

CVmanager, Laravel ve Livewire ile geliştirilmiş çok dilli CV ve portfolyo yönetim uygulamasıdır. Uygulama; CV kayıtları, hakkımda sayfası, iletişim bilgileri, teknoloji kataloğu ve portfolyo projeleri için yönetilebilir bir admin paneli sunar.

## Özellikler

- Çok dilli içerik yönetimi
- CV bölümleri için sıralanabilir tekrar alanları
- Hakkımda, iletişim ve portfolyo sayfaları
- Proje görselleri ve teknoloji logoları için dosya yükleme
- Portfolyo projeleri için yayın durumu, teknoloji seçimi ve detay sayfaları
- İletişim formu ve mesaj yönetimi
- Laravel Fortify tabanlı kimlik doğrulama
- Pest ve PHPUnit ile test altyapısı

## Teknoloji Yığını

| Katman | Teknoloji |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Livewire, Flux, Tailwind CSS, Vite |
| Kimlik Doğrulama | Laravel Fortify |
| Test | Pest, PHPUnit |
| Veritabanı | SQLite, MySQL veya Laravel tarafından desteklenen diğer veritabanları |
| Paketler | Spatie Permission, Spatie Translatable, Spatie Media Library, Laravel Excel |

## Proje Yapısı

```text
app/                 Uygulama sınıfları, Livewire bileşenleri ve modeller
config/              Laravel ve uygulama ayarları
database/            Migration, factory ve seeder dosyaları
public/images/       Statik görseller
resources/           Blade, CSS ve JavaScript kaynakları
routes/              Route dosyaları
tests/               Feature ve unit testleri
```

## Gereksinimler

- PHP 8.2+
- Composer
- Node.js ve npm
- SQLite, MySQL veya Laravel tarafından desteklenen bir veritabanı
- Flux Pro erişimi

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Geliştirme ortamını başlatmak için:

```bash
composer run dev
```

Testleri çalıştırmak için:

```bash
composer test
```

## Ortam Ayarları

`.env.example` örnek yapılandırma dosyasıdır. Uygulama ortamına ait gerçek değerler `.env` dosyasında tanımlanmalıdır.

Öne çıkan değişkenler:

```env
APP_NAME=CVmanager
APP_URL=http://localhost
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Production ortamında `APP_ENV`, `APP_DEBUG`, `APP_URL`, veritabanı, mail servisi ve güvenlik anahtarı değerleri ortama uygun şekilde yapılandırılmalıdır.



## Flux Pro

Bu proje `livewire/flux-pro` paketine ihtiyaç duyar. Flux Pro kurulumu yapılmadan bağımlılıklar tam olarak yüklenmez ve uygulama çalışmaz. Kurulumdan önce Flux Pro lisansı ve Composer erişimi yapılandırılmalıdır.

## Lisans

Bu proje Laravel tabanlı bir uygulamadır. Kullanılan üçüncü parti ve lisanslı paketlerin lisans koşulları ayrıca dikkate alınmalıdır.
