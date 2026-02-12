<?php

namespace Core;

use Models\Product;

class Cart
{
    private const SESSION_KEY = 'cart';
    private static ?Cart $instance = null;

    private array $items = [];

    private function __construct()
    {
        Session::start();
        $this->load();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load(): void
    {
        $this->items = $_SESSION[self::SESSION_KEY] ?? [];
    }

    private function save(): void
    {
        $_SESSION[self::SESSION_KEY] = $this->items;
    }

    public function add(int $productId, int $quantity = 1): bool
    {
        $product = Product::findWithTranslation($productId);

        if (!$product || !$product->is_active) {
            return false;
        }

        if ($product->track_inventory && $product->stock_quantity < $quantity) {
            return false;
        }

        if (isset($this->items[$productId])) {
            $newQty = $this->items[$productId]['quantity'] + $quantity;

            if ($product->track_inventory && $product->stock_quantity < $newQty) {
                $newQty = $product->stock_quantity;
            }

            $this->items[$productId]['quantity'] = $newQty;
        } else {
            $this->items[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'added_at' => time()
            ];
        }

        $this->save();
        return true;
    }

    public function update(int $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        if (!isset($this->items[$productId])) {
            return false;
        }

        $product = Product::findWithTranslation($productId);

        if (!$product || !$product->is_active) {
            $this->remove($productId);
            return false;
        }

        if ($product->track_inventory && $product->stock_quantity < $quantity) {
            $quantity = $product->stock_quantity;
        }

        $this->items[$productId]['quantity'] = $quantity;
        $this->save();

        return true;
    }

    public function remove(int $productId): bool
    {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
            $this->save();
            return true;
        }
        return false;
    }

    public function clear(): void
    {
        $this->items = [];
        $this->save();
    }

    public function getItems(): array
    {
        $items = [];

        foreach ($this->items as $productId => $item) {
            $product = Product::findWithTranslation($productId);

            if (!$product || !$product->is_active) {

                unset($this->items[$productId]);
                continue;
            }

            if ($product->track_inventory && $product->stock_quantity < $item['quantity']) {
                $item['quantity'] = $product->stock_quantity;
                $this->items[$productId]['quantity'] = $item['quantity'];

                if ($item['quantity'] <= 0) {
                    unset($this->items[$productId]);
                    continue;
                }
            }

            $items[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $product->getCurrentPrice() * $item['quantity']
            ];
        }

        $this->save();

        return $items;
    }

    public function getRawItems(): array
    {
        return $this->items;
    }

    public function getItemCount(): int
    {
        return count($this->items);
    }

    public function getTotalQuantity(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    public function getSubtotal(): float
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $subtotal += $item['subtotal'];
        }

        return $subtotal;
    }

    public function getShippingCost(): float
    {

        $freeShippingThreshold = (float)setting('free_shipping_threshold', 50);
        $shippingCost = (float)setting('shipping_cost', 5);

        if ($this->getSubtotal() >= $freeShippingThreshold) {
            return 0;
        }

        return $shippingCost;
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getShippingCost();
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function hasProduct(int $productId): bool
    {
        return isset($this->items[$productId]);
    }

    public function getQuantity(int $productId): int
    {
        return $this->items[$productId]['quantity'] ?? 0;
    }

    public function getTotalWeight(): float
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            if ($item['product']->weight) {
                $weight += $item['product']->weight * $item['quantity'];
            }
        }

        return $weight;
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->isEmpty()) {
            $errors[] = 'O carrinho está vazio.';
            return $errors;
        }

        foreach ($this->getItems() as $item) {
            $product = $item['product'];

            if (!$product->is_active) {
                $errors[] = sprintf('O produto "%s" já não está disponível.', $product->name);
            }

            if ($product->track_inventory && $product->stock_quantity < $item['quantity']) {
                if ($product->stock_quantity <= 0) {
                    $errors[] = sprintf('O produto "%s" está esgotado.', $product->name);
                } else {
                    $errors[] = sprintf(
                        'O produto "%s" só tem %d unidade(s) em stock.',
                        $product->name,
                        $product->stock_quantity
                    );
                }
            }
        }

        return $errors;
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            $product = $item['product'];
            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->getCurrentPrice(),
                'original_price' => $product->price,
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'image' => $product->getPrimaryImage(),
                'in_stock' => $product->isInStock()
            ];
        }

        return [
            'items' => $items,
            'item_count' => $this->getItemCount(),
            'total_quantity' => $this->getTotalQuantity(),
            'subtotal' => $this->getSubtotal(),
            'shipping' => $this->getShippingCost(),
            'total' => $this->getTotal()
        ];
    }
}
