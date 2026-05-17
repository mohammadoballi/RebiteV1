<?php

namespace App\Support;

use App\Models\DonationItem;
use Illuminate\Support\Collection;

class DonationItemAggregator
{
    /**
     * Build denormalized donation fields from item rows (for donations table columns).
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{food_type?: string, quantity?: string, quantity_unit?: string}
     */
    public static function aggregateForDonation(array $items): array
    {
        if ($items === []) {
            return [];
        }

        $collection = collect($items);

        return [
            'food_type' => $collection->pluck('food_type')->filter()->implode(', '),
            'quantity' => $collection
                ->map(fn (array $item) => self::formatLine($item))
                ->implode(', '),
            'quantity_unit' => $collection->count() > 1 ? 'mixed' : ($collection->first()['quantity_unit'] ?? 'kg'),
        ];
    }

    /**
     * @param  Collection<int, DonationItem>|array<int, DonationItem>  $items
     */
    public static function summarizeQuantities(Collection|array $items): string
    {
        $collection = $items instanceof Collection ? $items : collect($items);

        if ($collection->isEmpty()) {
            return '';
        }

        return $collection
            ->map(fn (DonationItem|array $item) => self::formatLine(
                $item instanceof DonationItem ? $item->only(['food_type', 'quantity', 'quantity_unit']) : $item
            ))
            ->implode(', ');
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public static function formatLine(array $item): string
    {
        $foodType = trim((string) ($item['food_type'] ?? ''));
        $quantity = trim((string) ($item['quantity'] ?? ''));
        $unit = trim((string) ($item['quantity_unit'] ?? 'kg'));

        $amount = trim($quantity . ' ' . $unit);

        if ($foodType !== '' && $amount !== '') {
            return $foodType . ': ' . $amount;
        }

        return $foodType !== '' ? $foodType : $amount;
    }
}
