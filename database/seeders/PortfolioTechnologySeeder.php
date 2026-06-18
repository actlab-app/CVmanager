<?php

namespace Database\Seeders;

use App\Models\PortfolioTechnology;
use Illuminate\Database\Seeder;

class PortfolioTechnologySeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_values(array_keys(config('portfolio-technologies'))) as $sortOrder => $slug) {
            $technology = config('portfolio-technologies.'.$slug);

            PortfolioTechnology::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $technology['name'],
                    'category' => $technology['category'] ?? null,
                    'logo_path' => isset($technology['logo'])
                        ? 'images/technologies/'.$technology['logo']
                        : null,
                    'icon' => $technology['icon'] ?? null,
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ],
            );
        }
    }
}
