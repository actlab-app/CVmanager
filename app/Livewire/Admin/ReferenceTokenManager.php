<?php

namespace App\Livewire\Admin;

use App\Models\ReferenceToken;
use App\Support\ReferenceUrl;
use Flux\Flux;
use Illuminate\Contracts\View\View;
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

    public ?int $referenceTokenId = null;

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

        Flux::toast('Referans tokeni silindi.', variant: 'success');
    }

    public function render(): View
    {
        $tokens = ReferenceToken::query()
            ->latest('last_visited_at')
            ->latest()
            ->withCount(['visits as visit_records_count'])
            ->get();

        return view('livewire.admin.reference-token-manager', [
            'tokens' => $tokens,
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
}
