<?php

namespace Database\Seeders;

use App\Models\NigerianState;
use Illuminate\Database\Seeder;

class NigerianStateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['FCT Abuja', 'FC', 'North-Central'],
            ['Lagos', 'LA', 'South-West'],
            ['Abia', 'AB', 'South-East'],
            ['Adamawa', 'AD', 'North-East'],
            ['Akwa Ibom', 'AK', 'South-South'],
            ['Anambra', 'AN', 'South-East'],
            ['Bauchi', 'BA', 'North-East'],
            ['Bayelsa', 'BY', 'South-South'],
            ['Benue', 'BE', 'North-Central'],
            ['Borno', 'BO', 'North-East'],
            ['Cross River', 'CR', 'South-South'],
            ['Delta', 'DE', 'South-South'],
            ['Ebonyi', 'EB', 'South-East'],
            ['Edo', 'ED', 'South-South'],
            ['Ekiti', 'EK', 'South-West'],
            ['Enugu', 'EN', 'South-East'],
            ['Gombe', 'GO', 'North-East'],
            ['Imo', 'IM', 'South-East'],
            ['Jigawa', 'JI', 'North-West'],
            ['Kaduna', 'KD', 'North-West'],
            ['Kano', 'KN', 'North-West'],
            ['Katsina', 'KT', 'North-West'],
            ['Kebbi', 'KB', 'North-West'],
            ['Kogi', 'KO', 'North-Central'],
            ['Kwara', 'KW', 'North-Central'],
            ['Nasarawa', 'NA', 'North-Central'],
            ['Niger', 'NI', 'North-Central'],
            ['Ogun', 'OG', 'South-West'],
            ['Ondo', 'ON', 'South-West'],
            ['Osun', 'OS', 'South-West'],
            ['Oyo', 'OY', 'South-West'],
            ['Plateau', 'PL', 'North-Central'],
            ['Rivers', 'RI', 'South-South'],
            ['Sokoto', 'SO', 'North-West'],
            ['Taraba', 'TA', 'North-East'],
            ['Yobe', 'YO', 'North-East'],
            ['Zamfara', 'ZA', 'North-West'],
        ];

        foreach ($states as $i => [$name, $code, $region]) {
            NigerianState::updateOrCreate(
                ['name' => $name],
                ['code' => $code, 'region' => $region, 'sort_order' => $i + 1, 'is_active' => true]
            );
        }
    }
}
