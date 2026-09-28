<?php

namespace Database\Seeders;

use App\Features\Users\Data\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $baseLatitude = 29.6952;
        $baseLongitude = 31.2481;
        $users = [
            // Farmers (5)
            [
                'id' => 5,
                'name' => 'مزرعة النخيل',
                'user_type' => 'farmer',
                'phone' => '01000000001',
                'address' => 'الإسكندرية، مصر',
            ],
            [
                'id' => 6,
                'name' => 'مزرعة الخير',
                'user_type' => 'farmer',
                'phone' => '01000000002',
                'address' => 'الدقهلية، مصر',
            ],
            [
                'id' => 7,
                'name' => 'مزرعة البركة',
                'user_type' => 'farmer',
                'phone' => '01000000003',
                'address' => 'الشرقية، مصر',
            ],
            [
                'id' => 8,
                'name' => 'مزرعة الروابي',
                'user_type' => 'farmer',
                'phone' => '01000000004',
                'address' => 'البحيرة، مصر',
            ],
            [
                'id' => 9,
                'name' => 'مزرعة السلام',
                'user_type' => 'farmer',
                'phone' => '01000000005',
                'address' => 'المنوفية، مصر',
            ],
            // Shops (5)
            [
                'id' => 10,
                'name' => 'محل النماء',
                'user_type' => 'shop',
                'phone' => '01000000006',
                'address' => 'القاهرة، مصر',
            ],
            [
                'id' => 11,
                'name' => 'محل الأمل',
                'user_type' => 'shop',
                'phone' => '01000000007',
                'address' => 'الجيزة، مصر',
            ],
            [
                'id' => 12,
                'name' => 'محل النور',
                'user_type' => 'shop',
                'phone' => '01000000008',
                'address' => 'الإسكندرية، مصر',
            ],
            [
                'id' => 13,
                'name' => 'محل البركة',
                'user_type' => 'shop',
                'phone' => '01000000009',
                'address' => 'طنطا، مصر',
            ],
            [
                'id' => 14,
                'name' => 'محل التوحيد',
                'user_type' => 'shop',
                'phone' => '01000000010',
                'address' => 'المنصورة، مصر',
            ],

            // Companies (5)
            [
                'id' => 15,
                'name' => 'شركة الزراعة الحديثة',
                'user_type' => 'company',
                'phone' => '01000000011',
                'address' => 'القاهرة، القرية الذكية',
            ],
            [
                'id' => 16,
                'name' => 'شركة الوادي الأخضر',
                'user_type' => 'company',
                'phone' => '01000000012',
                'address' => 'التجمع الخامس، القاهرة',
            ],
            [
                'id' => 17,
                'name' => 'شركة التنمية الزراعية',
                'user_type' => 'company',
                'phone' => '01000000013',
                'address' => 'السادس من أكتوبر، الجيزة',
            ],
            [
                'id' => 18,
                'name' => 'شركة النيل للأسمدة',
                'user_type' => 'company',
                'phone' => '01000000014',
                'address' => 'المعادي، القاهرة',
            ],
            [
                'id' => 19,
                'name' => 'شركة المستقبل الزراعي',
                'user_type' => 'company',
                'phone' => '01000000015',
                'address' => 'مدينة نصر، القاهرة',
            ],

            // Sales Engineers (5)
            [
                'id' => 20,
                'name' => 'مهندس أحمد مبيعات',
                'user_type' => 'salesEngineer',
                'phone' => '01000000016',
                'address' => 'القاهرة، مصر',
            ],
            [
                'id' => 21,
                'name' => 'مهندس محمد مبيعات',
                'user_type' => 'salesEngineer',
                'phone' => '01000000017',
                'address' => 'الجيزة، مصر',
            ],
            [
                'id' => 22,
                'name' => 'مهندس محمود مبيعات',
                'user_type' => 'salesEngineer',
                'phone' => '01000000018',
                'address' => 'الإسكندرية، مصر',
            ],
            [
                'id' => 23,
                'name' => 'مهندس إبراهيم مبيعات',
                'user_type' => 'salesEngineer',
                'phone' => '01000000019',
                'address' => 'المنصورة، مصر',
            ],
            [
                'id' => 24,
                'name' => 'مهندس حسن مبيعات',
                'user_type' => 'salesEngineer',
                'phone' => '01000000020',
                'address' => 'أسيوط، مصر',
            ],
        ];
        foreach ($users as $userData) {
            $delta = 0.003;
            $latitude = $baseLatitude + (mt_rand(-1000, 1000) / 1000) * $delta;
            $longitude = $baseLongitude + (mt_rand(-1000, 1000) / 1000) * $delta;
            User::updateOrCreate(
                [
                    'id' => $userData['id'],
                ],
                [
                    ...$userData,
                    'latitude' => round($latitude, 6),
                    'longitude' => round($longitude, 6),
                ]
            );
        }
    }
}
