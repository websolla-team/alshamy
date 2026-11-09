# Alshamy Menu Import + Desktop Admin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** مطابقة 131 مدخلًا من المنيو المرسل مع قاعدة الشامي، وتقديم لوحة إدارة أسهل، بدون فقد منتجات أو طلبات أو نشر أسعار مجهولة.

**Architecture:** مطابقة المصدر بمفاتيح ثابتة، معاينة تقرير الفرق افتراضيًا، أمر تطبيق صريح بعد مراجعة بشرية؛ تحسين شاشات الإدارة الحالية بدل بناء لوحة جديدة. تطبيق تغييرات المخطط بعد مقارنة قاعدة المعاينة الحقيقية بمخطط المستودع.

**Tech Stack:** Laravel / Eloquent / DB transactions / Artisan / Blade / Bootstrap / PHPUnit.

**Spec:** `docs/superpowers/specs/2026-11-09-alshamy-menu-ux.md`

**Menu source:** `docs/catalog/menu-2026-11-09.md`

## Global Constraints
- جميع الأسعار جنيه مصري، والمصدر صور المنيو 2026-11-09، وتُراجع 7 ملاحظات عدم اليقين قبل التطبيق.
- لا استبدال صور أصلية، ولا تعديل أسعار قاعدة الإنتاج، ولا حذف منتجات/فئات قبل اعتماد جدول فروق.
- لا إنشاء مخزون/توفّر أو ترجمة إنجليزية وهمية. لا منتجات بسعر 0 كبديل لغياب السعر.
- الحفاظ على هوية `product.id` و`variant.id` متى ما أمكن؛ لا حذف متغير لمجرد غيابه من ملف.
- الإدارة تبدأ من عرض 1440 بكسل، مع بقاء 1024 قابلة للاستخدام.

## Review Focus
- الاسم القديم مختلف إملائيًا عن الجديد: يعرض «يحتاج مطابقة» ولا ينشئ نسخة جديدة تلقائيًا.
- تكرار الاستيراد: لا يكرر المنتج ولا حجمه ولا الإضافة.
- حجم جديد بسعر مختلف: لا يغير حجمًا آخر أو يمس طلبات قديمة.
- عنصر بلا صورة أو سعر واضح: لا يُنشر بصفر أو صورة ملفّقة.
- حذف قسم مملوء أو تعديل متغير مستخدم: لا يحذف المنتجات تبعيًا ولا يفقد سجل الطلبات.

---

### Task 1: مطابقة مخطط قاعدة المعاينة وحماية البيانات
**Files:**
- Inspect: `database/migrations/2024_08_05_082349_create_categories_table.php`
- Inspect: `database/migrations/2024_08_06_131013_create_variants_table.php`
- Inspect: `app/Models/Product.php`, `app/Models/Variant.php`, `app/Models/Category.php`
- Create/Modify: migration جديدة فقط بعد توثيق اختلافات القاعدة الحالية
- Test: `tests/Feature/MenuSchemaCompatibilityTest.php` (new)

**Interfaces:**
- Consumes: جداول `products,variants,categories,sizes,colors,orders,order_details`.
- Produces: مخطط متوافق مع المنتج/الخيار/التصنيف ومفتاح مصدر ثابت `menu_key` قابل للفهرسة.

- [ ] خذ نسخة احتياطية في بيئة المعاينة واقرأ `SHOW CREATE TABLE` / schema الفعلي، ولا تفترض تطابقه مع migrations.
- [ ] اكتب اختبار إنشاء منتج بخيار حجم دون لون، واختبار ترتيب الفئات ووجود جدول `sizes`، ثم شغله لبيان العطل.
- [ ] أصلح اختلاف `variants.color_id` الإجباري مع النموذج (وفق قرار المخطط بعد معاينته)، والحقول `categories.order_num`، وحفظ `category_type_id` إن كان مطلوبًا.
- [ ] أضف مفتاح مصدر ثابت اختياريًا وفريدًا للمنتجات/الخيارات المستوردة، دون تغيير مفاتيح القديمة.
- [ ] شغّل `php artisan migrate --pretend` على المعاينة ثم الاختبارات بعد التطبيق؛ تأكد من بقاء الطلبات والأصناف كما كانت.
- [ ] Commit: `fix(menu): align restaurant schema and source identity`.

### Task 2: تحويل التفريغ إلى ملف استيراد مُراجع
**Files:**
- Read: `docs/catalog/menu-2026-11-09.md`
- Create: `database/menu/alshamy-2026-11-09.json`
- Test: `tests/Unit/MenuSourceValidationTest.php`

**Interfaces:**
- Consumes: 131 بندًا في الملف المرجعي، منها «محشية الأطراف» إضافة.
- Produces: JSON منضبط بفئة، مفتاح ثابت، اسم عربي، اختيارات اسم/سعر، وصف/إضافات عند التأكد، علامات مراجعة.

- [ ] أنشئ تمثيلًا منفصلًا للأصناف والإضافات: لا تجعل «محشية الأطراف» بيتزا كاملة.
- [ ] اكتب اختبار `test_catalog_has_unique_menu_keys` واختبار رفض الأسعار السالبة أو غير الرقمية والمتغير بلا حجم.
- [ ] أدخل 131 بندًا تحت 13 عنوان قسم من صورتين، مع حفظ أي قيمة غير مؤكدة ك`needs_review`.
- [ ] راجع بالعين قيم العجل/الضاني، أحجام كول سلو/تومية، التوابل/إضافات السندوتش، وبيانات الفرع مع صاحب المنيو.
- [ ] شغّل اختبارات البيانات، وقارن عدد البنود والاختيارات والملحوظات بملف المصدر؛ Commit: `data(menu): transcribe reviewed menu source`.

### Task 3: أمر معاينة واستيراد آمنين
**Files:**
- Create: `app/Console/Commands/PreviewMenuImport.php`
- Create: `app/Console/Commands/ApplyMenuImport.php`
- Create: `app/Services/Menu/MenuImportService.php`
- Test: `tests/Feature/MenuImportTest.php`

**Interfaces:**
- Consumes: `database/menu/alshamy-2026-11-09.json` وجداول منتجات حالية.
- Produces: تقرير `new/update/unchanged/review`، وتطبيق `upsert` بعمليات مصرح بها فقط.

- [ ] اكتب اختبارات: معاينة بدون تعديل، تكرار التطبيق مرتين، الحفاظ على `product.id`، وعدم حذف المتغيرات الغائبة.
- [ ] شغّل الاختبارات وتأكد من الفشل قبل التنفيذ.
- [ ] نفّذ `php artisan menu:preview --source=database/menu/alshamy-2026-11-09.json` كأمر قراءة فقط مع إخراج تقرير الفروق ومطابقة الأسماء غير الواضحة.
- [ ] نفّذ `php artisan menu:apply --source=... --approved` بعمليات داخل معاملات قاعدة البيانات، وتوقف عند مدخل `needs_review` بلا قرار أو صورة مطلوبة غير متاحة.
- [ ] امنع حذف أي كتالوج قائم أو تغيير طلبات محفوظة؛ اختبر رد التغيير في حال فشل صف بعد صف صحيح.
- [ ] كرر نفس أمر المعاينة بعد التطبيق وتأكد من «0 new, 0 update»؛ Commit: `feat(menu): add reviewed idempotent import`.

### Task 4: شاشة منتجات وإدارة أسرع على الديسكتوب
**Files:**
- Modify: `resources/views/admin/products/index.blade.php`
- Modify: `resources/views/admin/products/create.blade.php`
- Modify: `resources/views/admin/products/edit.blade.php`
- Modify: `app/Http/Controllers/Admin/ProductController.php`
- Modify: `resources/views/admin/categories/index.blade.php`
- Test: `tests/Feature/AdminProductWorkflowTest.php`

**Interfaces:**
- Consumes: Product/Category/Variant وأسعارها وتوفرها.
- Produces: جدول بحث وتصفية، أسعار/أحجام واضحة، تعديل يحافظ على هويات الخيارات، حفظ مُتحقَّق منه.

- [ ] اكتب اختبارات رفض السعر/الكمية السالبة، والتحقق من حفظ الخيارات وتحديثها دون فقد.
- [ ] أصلح `store()` و`update()`: تحقق صريح من `array` والأطوال/المفاتيح، `old_price` الاختياري، وأي علاقة لون واقعية؛ لا حذف عرضي.
- [ ] اجعل جدول المنتجات يعرض الاسم والقسم والأحجام/الأسعار، وحالة التوفر، مع بحث واضح وزر تعديل ذي تسمية.
- [ ] رتّب نموذج المنتج للديسكتوب بعمودين منطقيين وقسم «الأحجام والأسعار»، مع تخفيف الاعتماد على الحقول الإلزامية التي لا يوفرها مصدر المنيو.
- [ ] أضف حماية من حذف فئة تحتوي أصنافًا، أو اطلب نقل الأصناف قبل الحذف؛ تحقق من الطلبات والمخزون.
- [ ] شغّل اختبارات Laravel، ولقطات 1440/1024 بعد تعديل منتج تجريبي؛ Commit: `feat(admin): streamline product and category management`.

### Task 5: شاشة رئيسية إدارية مفيدة ومراجعة الإصدار
**Files:**
- Modify: `resources/views/dashboard.blade.php`
- Modify: `routes/web.php` فقط إذا احتجنا مسارًا آمنًا لبيانات المؤشرات
- Test: `tests/Feature/AdminDashboardTest.php`

**Interfaces:**
- Consumes: عدد الأصناف والأقسام والأصناف التي تحتاج مراجعة، وحسابات صاحب الصلاحية.
- Produces: ملخص تشغيلي وروابط سريعة للمنتجات والتصنيفات بدل صفحة ترحيب فقط.

- [ ] اكتب اختبار دخول أدمن لصفحة الملخص ورفض غير الأدمن.
- [ ] اعرض إحصاءات حقيقية من قاعدة المعاينة فقط، والأصناف الناقصة بيانات، وروابط «المنتجات» و«الأقسام»، بدون رسوم أو مؤشرات مفترضة.
- [ ] افحص شاشة الإدارة 1440 و1024 واتجاه النص وحالات الجدول الفارغ والأسماء الطويلة.
- [ ] شغّل اختبارات النظام والتحقق المرئي، ثم تقرير فرق المنتجات قبل نشر أي قيمة.
- [ ] Commit: `feat(admin): useful desktop dashboard`.

## Release Gate
- لا تنفيذ على الإنتاج قبل توثيق مخططه، نسخة احتياطية، موافقة على الأسعار/الصور/العنوان، ونجاح تجارب المعاينة.
- `php` و`composer` غير متاحين على جهاز التخطيط الحالي؛ الاختبارات التنفيذية تُنفذ في بيئة PHP/خادم المشروع عند بدء التنفيذ.
