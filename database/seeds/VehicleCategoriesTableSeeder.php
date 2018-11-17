<?php

use App\Constants\VehicleCategory as CategoryConstants;
use App\VehicleCategory;
use Illuminate\Database\Seeder;

class VehicleCategoriesTableSeeder extends Seeder
{
    /**
     * Seed the vehicle categories
     *
     * @return void
     */
    public function run()
    {
        $vehicleCategories = [
            [
                'slug'           => CategoryConstants::A,
                'name'           => 'Category A',
                'dayTariff'      => 3,
                'nightTariff'    => 2,
                'spacesRequired' => 1,
            ],
            [
                'slug'           => CategoryConstants::B,
                'name'           => 'Category B',
                'dayTariff'      => 6,
                'nightTariff'    => 4,
                'spacesRequired' => 2,
            ],
            [
                'slug'           => CategoryConstants::C,
                'name'           => 'Category C',
                'dayTariff'      => 12,
                'nightTariff'    => 8,
                'spacesRequired' => 4,
            ],
        ];

        foreach ($vehicleCategories as $vehicleCategory) {
            $category = new VehicleCategory();
            $category->slug = $vehicleCategory['slug'];
            $category->name = $vehicleCategory['name'];
            $category->day_tariff = $vehicleCategory['dayTariff'];
            $category->night_tariff = $vehicleCategory['nightTariff'];
            $category->spaces_required = $vehicleCategory['spacesRequired'];

            $category->save();
        }
    }
}
