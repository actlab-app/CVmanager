<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Referans Tokeni Gerekli</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-white antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-xl text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-400 text-zinc-950">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v4" />
                    <path d="M12 17h.01" />
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                </svg>
            </div>
            <h1 class="mt-6 text-3xl font-black tracking-tight sm:text-4xl">Referans tokeni gerekiyor</h1>
            <p class="mt-3 text-sm leading-6 text-zinc-300 sm:text-base">
                Bu CV bağlantısı yalnızca size özel oluşturulan referans linkiyle açılabilir. Lütfen başvuru veya paylaşım sırasında verilen bağlantıyı kullanın.
            </p>
        </section>
    </main>
</body>
</html>
