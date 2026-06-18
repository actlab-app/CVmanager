# CVmanager

Laravel 12, Livewire, Flux ve Tailwind CSS ile gelistirilmis CV ve portfolyo yonetim uygulamasi.

## Gereksinimler

- PHP 8.2+
- Composer
- Node.js ve npm
- SQLite veya Laravel tarafindan desteklenen baska bir veritabani
- Flux Pro lisansi ve Composer erisimi

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Gelisim sunucusu icin:

```bash
composer run dev
```

Testler icin:

```bash
composer test
```

## Public Repo Notlari

- `.env`, `vendor`, `node_modules`, SQLite veritabani, cache, log ve storage ciktisi Git'e alinmaz.
- `packages/livewire/flux-pro` klasoru public repo'ya dahil edilmez. Flux Pro lisansli bir bagimlilik oldugu icin kurulum yapacak kisinin kendi Composer erisimini ayarlamasi gerekir.
- Gercek ortam bilgileri `.env.example` icine yazilmamalidir; sadece ornek/deger gerektirmeyen degiskenler tutulmalidir.
