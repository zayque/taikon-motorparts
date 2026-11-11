<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Order;
use RuntimeException;

/**
 * Turns the current cart into an order inside a single transaction.
 * Stock rows are locked (SELECT … FOR UPDATE) so two customers cannot
 * buy the last unit at the same time.
 */
final class OrderPlacer
{
    /**
     * @param array<string, mixed> $customer validated checkout fields
     * @return array<string, mixed> the created order row
     * @throws RuntimeException with a customer-friendly message when stock ran out
     */
    public static function place(?int $userId, array $customer): array
    {
        $cart = Cart::raw();
        if ($cart === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $orderId = Database::transaction(static function () use ($userId, $customer, $cart): int {
            $ids = array_map('intval', array_keys($cart));
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $products = array_column(
                Database::all(
                    "SELECT id, name, sku, price, stock FROM products WHERE id IN ($placeholders) AND is_active = 1 FOR UPDATE",
                    $ids
                ),
                null,
                'id'
            );

            $lines = [];
            $subtotal = 0.0;

            foreach ($cart as $id => $qty) {
                $product = $products[$id] ?? null;
                if ($product === null || (int) $product['stock'] < $qty) {
                    $name = $product['name'] ?? 'An item';
                    throw new RuntimeException("$name no longer has enough stock. Please review your cart.");
                }

                $lineTotal = round((float) $product['price'] * $qty, 2);
                $subtotal += $lineTotal;
                $lines[] = [$product, $qty, $lineTotal];
            }

            $subtotal = round($subtotal, 2);
            $shipping = Cart::shippingFor($subtotal, $customer['fulfilment']);
            $isPickup = $customer['fulfilment'] === 'pickup';

            $orderId = Database::insert('orders', [
                'order_number'   => Order::generateNumber(),
                'user_id'        => $userId,
                'customer_name'  => $customer['name'],
                'email'          => strtolower($customer['email']),
                'phone'          => $customer['phone'],
                'fulfilment'     => $customer['fulfilment'],
                'address_line'   => $isPickup ? null : $customer['address_line'],
                'city'           => $isPickup ? null : $customer['city'],
                'postcode'       => $isPickup ? null : $customer['postcode'],
                'state'          => $isPickup ? null : $customer['state'],
                'payment_method' => $customer['payment_method'],
                'subtotal'       => $subtotal,
                'shipping_fee'   => $shipping,
                'total'          => $subtotal + $shipping,
                'notes'          => $customer['notes'] ?: null,
            ]);

            foreach ($lines as [$product, $qty, $lineTotal]) {
                Database::insert('order_items', [
                    'order_id'     => $orderId,
                    'product_id'   => (int) $product['id'],
                    'product_name' => $product['name'],
                    'sku'          => $product['sku'],
                    'unit_price'   => $product['price'],
                    'quantity'     => $qty,
                    'line_total'   => $lineTotal,
                ]);

                Database::query('UPDATE products SET stock = stock - ? WHERE id = ?', [$qty, (int) $product['id']]);
            }

            return $orderId;
        });

        Cart::clear();

        return Order::find($orderId) ?? throw new RuntimeException('Order could not be loaded.');
    }
}
