<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Product;

/**
 * Session cart storing only [productId => quantity].
 * Prices and stock always come fresh from the database.
 */
final class Cart
{
    private const KEY = 'cart';
    public const MAX_QTY_PER_LINE = 20;

    /** @return array<int, int> */
    public static function raw(): array
    {
        $cart = Session::get(self::KEY, []);

        return is_array($cart) ? $cart : [];
    }

    public static function count(): int
    {
        return array_sum(self::raw());
    }

    /** Add quantity; returns the quantity actually in the cart afterwards. */
    public static function add(array $product, int $qty = 1): int
    {
        $cart = self::raw();
        $id = (int) $product['id'];
        $limit = min(self::MAX_QTY_PER_LINE, (int) $product['stock']);
        $cart[$id] = min($limit, ($cart[$id] ?? 0) + max(1, $qty));
        Session::put(self::KEY, $cart);

        return $cart[$id];
    }

    public static function set(int $productId, int $qty): void
    {
        $cart = self::raw();

        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min(self::MAX_QTY_PER_LINE, $qty);
        }

        Session::put(self::KEY, $cart);
    }

    public static function remove(int $productId): void
    {
        self::set($productId, 0);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    /**
     * Resolve the cart against the database. Drops products that no longer
     * exist and clamps quantities to available stock.
     *
     * @return array{lines: list<array<string, mixed>>, subtotal: float, count: int, adjusted: bool}
     */
    public static function summary(): array
    {
        $raw = self::raw();
        $products = Product::findMany(array_keys($raw));
        $lines = [];
        $subtotal = 0.0;
        $adjusted = false;

        foreach ($raw as $id => $qty) {
            $product = $products[$id] ?? null;
            $available = $product ? min((int) $product['stock'], self::MAX_QTY_PER_LINE) : 0;

            if ($available <= 0) {
                unset($raw[$id]);
                $adjusted = true;
                continue;
            }

            if ($qty > $available) {
                $raw[$id] = $qty = $available;
                $adjusted = true;
            }

            $lineTotal = round((float) $product['price'] * $qty, 2);
            $subtotal += $lineTotal;
            $lines[] = ['product' => $product, 'quantity' => $qty, 'line_total' => $lineTotal];
        }

        if ($adjusted) {
            Session::put(self::KEY, $raw);
        }

        return ['lines' => $lines, 'subtotal' => round($subtotal, 2), 'count' => array_sum($raw), 'adjusted' => $adjusted];
    }

    public static function shippingFor(float $subtotal, string $fulfilment): float
    {
        if ($fulfilment === 'pickup' || $subtotal <= 0) {
            return 0.0;
        }

        return $subtotal >= (float) config('shipping.free_over') ? 0.0 : (float) config('shipping.flat_rate');
    }
}
