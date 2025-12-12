<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SysAddressProvincesSeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            'Abra',
            'Agusan del Norte',
            'Agusan del Sur',
            'Aklan',
            'Albay',
            'Antique',
            'Apayao',
            'Aurora',
            'Basilan',
            'Bataan',
            'Batanes',
            'Batangas',
            'Benguet',
            'Biliran',
            'Bohol',
            'Bukidnon',
            'Bulacan',
            'Cagayan',
            'Camarines Norte',
            'Camarines Sur',
            'Camiguin',
            'Capiz',
            'Catanduanes',
            'Cavite',
            'Cebu',
            'Compostela Valley',
            'Cotabato',
            'Davao del Norte',
            'Davao del Sur',
            'Davao Oriental',
            'Dinagat Islands',
            'Eastern Samar',
            'Guimaras',
            'Ifugao',
            'Ilocos Norte',
            'Ilocos Sur',
            'Iloilo',
            'Isabela',
            'Kalinga',
            'La Union',
            'Laguna',
            'Lanao del Norte',
            'Lanao del Sur',
            'Leyte',
            'Maguindanao',
            'Marinduque',
            'Masbate',
            'Misamis Occidental',
            'Misamis Oriental',
            'Mountain Province',
            'Negros Occidental',
            'Negros Oriental',
            'Northern Samar',
            'Nueva Ecija',
            'Nueva Vizcaya',
            'Occidental Mindoro',
            'Oriental Mindoro',
            'Palawan',
            'Pampanga',
            'Pangasinan',
            'Quezon',
            'Quirino',
            'Rizal',
            'Romblon',
            'Samar',
            'Sarangani',
            'Siquijor',
            'Sorsogon',
            'South Cotabato',
            'Southern Leyte',
            'Sultan Kudarat',
            'Sulu',
            'Surigao del Norte',
            'Surigao del Sur',
            'Tarlac',
            'Tawi-Tawi',
            'Zambales',
            'Zamboanga del Norte',
            'Zamboanga del Sur',
            'Zamboanga Sibugay',
            'Metro Manila',
            'Other',
        ];

        foreach ($provinces as $province) {
            DB::table('sys_address_provinces')->insert([
                'name' => $province,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
