@php
    $isTurkish = app()->getLocale() === 'tr';
    $privacyHidden = (bool) $settings?->privacy_hidden;
@endphp

<div class="space-y-5">
    <div class="flex justify-end gap-2">
        @foreach (['tr' => 'TR', 'en' => 'EN'] as $locale => $label)
            <button type="button" wire:click="setLocale('{{ $locale }}')" @class([
                'cursor-pointer rounded-md px-3 py-1 text-[12px] font-bold',
                'bg-accent text-white' => app()->getLocale() === $locale,
                'bg-[var(--bg-card)] text-muted hover:text-ink' => app()->getLocale() !== $locale,
            ])>{{ $label }}</button>
        @endforeach
    </div>

    <header class="rounded-2xl border border-line bg-[var(--bg-card)] p-5 sm:p-7">
        <div class="flex items-center gap-2 text-[12px] font-bold uppercase tracking-[0.18em] text-accent">
            <i data-lucide="messages-square" class="h-4 w-4"></i>
            {{ $isTurkish ? 'Bağlantı Kuralım' : 'Let’s Connect' }}
        </div>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-ink sm:text-4xl">
            {{ $settings?->title ?: ($isTurkish ? 'İletişim' : 'Contact') }}
        </h1>
        <p class="mt-2 max-w-2xl text-[14px] leading-relaxed text-muted sm:text-[15px]">{{ $settings?->intro }}</p>
    </header>

    @if ($privacyHidden && $contactItems->contains('is_private', true))
        <div class="flex gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-amber-900 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-100">
            <i data-lucide="shield-alert" class="mt-0.5 h-5 w-5 shrink-0"></i>
            <p class="text-[12px] font-semibold leading-relaxed sm:text-[13px]">{{ $settings?->privacy_notice }}</p>
        </div>
    @endif

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
        <div class="space-y-5">
            <div class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-5">
                <div class="mb-4 flex items-center gap-2 rounded-lg bg-accentSoft px-3 py-2 text-[13px] font-black text-accentDark">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--bg-card)]">
                        <i data-lucide="contact-round" class="h-4 w-4"></i>
                    </span>
                    {{ $isTurkish ? 'İLETİŞİM KANALLARI' : 'CONTACT CHANNELS' }}
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @forelse ($contactItems as $item)
                        @php($hidden = $privacyHidden && $item->is_private)

                        @if ($item->url && ! $hidden)
                            <a href="{{ $item->url }}" @if (str_starts_with($item->url, 'http')) target="_blank" rel="noreferrer" @endif
                                class="group flex items-center gap-3 rounded-xl border border-line bg-soft p-3.5 transition hover:border-accent">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--bg-card)] text-accent">
                                    <x-contact-icon :name="$item->icon" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[10px] font-bold uppercase tracking-wide text-muted">{{ $item->label }}</span>
                                    <span class="mt-0.5 block truncate text-[13px] font-black text-ink">{{ $item->displayValue($privacyHidden) }}</span>
                                </span>
                                <i data-lucide="arrow-up-right" class="h-4 w-4 text-muted transition group-hover:text-accent"></i>
                            </a>
                        @else
                            <div class="flex items-center gap-3 rounded-xl border border-line bg-soft p-3.5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--bg-card)] text-accent">
                                    <x-contact-icon :name="$hidden ? 'lock-keyhole' : $item->icon" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-[10px] font-bold uppercase tracking-wide text-muted">{{ $item->label }}</span>
                                    <span class="mt-0.5 block truncate text-[13px] font-black text-ink">{{ $item->displayValue($privacyHidden) }}</span>
                                </span>
                                @if ($hidden)
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-[9px] font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                        {{ $isTurkish ? 'GİZLİ' : 'HIDDEN' }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="py-8 text-center text-sm text-muted">
                            {{ $isTurkish ? 'İletişim bilgileri yakında eklenecek.' : 'Contact details will be added soon.' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 justify-between ">
            @if ($settings?->location || $settings?->map_url)
                <div class="overflow-hidden rounded-2xl border border-line bg-[var(--bg-card)] mb-5">
                    @if ($settings?->map_url)
                        <iframe src="{{ $settings->map_url }}" class="h-56 w-full border-0" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="{{ $isTurkish ? 'Yaklaşık konum' : 'Approximate location' }}"></iframe>
                    @endif
                    @if ($settings?->location)
                        <div class="flex items-center gap-3 border-t border-line px-4 py-3">
                            <i data-lucide="map-pin" class="h-4 w-4 text-accent"></i>
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-wide text-muted">{{ $isTurkish ? 'Konum' : 'Location' }}</div>
                                <div class="mt-0.5 text-[13px] font-black text-ink">{{ $settings->location }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        <div class="rounded-2xl border border-line bg-[var(--bg-card)] p-4 sm:p-6">
            <div class="mb-5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accentSoft text-accent">
                    <i data-lucide="send" class="h-5 w-5"></i>
                </div>
                <h2 class="mt-3 text-xl font-black text-ink">{{ $settings?->form_title ?: ($isTurkish ? 'Mesaj Gönderin' : 'Send a Message') }}</h2>
                <p class="mt-1 text-[12px] text-muted">{{ $isTurkish ? 'Formdaki tüm alanlar zorunludur.' : 'All fields in the form are required.' }}</p>
            </div>

            @if ($sent)
                <div class="mb-4 flex gap-3 rounded-xl border border-emerald-300 bg-emerald-50 p-4 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-200">
                    <i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i>
                    <p class="text-[13px] font-semibold">{{ $settings?->success_message }}</p>
                </div>
            @endif

            <form wire:submit="submit" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $isTurkish ? 'Ad Soyad' : 'Name' }}</span>
                        <input type="text" wire:model="name" class="w-full rounded-xl border border-line bg-soft px-3.5 py-2.5 text-[13px] text-ink outline-none focus:border-accent" />
                        @error('name') <span class="mt-1 block text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $isTurkish ? 'E-posta' : 'Email' }}</span>
                        <input type="email" wire:model="email" class="w-full rounded-xl border border-line bg-soft px-3.5 py-2.5 text-[13px] text-ink outline-none focus:border-accent" />
                        @error('email') <span class="mt-1 block text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $isTurkish ? 'Konu' : 'Subject' }}</span>
                    <input type="text" wire:model="subject" class="w-full rounded-xl border border-line bg-soft px-3.5 py-2.5 text-[13px] text-ink outline-none focus:border-accent" />
                    @error('subject') <span class="mt-1 block text-[11px] text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $isTurkish ? 'Mesajınız' : 'Your Message' }}</span>
                    <textarea wire:model="message" rows="7" class="w-full resize-none rounded-xl border border-line bg-soft px-3.5 py-2.5 text-[13px] text-ink outline-none focus:border-accent"></textarea>
                    @error('message') <span class="mt-1 block text-[11px] text-red-500">{{ $message }}</span> @enderror
                </label>

                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" wire:model="website" tabindex="-1" autocomplete="off" /></label>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-[13px] font-black text-white transition hover:bg-accentDark disabled:opacity-60">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    <span wire:loading.remove wire:target="submit">{{ $isTurkish ? 'Mesajı Gönder' : 'Send Message' }}</span>
                    <span wire:loading wire:target="submit">{{ $isTurkish ? 'Gönderiliyor...' : 'Sending...' }}</span>
                </button>
            </form>
        </div>
        </div>


    </section>
</div>
