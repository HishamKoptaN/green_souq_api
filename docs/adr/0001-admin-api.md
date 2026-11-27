# ADR 001: طبقة API الإدارية للسوق الأخضر

## القرار
إنشاء مسارات تحت `/api/admin` محمية بـ Sanctum و middleware جديد `EnsureAdmin`، مع جدولي `roles` و`user_has_roles` لتحديد المشرفين. تسجيل الدخول الإداري يعيد استخدام تحقق Firebase الحالي (`FirebaseUseCases`) ثم يصدر رمز Sanctum بصلاحية `admin`، دون إدخال نظام كلمات مرور جديد.

## البدائل المرفوضة
- إضافة حقل `is_admin` فقط: لا يدعم توسيع الأدوار لاحقاً ولا يطابق بنية `user_has_roles` الموجودة في الكود القديم.
- استخدام Spatie Permission: حزمة جديدة غير معتمدة في المشروع، والجداول المطلوبة بسيطة.
- إعادة استخدام مسارات `/api/user` للإدارة: يخلط صلاحيات المستخدم العادي بصلاحيات المشرف.

## الأثر
ملفات: `routes/admin.php`، `bootstrap/app.php`، `app/Http/Middleware/EnsureAdmin.php`، `app/Features/Admin/**`، `app/Features/Users/Data/Models/User.php`، `database/migrations/*roles*`، `database/seeders/RolesSeeder.php`، `tests/Feature/Admin/**`.
الاختبارات تعمل على `dev` فقط وبقاعدة sqlite في الذاكرة.
