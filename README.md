# CVmanager

![Laravel](https://img.shields.io/badge/Laravel-12-ff2d20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-fb70a9?logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-38bdf8?logo=tailwindcss&logoColor=white)
![Tests](https://img.shields.io/badge/tests-passing-22c55e)

CVmanager, Laravel 12 ve Livewire ile geliştirilmiş çok dilli CV, portfolyo ve kişisel web sitesi yönetim uygulamasıdır. Admin panelinden CV kayıtları, hakkımda içeriği, iletişim bilgileri, teknoloji kataloğu ve portfolyo projeleri yönetilebilir.

## ✨ Özellikler

- 🧩 Livewire tabanlı admin paneli
- 🌍 Türkçe ve İngilizce içerik yönetimi
- 📄 CV bölümleri, ikonlar ve sıralanabilir tekrar alanları
- 🖼️ Hakkımda görselleri, proje görselleri ve teknoloji logoları için dosya yükleme
- 💼 Yayınlanabilir portfolyo projeleri
- 🛠️ Teknoloji kataloğu ve ikon/logo yönetimi
- ✉️ İletişim formu ve mesaj yönetimi
- 🔐 Laravel Fortify ile kimlik doğrulama altyapısı
- 🧪 Pest testleri ve GitHub Actions workflow dosyaları

## 🧱 Teknoloji Yığını

| Katman | Teknoloji |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Livewire, Flux, Tailwind CSS, Vite |
| Auth | Laravel Fortify |
| Test | Pest, PHPUnit |
| Veri | SQLite veya Laravel destekli diğer veritabanları |
| Paketler | Spatie Permission, Spatie Translatable, Spatie Media Library, Laravel Excel |

## 📁 Proje Yapısı

```text
app/                 Uygulama sınıfları, Livewire componentleri ve modeller
config/              Laravel ve uygulama ayarları
database/            Migration, factory ve seeder dosyaları
public/images/       Repo ile gelen statik görseller
resources/           Blade, CSS ve JavaScript kaynakları
routes/              Web ve console route dosyaları
tests/               Feature ve unit testleri
```

## 🚀 Kurulum

Gereksinimler:

- PHP 8.2+
- Composer
- Node.js ve npm
- SQLite, MySQL veya Laravel tarafından desteklenen başka bir veritabanı
- Flux Pro lisansı ve Composer erişimi

Kurulum komutları:

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

## ⚙️ Ortam Ayarları

`.env.example` sadece örnek yapılandırma içindir. Gerçek ortam değerleri `.env` dosyasında tutulmalıdır.

Öne çıkan değişkenler:

```env
APP_NAME=CVmanager
APP_URL=http://localhost
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Production ortamında özellikle şunları güncelle:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://alan-adin.com`
- Veritabanı bilgileri
- Mail servis bilgileri
- Güvenli `APP_KEY`

## 🖼️ Upload ve Git Notları

Admin panelden yüklenen dosyalar Git'e alınmaz. Bu dosyalar çalışma zamanı çıktısıdır ve sunucuda saklanmalıdır.

Ignore edilen upload alanları:

- `public/uploads/`
- `public/images/about/uploads/`
- `public/images/technologies/catalog/`
- `public/images/portfolio/*/*`
- `public/storage`
- `storage/`

Repo'da sadece uygulama kodu, migration/seeder dosyaları ve seçilmiş statik demo görselleri tutulur. `.env`, SQLite veritabanı, cache, log, `vendor`, `node_modules` ve build çıktıları Git'e alınmaz.

## 🔒 Flux Pro Notu

`packages/livewire/flux-pro` klasörü public repo'ya dahil edilmez. Bu paket lisanslı/pro bağımlılık olduğu için projeyi kuracak kişinin kendi Flux Pro erişimini ayarlaması gerekir.

Bu repo public kullanılacaksa Composer yapılandırmasında Flux Pro için lisanslı kurulum akışı ayrıca netleştirilmelidir. Aksi halde temiz bir klonda `composer install` sırasında Flux Pro bağımlılığı çözülemeyebilir.

## ✅ Test Durumu

Son yerel doğrulama:

```text
71 passed
7 skipped
```

Atlanan testler iki aşamalı doğrulama özelliği kapalıyken beklenen şekilde skip edilir.

## 🌐 GitHub'a Yayınlama

Bu klasörde Git reposu `main` branch ile hazırlandı ve remote şu adrese bağlandı:

```bash
https://github.com/actlab-app/CVmanager.git
```

GitHub'da public repo oluşturduktan sonra ilk push:

```bash
git push -u origin main
```

Repo oluştururken GitHub arayüzünde README, `.gitignore` veya license ekleme seçenekleri işaretlenmemelidir; bu dosyalar yerel projede zaten vardır.

## 📌 Lisans

Bu proje Laravel starter yapısı üzerinde geliştirilmiştir. Public kullanım, projedeki üçüncü parti ve lisanslı bağımlılıkların lisans koşullarına uygun olmalıdır.
