# Alshamy Storefront Mobile-First Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** إصلاح تجربة قائمة الشامي على الموبايل مع بقاء هوية المطعم وتحسين الديسكتوب.

**Architecture:** تحسين قوالب Blade الحالية وتخصيصات CSS في آخر الملف، وتصفية الفئات الفارغة في الاستعلام. لا تغيير شامل لإطار العمل، ولا إطلاق سلة شراء ناقصة.

**Tech Stack:** Laravel / Blade / Bootstrap / CSS / JavaScript / Browser QA.

**Spec:** `docs/superpowers/specs/2026-11-09-alshamy-menu-ux.md`

## Global Constraints
- متصفح العملاء يبدأ من 360/390/430 بكسل؛ الإدارة خارج هذه الخطة.
- RTL عربي، والمحافظة على الشعار والألوان والحروف الصحيحة.
- السعر من المتغير المعتمد، ولا تعرض خصم 0 أو صفرًا مكان غياب السعر.
- لا تغيير في الأسعار أو قاعدة الإنتاج ضمن هذه الخطة.
- لا تفعيل دفع أو سلة ما دامت قوالب السلة/الدفع ناقصة.

## Review Focus
- فئة بلا منتجات: تختفي من شريط التبويب، وتُختار أول فئة غير فارغة تلقائيًا.
- صنف بحجم واحد: يظهر السعر والحجم مباشرة دون اختيار عديم الفائدة.
- صنف بحجمين: تغيير الحجم لا يغير سعر بطاقة صنف آخر.
- السعر القديم 0 أو NULL: يختفي بالكامل ولا تُطبع شرطة زائدة.
- شاشة 360 بكسل / تكبير 200٪ / اسم طويل: لا يوجد قطع أو تمرير أفقي للصفحة.

---

### Task 1: تنظيف عرض المنتجات والتصنيفات
**Files:**
- Modify: `app/Http/Controllers/Front/HomeController.php:58-81`
- Modify: `resources/views/website/index.blade.php:39-130`
- Test: `tests/Feature/StorefrontMenuTest.php` (new)

**Interfaces:**
- Consumes: `Category::products`, `Product::variants`, `Product::oneVariant`.
- Produces: فئات لها منتجات صالحة للعرض فقط؛ السعر الابتدائي اختيار واضح.

- [ ] اكتب اختبار `test_empty_category_is_not_rendered` مع فئة بيتزا خالية وفئة بها منتج.
- [ ] شغّل `php artisan test --filter=StorefrontMenuTest` وتأكد أنه يفشل قبل التعديل.
- [ ] عدل استعلام `HomeController::index()` لجلب المنتجات/الأسعار اللازمة مرة واحدة، وعدم عرض الفئات الخالية، مع الحفاظ على ترتيبها.
- [ ] عدل قالب كل منتج: لا رابط `href="#"` كاذب، ولا استخدام `old_price=0` كخصم.
- [ ] أضف الاختبارات `test_single_variant_price`, `test_two_variant_prices`, `test_zero_old_price_hidden` وشغّل نفس الأمر للتحقق.
- [ ] Commit: `fix(storefront): render only populated menu categories`.

### Task 2: تصنيفات موبايل في صف واحد
**Files:**
- Modify: `public/website/assets/css/main.css:7409-7521`
- Modify: `resources/views/website/index.blade.php:139-166`
- Test: `tests/browser/storefront.spec.js` (new; استخدم متصفحًا متاحًا بالمشروع)

**Interfaces:**
- Consumes: تبويبات Bootstrap `#pills-tab .nav-link`.
- Produces: صف واحد قابل للتمرير باللمس، فعال بلوحة المفاتيح، والتبويب النشط واضح.

- [ ] اكتب فحص متصفح بعرض 390: `nav-pills` صف واحد، التنقل بين أول وآخر قسم يعمل.
- [ ] شغّل الفحص على الموقع الحالي وسجل فشله.
- [ ] في CSS أضف `flex-wrap: nowrap` صراحة، `overflow-x: auto`، هوامش لمس مناسبة، وإزالة التكرار المتعارض بين قواعد عرض 768 بكسل.
- [ ] احذف سلوك سحب الماوس المربك أو اجعله مكمّلًا للتمرير الأصلي، واحفظ قابلية الوصول باللوحة.
- [ ] تحقق عند 360/390/430/768/1440 بصور وفحص عدم وجود تمرير أفقي للصفحة.
- [ ] Commit: `fix(storefront): mobile-first category navigation`.

### Task 3: إعادة ضبط بطاقة المنتج والأسعار
**Files:**
- Modify: `resources/views/website/index.blade.php:59-116`
- Modify: `resources/views/website/index.blade.php:170-193`
- Modify: `public/website/assets/css/main.css:7606-7686`
- Test: `tests/Feature/StorefrontMenuTest.php`

**Interfaces:**
- Consumes: `variants` المرتبة والسعر والاسم والوصف والصورة.
- Produces: بطاقة عرض صورة واسم ووصف مختصر وسعر/اختيار حجم متناسق في RTL.

- [ ] اكتب اختبار سعر المنتج: «وجبة كريسبي 4 قطع» وسعر الحجم المختار، بدون `EGP 157 -`.
- [ ] نفّذ قالب بطاقة واحد يعرض السعر بالجنيه ويحافظ على اتجاه الأرقام؛ لا يعرض اختيار حجم إلا إذا تعددت الخيارات.
- [ ] ضبط صورة 64–80 بكسل، خط الاسم والوصف، تباين الألوان، المسافات، ووضع السعر، مع الحفاظ على تخطيط عمود الموبايل.
- [ ] حمّل صور المنتجات خارج الشاشة بصورة مؤجلة، وأعطِ `alt` وصفيًا بدل `thumb`.
- [ ] اختبر الأسماء الطويلة، والمحتوى الناقص، ومقاسات الشاشات الخمسة، ثم Commit: `refactor(storefront): readable menu item cards`.

### Task 4: جودة التحميل والوصول والتفاعل
**Files:**
- Modify: `resources/views/website/layouts/master.blade.php:2-15`
- Modify: `resources/views/website/layouts/footer-scripts.blade.php:48-70`
- Modify: `resources/views/website/layouts/nav.blade.php:165-233`
- Test: `tests/browser/storefront.spec.js`

**Interfaces:**
- Consumes: وجود `#exampleModal` بشكل اختياري، اللغة الحالية، عناصر القائمة.
- Produces: لا استثناءات JavaScript ولا حظر للتكبير، وشريط رأس ملائم للموبايل.

- [ ] اكتب فحص متصفح «لا أخطاء pageerror» و«زر القائمة يعمل» و«التكبير لا يُمنع».
- [ ] أصلح الوصول إلى `modal.addEventListener` عند غياب `#exampleModal`، وفحص عناصر النوافذ الأخرى قبل استخدامها.
- [ ] استخدم `dir=rtl` للعربية، ووسم viewport واحدًا يسمح بالتكبير، وتخلص من favicon المكسور.
- [ ] اضبط رأس الصفحة وزر الاتصال المتاح من `$Contact` لتبقى طريقة طلب واضحة دون تفعيل دفع.
- [ ] شغّل اختبار Laravel والمتصفح، و`impeccable detect --json` على القوالب المعدلة، وصور موبايل وديسكتوب مرتين كحد أقصى.
- [ ] Commit: `fix(storefront): accessible shell and safe scripts`.

## Verification / handoff
- شغّل المتجر في بيئة معاينة بها PHP والمكتبات وبيانات تجريبية؛ PHP غير متاح حاليًا على جهاز التخطيط.
- راجع تصنيفًا فارغًا، وأسعارًا متعددة، واختيار حجم، وروابط الهاتف، وبدء التحميل.
- أرفق صور 390 و768 و1440 قبل/بعد، ولا تنشر دون الموافقة على بيانات الأسعار.
