<?php

namespace App\Services;

class CateringPackageCatalog
{
    public const PACKED_MEAL_PRICE = 250;

    public const PACKED_MEAL_SIDES = ['Bam-i', 'Vegetables'];

    public const PACKED_MEAL_FIXED_INCLUSIONS = [
        'rice' => 'Rice',
        'dessert' => 'Dessert/Fruits',
        'drinks' => 'Drinks',
    ];

    public const PACKED_MEALS = [
        'A' => 'Crispy Fried Chicken',
        'B' => '2 pc Burger Steak w/ Gravy',
        'C' => 'Pork Sisig',
        'D' => 'Sweet & Sour Fish Fillet',
        'E' => 'Creamy Pork Steak',
        'F' => 'Pork Bistek Tagalog',
        'G' => 'Chicken Bicol Express',
    ];

    public const PARTY_TRAYS = [
        'Pork Dishes' => [
            'full_price' => 799,
            'half_price' => 459,
            'dishes' => [
                'Pork Salpicao', 'Pork Sisig', 'Hoisin Pork', 'Pepper Steak', 'Pork Tapa',
                'Breaded Pork Chops', 'Ginamay', 'Pork Humba', 'Sweet Chili Pork', 'Pork BBQ',
                'Braised Pork w/ Hoisin Sauce', 'Bicol Express', 'Kare-Kare', 'Creamy Pork Steak',
                'Sweet & Sour Pork', 'Afritada', 'Hawaiian Pork', 'Spicy Spareribs',
                'Pork Steak w/ Anato Oil', 'Pork Bistek Tagalog',
            ],
        ],
        'Chicken Dishes' => [
            'full_price' => 699,
            'half_price' => 459,
            'dishes' => [
                'Asado', 'Bistek', 'Estopado', 'Creamy Chicken', 'Chicken Curry', 'Chicken Guisado',
                'Fried Chicken', 'Crispy Chicken', 'Batter Fried Chicken', 'Sweet & Sour Chicken',
                'Garlic Chicken', 'Roasted Chicken', 'Menudo', 'Adobo', 'Buffalo Chicken',
                'Chicken Cordon Bleu', 'Thai Chili Chicken', 'Korean Chicken',
            ],
        ],
        'Beef Dishes' => [
            'full_price' => 999,
            'half_price' => 549,
            'dishes' => [
                'Beef Tapa', 'Sweet & Sour Beef Balls', 'Kare-Kare', 'Beef Curry', 'Beef Caldereta',
                'Beef with Onion', 'Mechado', 'Beef Steak', 'Beef with Mushrooms', 'Teriyaki',
            ],
        ],
        'Seafood Dishes' => [
            'full_price' => 1299,
            'half_price' => 759,
            'dishes' => [
                'Salted Bangus', 'Shrimp with Broccoli', 'Buttered Garlic Shrimp',
                'Fish Fillet w/ Sweet & Sour Sauce', 'Fish Kinilaw', 'Battered Squid Rings',
                'Stir Fried Squid w/ Oyster Sauce',
            ],
        ],
        'Pasta / Noodles' => [
            'full_price' => 599,
            'half_price' => 359,
            'dishes' => ['Bam-i', 'Bihon Guisado', 'Sotanghon Guisado', 'Pancit Guisado', 'Spaghetti'],
        ],
        'Vegetable Dishes' => [
            'full_price' => 699,
            'half_price' => 499,
            'dishes' => ['Chopsuey', 'Pakbet', 'Mixed Veggies', 'Stir Fried Vegetables w/ Oyster Sauce'],
        ],
        'Desserts' => [
            'full_price' => 699,
            'half_price' => 499,
            'dishes' => ['Buko Pandan', 'Fruit Salad', 'Macaroni Salad', 'Maja Blanca'],
        ],
    ];

    public static function partyTrayDishes(): array
    {
        return array_merge(...array_values(array_map(
            static fn (array $category): array => $category['dishes'],
            self::PARTY_TRAYS,
        )));
    }

    public static function validPartyTrayItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $validItems = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $category = $item['category'] ?? null;
            $dish = $item['dish'] ?? null;
            $size = $item['size'] ?? null;
            $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
            if (
                is_string($category)
                && isset(self::PARTY_TRAYS[$category])
                && is_string($dish)
                && in_array($dish, self::PARTY_TRAYS[$category]['dishes'], true)
                && in_array($size, ['full', 'half'], true)
                && $quantity !== false
                && $quantity > 0
                && $quantity <= 5000
            ) {
                $validItems[] = [
                    'category' => $category,
                    'dish' => $dish,
                    'size' => $size,
                    'quantity' => $quantity,
                ];
            }
        }

        return $validItems;
    }

    public const PACKAGES = [
        'option_550' => [
            'label' => 'Option 1',
            'price' => 550,
            'meat_count' => 2,
        ],
        'option_650' => [
            'label' => 'Option 2',
            'price' => 650,
            'meat_count' => 3,
        ],
    ];

    public const MENU = [
        'soup' => [
            'label' => 'Soup',
            'items' => ['Pochero Soup', 'Egg Drop Soup', 'Sotanghon Soup', 'Egg Tomato Soup'],
        ],
        'beef' => [
            'label' => 'Beef',
            'items' => [
                'Beef Tapa', 'Sweet & Sour Beef Balls', 'Kare-Kare', 'Beef Curry', 'Beef Caldereta',
                'Beef with Onion', 'Mechado', 'Beef Steak', 'Beef with Mushrooms', 'Teriyaki',
            ],
        ],
        'pork' => [
            'label' => 'Pork',
            'items' => [
                'Pork Salpicao', 'Pork Sisig', 'Hoisin Pork', 'Pepper Steak', 'Pork Tapa',
                'Breaded Pork Chops', 'Ginamay', 'Pork Humba', 'Sweet Chili Pork', 'Pork BBQ',
                'Braised Pork w/ Hoisin Sauce', 'Bicol Express', 'Kare-Kare', 'Creamy Pork Steak',
                'Sweet & Sour Pork', 'Afritada', 'Hawaiian Pork', 'Spicy Spareribs',
                'Pork Steak w/ Anato Oil', 'Pork Bistek Tagalog',
            ],
        ],
        'chicken' => [
            'label' => 'Chicken',
            'items' => [
                'Asado', 'Bistek', 'Estopado', 'Creamy Chicken', 'Chicken Curry', 'Chicken Guisado',
                'Fried Chicken', 'Crispy Chicken', 'Batter Fried Chicken', 'Sweet & Sour Chicken',
                'Garlic Chicken', 'Roasted Chicken', 'Menudo', 'Adobo', 'Buffalo Chicken',
                'Chicken Cordon Bleu', 'Thai Chili Chicken', 'Korean Chicken',
            ],
        ],
        'seafood' => [
            'label' => 'Seafood',
            'items' => [
                'Salted Bangus', 'Shrimp with Broccoli', 'Buttered Garlic Shrimp',
                'Fish Fillet w/ Sweet & Sour Sauce', 'Fish Kinilaw', 'Battered Squid Rings',
                'Stir Fried Squid w/ Oyster Sauce',
            ],
        ],
        'vegetables' => [
            'label' => 'Vegetables',
            'items' => [
                'Chopsuey', 'Pakbet', 'Mixed Veggies', 'Stir Fried Vegetables w/ Oyster Sauce',
            ],
        ],
        'pasta' => [
            'label' => 'Pasta/Noodles',
            'items' => ['Bam-i', 'Bihon Guisado', 'Sotanghon Guisado', 'Pancit Guisado', 'Spaghetti'],
        ],
        'dessert' => [
            'label' => 'Dessert',
            'items' => ['Buko Pandan', 'Fruit Salad', 'Macaroni Salad', 'Maja Blanca'],
        ],
        'rice' => [
            'label' => 'Rice',
            'items' => ['Plain Rice', 'Garlic Rice', 'Fried Rice', 'Java Rice'],
        ],
        'drinks' => [
            'label' => 'Drinks',
            'items' => ['Iced Tea', 'Lemonade', 'Cucumber Juice'],
        ],
    ];

    public static function meatItems(): array
    {
        return array_merge(
            self::MENU['pork']['items'],
            self::MENU['beef']['items'],
            self::MENU['chicken']['items'],
            self::MENU['seafood']['items'],
        );
    }

}
