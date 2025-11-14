<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competitor;

class CompetitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competitors = [
            [
                'name' => 'Test Competitor 1',
                'website' => 'https://www.test1.com',
                'shortname' => 'TEST1',
                'price_class_name' => 'Test Class 1',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 2',
                'website' => 'https://www.test2.com',
                'shortname' => 'TEST2',
                'price_class_name' => 'Test Class 2',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 3',
                'website' => 'https://www.test3.com',
                'shortname' => 'TEST3',
                'price_class_name' => 'Test Class 3',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 4',
                'website' => 'https://www.test4.com',
                'shortname' => 'TEST4',
                'price_class_name' => 'Test Class 4',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 5',
                'website' => 'https://www.test5.com',
                'shortname' => 'TEST5',
                'price_class_name' => 'Test Class 5',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 6',
                'website' => 'https://www.test6.com',
                'shortname' => 'TEST6',
                'price_class_name' => 'Test Class 6',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 7',
                'website' => 'https://www.test7.com',
                'shortname' => 'TEST7',
                'price_class_name' => 'Test Class 7',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 8',
                'website' => 'https://www.test8.com',
                'shortname' => 'TEST8',
                'price_class_name' => 'Test Class 8',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 9',
                'website' => 'https://www.test9.com',
                'shortname' => 'TEST9',
                'price_class_name' => 'Test Class 9',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 10',
                'website' => 'https://www.test10.com',
                'shortname' => 'TEST10',
                'price_class_name' => 'Test Class 10',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 11',
                'website' => 'https://www.test11.com',
                'shortname' => 'TEST11',
                'price_class_name' => 'Test Class 11',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 12',
                'website' => 'https://www.test12.com',
                'shortname' => 'TEST12',
                'price_class_name' => 'Test Class 12',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 13',
                'website' => 'https://www.test13.com',
                'shortname' => 'TEST13',
                'price_class_name' => 'Test Class 13',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 14',
                'website' => 'https://www.test14.com',
                'shortname' => 'TEST14',
                'price_class_name' => 'Test Class 14',
                'status' => 1
            ],
            [
                'name' => 'Test Competitor 15',
                'website' => 'https://www.test15.com',
                'shortname' => 'TEST15',
                'price_class_name' => 'Test Class 15',
                'status' => 1
            ]
        ];

        foreach ($competitors as $competitor) {
            Competitor::create($competitor);
        }
    }
} 