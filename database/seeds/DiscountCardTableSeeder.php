<?php

use App\Constants\DiscountCard as DiscountCardConstants;
use App\DiscountCard;
use Illuminate\Database\Seeder;

class DiscountCardTableSeeder extends Seeder
{
    /**
     * Seed the discount cards
     *
     * @return void
     */
    public function run()
    {
        $cards = [
            [
                'slug'     => DiscountCardConstants::SILVER,
                'name'     => 'Silver',
                'discount' => 10,
            ],
            [
                'slug'     => DiscountCardConstants::GOLD,
                'name'     => 'Gold',
                'discount' => 15,
            ],
            [
                'slug'     => DiscountCardConstants::PLATINUM,
                'name'     => 'Platinum',
                'discount' => 20,
            ],
        ];

        foreach ($cards as $card) {
            $discountCard = new DiscountCard();
            $discountCard->slug = $card['slug'];
            $discountCard->name = $card['name'];
            $discountCard->discount = $card['discount'];

            $discountCard->save();
        }
    }
}
