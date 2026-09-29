<?php

namespace Database\Seeders;

use App\Features\Posts\Data\Models\Post;
use App\Features\Posts\Data\Models\SocialPost;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Seeder;

class SocialPostsSeeder extends Seeder
{
    public function run(): void
    {
        $postsData = [
            ['id' => 1, 'type' => 'shop', 'content' => 'أفضل العروض على المبيدات الزراعية والأسمدة في محل النماء.'],
            ['id' => 2, 'type' => 'salesEngineer', 'content' => 'استشارة هندسية مجانية لتحسين كفاءة التسميد الكيماوي والحيوي.'],
            ['id' => 3, 'type' => 'company', 'content' => 'تعلن شركة الزراعة الحديثة عن طرح جيل جديد من الأسمدة العضوية المركزة.'],
            ['id' => 4, 'type' => 'farmer', 'content' => 'بدء حصاد محصول القمح في مزرعة النخيل بحمد الله وبإنتاجية عالية.'],
            ['id' => 5, 'type' => 'shop', 'content' => 'توفر لدينا أدوات الري الحديثة وبطاريات الرش بأفضل الأسعار بمحل الأمل.'],
            ['id' => 6, 'type' => 'salesEngineer', 'content' => 'دعم فني متكامل لمزارع البطاطس والطماطم للوقاية من الآفات.'],
            ['id' => 7, 'type' => 'company', 'content' => 'شركة الوادي الأخضر تطلق مبادرة دعم المزارعين المصريين بأحدث التقنيات.'],
            ['id' => 8, 'type' => 'farmer', 'content' => 'تجارب ناجحة لزراعة البطاطس باستخدام الري بالتنقيط الحديث في مزرعة الخير.'],
            ['id' => 9, 'type' => 'shop', 'content' => 'تشكيلة واسعة من البذور المحسنة عالية الإنتاجية في محل النور.'],
            ['id' => 10, 'type' => 'salesEngineer', 'content' => 'كيفية اختيار نظام الري بالتنقيط الأنسب لطبيعة تربتك الزراعية.'],
            ['id' => 11, 'type' => 'company', 'content' => 'عروض خاصة للشركات والمزارع الكبرى على منظومات الطاقة الشمسية للري.'],
            ['id' => 12, 'type' => 'farmer', 'content' => 'نصائح عملية لكل مزارع لزيادة نسبة إنبات بذور الخضروات.'],
            ['id' => 13, 'type' => 'shop', 'content' => 'مستلزمات البيوت المحمية وشبكات الري متوفرة الآن لدى محل البركة.'],
            ['id' => 14, 'type' => 'salesEngineer', 'content' => 'برنامج وقائي متكامل لمكافحة سوسة النخيل والآفات الحشرية.'],
            ['id' => 15, 'type' => 'company', 'content' => 'شركة النيل للأسمدة تنظم ندوة ارشادية كبرى لمهندسي المبيعات والمزارعين.'],
            ['id' => 16, 'type' => 'farmer', 'content' => 'تقرير عن نجاح موسم حصاد المانجو والتمور في مزرعة البركة والروابي.'],
            ['id' => 17, 'type' => 'shop', 'content' => 'زوروا محل التوحيد للاستفادة من تخفيضات الموسم الزراعي الجديد.'],
            ['id' => 18, 'type' => 'salesEngineer', 'content' => 'إرشادات هندسية لرفع إنتاجية المحاصيل الحقلية بأقل تكلفة ممكنة.'],
            ['id' => 19, 'type' => 'company', 'content' => 'ابتكارات جديدة في عالم المكافحة الحيوية تقدمها شركة المستقبل الزراعي.'],
            ['id' => 20, 'type' => 'farmer', 'content' => 'تجربتي الشخصية كمزارع في استخدام الأسمدة العضوية لتحسين خصوبة التربة.'],
        ];
        foreach ($postsData as $data) {
            $postType = $data['type'];
            $user = User::where('user_type', $postType)->inRandomOrder()->first();
            if ($user) {
                $socialPost = SocialPost::updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    [
                        'content' => $data['content'],
                    ]
                );
                Post::updateOrCreate(
                    [
                        'id' => $data['id'],
                        'user_id' => $user->id,
                        'postable_type' => 'social',
                        'postable_id' => $socialPost->id,
                    ],
                    []
                );
            }
        }
    }
}
