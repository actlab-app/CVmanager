<?php

namespace App\Livewire\Admin;

use App\Models\ReferenceToken;
use App\Models\ReferenceVisit;
use App\Support\ReferenceUrl;
use Carbon\CarbonImmutable;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Referans Tokenleri')]
class ReferenceTokenManager extends Component
{
    use WithFileUploads;

    private const CHART_COLORS = [
        '#10b981',
        '#3b82f6',
        '#f59e0b',
        '#ef4444',
        '#8b5cf6',
        '#06b6d4',
        '#ec4899',
        '#84cc16',
        '#f97316',
        '#6366f1',
        '#71717a',
    ];

    public ?int $referenceTokenId = null;

    public ?int $detailReferenceTokenId = null;

    public bool $showDetailModal = false;

    public ?string $detailDateFrom = null;

    public ?string $detailDateTo = null;

    public string $detailTab = 'analytics';

    public string $name = '';

    public string $token = '';

    public string $description = '';

    public bool $is_active = true;

    public mixed $image = null;

    public ?string $existingImagePath = null;

    public bool $removeExistingImage = false;

    public function mount(): void
    {
        $this->generateToken();
    }

    public function create(): void
    {
        $this->resetForm();
    }

    public function edit(int $referenceTokenId): void
    {
        $referenceToken = ReferenceToken::query()->findOrFail($referenceTokenId);

        $this->referenceTokenId = $referenceToken->id;
        $this->name = $referenceToken->name;
        $this->token = $referenceToken->token;
        $this->description = $referenceToken->description ?? '';
        $this->is_active = (bool) $referenceToken->is_active;
        $this->existingImagePath = $referenceToken->image;
        $this->removeExistingImage = false;
        $this->image = null;
        $this->resetValidation();
    }

    public function generateToken(): void
    {
        do {
            $token = Str::upper(Str::random(12));
        } while (ReferenceToken::query()->where('token', $token)->exists());

        $this->token = $token;
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->removeExistingImage = true;
    }

    public function showDetail(int $referenceTokenId): void
    {
        ReferenceToken::query()->findOrFail($referenceTokenId);

        $this->detailReferenceTokenId = $referenceTokenId;
        $this->detailTab = 'analytics';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailReferenceTokenId = null;
    }

    public function resetDetailDateRange(): void
    {
        $this->reset(['detailDateFrom', 'detailDateTo']);
    }

    public function selectDetailTab(string $tab): void
    {
        if (! in_array($tab, ['analytics', 'cleanup'], true)) {
            return;
        }

        $this->detailTab = $tab;
    }

    public function deleteVisitorVisits(string $visitorKey): void
    {
        if (! $this->detailReferenceTokenId) {
            return;
        }

        [$ipHash, $userAgentHash] = $this->decodeVisitorKey($visitorKey);

        $deletedCount = $this->visitorQuery(
            ReferenceVisit::query()->where('reference_token_id', $this->detailReferenceTokenId),
            $ipHash,
            $userAgentHash,
        )->delete();

        $this->refreshReferenceTokenVisitCounters($this->detailReferenceTokenId);

        Flux::toast(
            $deletedCount > 0 ? 'Ziyaretçinin kayıtları temizlendi.' : 'Temizlenecek ziyaret kaydı bulunamadı.',
            variant: 'success',
        );
    }

    public function save(): void
    {
        $this->token = ReferenceUrl::normalizeToken($this->token);
        $validated = $this->validate();

        $referenceToken = $this->referenceTokenId
            ? ReferenceToken::query()->findOrFail($this->referenceTokenId)
            : new ReferenceToken;

        $oldImagePath = $referenceToken->image;
        $newImagePath = $this->removeExistingImage ? null : $oldImagePath;

        if ($this->image) {
            $directory = public_path('images/reference-tokens');
            File::ensureDirectoryExists($directory);

            $extension = strtolower($this->image->getClientOriginalExtension() ?: 'jpg');
            $filename = Str::uuid().'.'.$extension;
            File::copy($this->image->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);
            $newImagePath = 'images/reference-tokens/'.$filename;
        }

        $referenceToken->fill([
            'name' => $validated['name'],
            'token' => $validated['token'],
            'description' => $validated['description'] ?: null,
            'image' => $newImagePath,
            'is_active' => $validated['is_active'],
        ])->save();

        $this->deleteReplacedImage($oldImagePath, $newImagePath);

        Flux::toast($this->referenceTokenId ? 'Referans tokeni güncellendi.' : 'Referans tokeni oluşturuldu.', variant: 'success');

        $this->resetForm();
    }

    public function delete(int $referenceTokenId): void
    {
        $referenceToken = ReferenceToken::query()->findOrFail($referenceTokenId);
        $imagePath = $referenceToken->image;

        $referenceToken->delete();
        $this->deleteReplacedImage($imagePath, null);

        if ($this->referenceTokenId === $referenceTokenId) {
            $this->resetForm();
        }

        if ($this->detailReferenceTokenId === $referenceTokenId) {
            $this->closeDetail();
        }

        Flux::toast('Referans tokeni silindi.', variant: 'success');
    }

    public function render(): View
    {
        $tokens = ReferenceToken::query()
            ->latest('last_visited_at')
            ->latest()
            ->withCount(['visits as visit_records_count'])
            ->get();

        $detailToken = $this->detailReferenceTokenId
            ? ReferenceToken::query()->find($this->detailReferenceTokenId)
            : null;

        return view('livewire.admin.reference-token-manager', [
            'tokens' => $tokens,
            'detailToken' => $detailToken,
            'detailStats' => $this->detailStats($detailToken),
        ]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'token' => [
                'required',
                'alpha_num',
                'min:2',
                'max:32',
                Rule::unique('reference_tokens', 'token')->ignore($this->referenceTokenId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:4096'],
        ];
    }

    public function referenceLink(string $routeName, ?ReferenceToken $referenceToken = null): string
    {
        $token = $referenceToken?->token ?? $this->token;

        if (! $token) {
            return route($routeName);
        }

        return ReferenceUrl::appendToken(route($routeName), $token);
    }

    private function resetForm(): void
    {
        $this->reset([
            'referenceTokenId',
            'name',
            'description',
            'image',
            'existingImagePath',
            'removeExistingImage',
        ]);

        $this->is_active = true;
        $this->generateToken();
        $this->resetValidation();
    }

    private function deleteReplacedImage(?string $oldPath, ?string $newPath): void
    {
        if ($oldPath && $oldPath !== $newPath && str_starts_with($oldPath, 'images/reference-tokens/')) {
            File::delete(public_path($oldPath));
        }
    }

    private function detailStats(?ReferenceToken $referenceToken): array
    {
        if (! $referenceToken) {
            return $this->emptyDetailStats();
        }

        $baseQuery = $this->detailVisitQuery($referenceToken->id);

        return [
            'total_visits' => (clone $baseQuery)->count(),
            'ip_chart' => $this->hashDistribution($referenceToken->id, 'ip_hash', "IP'si", 'Bilinmeyen IP'),
            'user_agent_chart' => $this->hashDistribution($referenceToken->id, 'user_agent_hash', 'User Agent', 'Bilinmeyen User Agent'),
            'page_chart' => $this->pageDistribution($referenceToken->id),
            'visitor_cleanup' => $this->visitorCleanupRows($referenceToken->id),
        ];
    }

    private function emptyDetailStats(): array
    {
        return [
            'total_visits' => 0,
            'ip_chart' => $this->emptyPieChart(),
            'user_agent_chart' => $this->emptyPieChart(),
            'page_chart' => [
                'items' => [],
                'total' => 0,
            ],
            'visitor_cleanup' => [
                'items' => [],
                'total' => 0,
            ],
        ];
    }

    private function visitorCleanupRows(int $referenceTokenId): array
    {
        $currentIpHash = $this->hashValue(request()->ip());
        $currentUserAgentHash = $this->hashValue(request()->userAgent());
        $query = $this->applyDetailVisitDateRange(
            ReferenceVisit::query()->where('reference_token_id', $referenceTokenId),
        );

        $items = $query
            ->select('ip_hash', 'user_agent_hash')
            ->selectRaw('COUNT(*) as visits_count')
            ->selectRaw('MAX(visited_at) as last_visited_at')
            ->groupBy('ip_hash', 'user_agent_hash')
            ->orderByDesc('last_visited_at')
            ->get()
            ->map(fn (object $row): array => [
                'ip_hash' => $row->ip_hash,
                'user_agent_hash' => $row->user_agent_hash,
                'visitor_key' => $this->visitorKey($row->ip_hash, $row->user_agent_hash),
                'ip_hash_short' => $this->shortHash($row->ip_hash),
                'user_agent_hash_short' => $this->shortHash($row->user_agent_hash),
                'is_current_ip' => $row->ip_hash && hash_equals($row->ip_hash, (string) $currentIpHash),
                'is_current_user_agent' => $row->user_agent_hash && hash_equals($row->user_agent_hash, (string) $currentUserAgentHash),
                'visits_count' => (int) $row->visits_count,
                'last_visited_at' => $row->last_visited_at
                    ? CarbonImmutable::parse($row->last_visited_at)->format('d.m.Y H:i')
                    : '-',
            ])
            ->all();

        return [
            'items' => $items,
            'total' => count($items),
        ];
    }

    private function hashDistribution(int $referenceTokenId, string $column, string $labelSuffix, string $unknownLabel): array
    {
        $rows = $this->detailVisitQuery($referenceTokenId)
            ->select($column)
            ->selectRaw('COUNT(*) as visits_count')
            ->groupBy($column)
            ->orderByDesc('visits_count')
            ->get();

        return $this->pieChartData(
            $rows,
            fn (object $row, int $index): string => $row->{$column}
                ? $this->sequenceLabel($index).' '.$labelSuffix
                : $unknownLabel,
        );
    }

    private function pageDistribution(int $referenceTokenId): array
    {
        $rows = $this->detailVisitQuery($referenceTokenId)
            ->select('path')
            ->selectRaw('COUNT(*) as visits_count')
            ->groupBy('path')
            ->orderByDesc('visits_count')
            ->get();

        $total = (int) $rows->sum(fn (object $row): int => (int) $row->visits_count);
        $visibleRows = $rows->take(10);
        $otherCount = (int) $rows->slice(10)->sum(fn (object $row): int => (int) $row->visits_count);
        $items = $visibleRows
            ->map(fn (object $row): array => [
                'label' => $this->pageLabel((string) $row->path),
                'count' => (int) $row->visits_count,
            ])
            ->values();

        if ($otherCount > 0) {
            $items->push([
                'label' => 'Diğer',
                'count' => $otherCount,
            ]);
        }

        $maxCount = max(1, (int) $items->max('count'));

        return [
            'items' => $items
                ->map(fn (array $item): array => [
                    ...$item,
                    'percentage' => round(($item['count'] / $maxCount) * 100, 2),
                ])
                ->all(),
            'total' => $total,
        ];
    }

    private function pieChartData(Collection $rows, callable $labelResolver): array
    {
        $total = (int) $rows->sum(fn (object $row): int => (int) $row->visits_count);

        if ($total === 0) {
            return $this->emptyPieChart();
        }

        $visibleRows = $rows->take(10);
        $otherCount = (int) $rows->slice(10)->sum(fn (object $row): int => (int) $row->visits_count);
        $items = collect();

        foreach ($visibleRows as $index => $row) {
            $items->push([
                'label' => $labelResolver($row, $index),
                'count' => (int) $row->visits_count,
            ]);
        }

        if ($otherCount > 0) {
            $items->push([
                'label' => 'Diğer',
                'count' => $otherCount,
            ]);
        }

        return $this->withPieStyles($items->values(), $total, $rows->count());
    }

    private function withPieStyles(Collection $items, int $total, int $groupsCount): array
    {
        $start = 0.0;
        $lastIndex = $items->count() - 1;
        $gradientParts = [];

        $styledItems = $items
            ->map(function (array $item, int $index) use ($total, $lastIndex, &$start, &$gradientParts): array {
                $color = self::CHART_COLORS[$index % count(self::CHART_COLORS)];
                $end = $index === $lastIndex ? 360.0 : $start + (($item['count'] / $total) * 360);
                $gradientParts[] = sprintf('%s %.2fdeg %.2fdeg', $color, $start, $end);
                $start = $end;

                return [
                    ...$item,
                    'color' => $color,
                    'percentage' => round(($item['count'] / $total) * 100, 1),
                ];
            })
            ->all();

        return [
            'items' => $styledItems,
            'total' => $total,
            'groups_count' => $groupsCount,
            'gradient' => 'conic-gradient('.implode(', ', $gradientParts).')',
        ];
    }

    private function emptyPieChart(): array
    {
        return [
            'items' => [],
            'total' => 0,
            'groups_count' => 0,
            'gradient' => 'conic-gradient(#e5e7eb 0deg 360deg)',
        ];
    }

    private function detailVisitQuery(int $referenceTokenId): Builder
    {
        return $this->applyDetailVisitDateRange(
            ReferenceVisit::query()->where('reference_token_id', $referenceTokenId),
        );
    }

    private function visitorQuery(Builder $query, ?string $ipHash, ?string $userAgentHash): Builder
    {
        return $this->whereNullableHash(
            $this->whereNullableHash($query, 'ip_hash', $ipHash),
            'user_agent_hash',
            $userAgentHash,
        );
    }

    private function whereNullableHash(Builder $query, string $column, ?string $hash): Builder
    {
        return $hash === null
            ? $query->whereNull($column)
            : $query->where($column, $hash);
    }

    private function visitorKey(?string $ipHash, ?string $userAgentHash): string
    {
        $payload = json_encode([$ipHash, $userAgentHash], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    private function decodeVisitorKey(string $visitorKey): array
    {
        $encoded = strtr($visitorKey, '-_', '+/');
        $encoded .= str_repeat('=', (4 - strlen($encoded) % 4) % 4);
        $payload = base64_decode($encoded, true);

        if ($payload === false) {
            return [null, null];
        }

        $decoded = json_decode($payload, true);

        if (! is_array($decoded) || count($decoded) !== 2) {
            return [null, null];
        }

        return [
            is_string($decoded[0] ?? null) ? $decoded[0] : null,
            is_string($decoded[1] ?? null) ? $decoded[1] : null,
        ];
    }

    private function refreshReferenceTokenVisitCounters(int $referenceTokenId): void
    {
        $referenceToken = ReferenceToken::query()->find($referenceTokenId);

        if (! $referenceToken) {
            return;
        }

        $visits = ReferenceVisit::query()
            ->where('reference_token_id', $referenceTokenId);

        $referenceToken->forceFill([
            'visits_count' => (clone $visits)->count(),
            'last_visited_at' => (clone $visits)->max('visited_at'),
        ])->save();
    }

    private function applyDetailVisitDateRange(Builder $query): Builder
    {
        [$dateFrom, $dateTo] = $this->detailDateRange();

        return $query
            ->when($dateFrom, fn (Builder $query) => $query->where('visited_at', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $query) => $query->where('visited_at', '<=', $dateTo));
    }

    private function detailDateRange(): array
    {
        $dateFrom = $this->parseDate($this->detailDateFrom)?->startOfDay();
        $dateTo = $this->parseDate($this->detailDateTo)?->endOfDay();

        if ($dateFrom && $dateTo && $dateFrom->greaterThan($dateTo)) {
            return [$dateTo->startOfDay(), $dateFrom->endOfDay()];
        }

        return [$dateFrom, $dateTo];
    }

    private function parseDate(?string $date): ?CarbonImmutable
    {
        if (! $date) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $date);
        } catch (\Throwable) {
            return null;
        }
    }

    private function sequenceLabel(int $index): string
    {
        $letters = range('A', 'Z');

        if ($index < count($letters)) {
            return $letters[$index];
        }

        return $letters[intdiv($index, count($letters)) - 1].$letters[$index % count($letters)];
    }

    private function shortHash(?string $hash): string
    {
        if (! $hash) {
            return 'Yok';
        }

        if (strlen($hash) <= 18) {
            return $hash;
        }

        return substr($hash, 0, 10).'...'.substr($hash, -6);
    }

    private function hashValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return hash_hmac('sha256', $value, (string) config('app.key'));
    }

    private function pageLabel(string $path): string
    {
        return match ($path) {
            'about' => 'Hakkımda',
            'cv' => 'CV',
            'contact' => 'İletişim',
            'portfolio' => 'Portfolyo',
            default => '/'.ltrim($path, '/'),
        };
    }
}
