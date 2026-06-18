<?php

namespace App\Livewire\Admin;

use App\Models\ContactItem;
use App\Models\ContactMessage;
use App\Models\ContactSetting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('İletişim Yönetimi')]
class ContactManager extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    private const TRANSLATABLE_FIELDS = [
        'title',
        'intro',
        'form_title',
        'privacy_notice',
        'success_message',
        'location',
    ];

    public string $activeLang = 'tr';

    public array $translations = [
        'tr' => [
            'title' => '',
            'intro' => '',
            'form_title' => '',
            'privacy_notice' => '',
            'success_message' => '',
            'location' => '',
        ],
        'en' => [
            'title' => '',
            'intro' => '',
            'form_title' => '',
            'privacy_notice' => '',
            'success_message' => '',
            'location' => '',
        ],
    ];

    public string $map_url = '';

    public bool $privacy_hidden = false;

    public array $items = [];

    public array $itemOrder = [];

    public array $removedItemIds = [];

    public function mount(): void
    {
        $settings = ContactSetting::first();

        if ($settings) {
            $this->map_url = $settings->map_url ?? '';
            $this->privacy_hidden = (bool) $settings->privacy_hidden;

            foreach (self::LANGUAGES as $language) {
                foreach (self::TRANSLATABLE_FIELDS as $field) {
                    $this->translations[$language][$field] = $settings->getTranslation($field, $language, false) ?? '';
                }
            }
        }

        ContactItem::query()
            ->ordered()
            ->get()
            ->each(function (ContactItem $item): void {
                $key = (string) Str::uuid();

                $this->items[$key] = [
                    'id' => $item->id,
                    'label' => [
                        'tr' => $item->getTranslation('label', 'tr', false) ?? '',
                        'en' => $item->getTranslation('label', 'en', false) ?? '',
                    ],
                    'value' => $item->value,
                    'url' => $item->url ?? '',
                    'icon' => $item->icon,
                    'is_private' => $item->is_private,
                    'is_active' => $item->is_active,
                    'show_in_cv' => $item->show_in_cv,
                ];
                $this->itemOrder[] = $key;
            });
    }

    public function switchLang(string $language): void
    {
        if (in_array($language, self::LANGUAGES, true)) {
            $this->activeLang = $language;
        }
    }

    public function addItem(): void
    {
        $key = (string) Str::uuid();

        $this->items[$key] = [
            'id' => null,
            'label' => ['tr' => '', 'en' => ''],
            'value' => '',
            'url' => '',
            'icon' => 'link',
            'is_private' => false,
            'is_active' => true,
            'show_in_cv' => false,
        ];
        $this->itemOrder[] = $key;
    }

    public function removeItem(string $key): void
    {
        if (! isset($this->items[$key])) {
            return;
        }

        if ($this->items[$key]['id']) {
            $this->removedItemIds[] = $this->items[$key]['id'];
        }

        unset($this->items[$key]);
        $this->itemOrder = array_values(array_filter(
            $this->itemOrder,
            fn (string $itemKey): bool => $itemKey !== $key,
        ));
    }

    public function moveItem(string $key, int $direction): void
    {
        $index = array_search($key, $this->itemOrder, true);

        if ($index === false) {
            return;
        }

        $target = $index + $direction;

        if (! isset($this->itemOrder[$target])) {
            return;
        }

        [$this->itemOrder[$index], $this->itemOrder[$target]] = [$this->itemOrder[$target], $this->itemOrder[$index]];
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function (): void {
            $settings = ContactSetting::firstOrNew();
            $settings->map_url = $this->map_url ?: null;
            $settings->privacy_hidden = $this->privacy_hidden;

            foreach (self::LANGUAGES as $language) {
                foreach (self::TRANSLATABLE_FIELDS as $field) {
                    $settings->setTranslation($field, $language, $this->translations[$language][$field]);
                }
            }

            $settings->save();

            ContactItem::query()->whereIn('id', $this->removedItemIds)->delete();

            foreach ($this->itemOrder as $sortOrder => $key) {
                $itemData = $this->items[$key];
                $item = isset($itemData['id'])
                    ? ContactItem::find($itemData['id'])
                    : null;
                $item ??= new ContactItem;

                $item->fill([
                    'value' => $itemData['value'],
                    'url' => $itemData['url'] ?: null,
                    'icon' => $itemData['icon'] ?: 'link',
                    'is_private' => $itemData['is_private'],
                    'is_active' => $itemData['is_active'],
                    'show_in_cv' => $itemData['show_in_cv'],
                    'sort_order' => $sortOrder,
                ]);

                foreach (self::LANGUAGES as $language) {
                    $item->setTranslation('label', $language, $itemData['label'][$language]);
                }

                $item->save();
                $this->items[$key]['id'] = $item->id;
            }
        });

        $this->removedItemIds = [];
        Flux::toast('İletişim sayfası kaydedildi.', variant: 'success');
    }

    public function markAsRead(int $messageId): void
    {
        ContactMessage::query()->whereKey($messageId)->update(['read_at' => now()]);
    }

    public function deleteMessage(int $messageId): void
    {
        ContactMessage::query()->whereKey($messageId)->delete();
        Flux::toast('Mesaj silindi.', variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.contact-manager', [
            'messages' => ContactMessage::query()->latest()->limit(50)->get(),
            'unreadCount' => ContactMessage::query()->whereNull('read_at')->count(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'translations.tr.title' => ['required', 'string', 'max:180'],
            'translations.en.title' => ['required', 'string', 'max:180'],
            'translations.*.*' => ['nullable', 'string', 'max:2000'],
            'map_url' => [
                'nullable',
                'url:https',
                'max:2048',
                'starts_with:https://www.google.com/maps/embed,https://maps.google.com/maps',
            ],
            'privacy_hidden' => ['boolean'],
            'items' => ['array', 'max:30'],
            'items.*.label.tr' => ['required', 'string', 'max:100'],
            'items.*.label.en' => ['required', 'string', 'max:100'],
            'items.*.value' => ['required', 'string', 'max:255'],
            'items.*.url' => [
                'nullable',
                'string',
                'max:2048',
                'regex:/^(https?:\/\/|mailto:|tel:)/i',
            ],
            'items.*.icon' => ['required', 'string', Rule::in(array_keys(config('contact-icons')))],
            'items.*.is_private' => ['boolean'],
            'items.*.is_active' => ['boolean'],
            'items.*.show_in_cv' => ['boolean'],
        ];
    }
}
