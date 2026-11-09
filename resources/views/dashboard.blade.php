@extends('layouts.master')

@section('css')
    <style>
        .admin-home { direction: rtl; text-align: right; color: #25344d; padding-block: 24px 38px; }
        .admin-home .ah-intro { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 26px; }
        .admin-home .ah-intro h1 { font-size: 1.75rem; font-weight: 700; margin: 0 0 9px; }
        .admin-home .ah-intro p { margin: 0; color: #66758d; font-size: 1rem; }
        .admin-home .ah-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .admin-home .ah-card { padding: 25px; border-radius: 12px; border: 1px solid #e2e8f1; background: #fff; }
        .admin-home .ah-card h2 { font-size: 1.2rem; font-weight: 700; color: #25344d; margin: 0 0 8px; }
        .admin-home .ah-card p { color: #66758d; line-height: 1.7; margin: 0 0 20px; }
        .admin-home .ah-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
        .admin-home .ah-actions .btn { min-height: 42px; display: inline-flex; justify-content: center; align-items: center; padding-inline: 16px; font-weight: 600; }
        .admin-home a:focus-visible { outline: 2px solid #4789e7; outline-offset: 3px; }
        @media (max-width: 740px) {
            .admin-home { padding-block-start: 16px; }
            .admin-home .ah-grid { grid-template-columns: minmax(0, 1fr); gap: 14px; }
            .admin-home .ah-card { padding: 20px; }
            .admin-home .ah-intro .btn { width: 100%; }
        }
    </style>
@endsection

@section('content')
    <main class="admin-home" dir="rtl">
        <header class="ah-intro">
            <div>
                <h1>لوحة التحكم</h1>
                <p>أهلًا {{ auth()->user()->first_name }}، اختار اللي محتاج تشتغل عليه.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
                معاينة المتجر
            </a>
        </header>

        <div class="ah-grid">
            <section class="ah-card" aria-labelledby="ah-products">
                <h2 id="ah-products">إدارة الأصناف والأسعار</h2>
                <p>راجع القائمة والأحجام والأسعار المتاحة وعدّل أي صنف.</p>
                <div class="ah-actions">
                    <a class="btn btn-primary" href="{{ route('products.index') }}">عرض الأصناف</a>
                    <a class="btn btn-outline-primary" href="{{ route('products.create') }}">إضافة صنف</a>
                </div>
            </section>

            <section class="ah-card" aria-labelledby="ah-categories">
                <h2 id="ah-categories">تنظيم المنيو</h2>
                <p>رتّب أقسام المنيو واعرض الأصناف تحت كل تصنيف.</p>
                <div class="ah-actions">
                    <a class="btn btn-primary" href="{{ route('categories.index') }}">عرض التصنيفات</a>
                    <a class="btn btn-outline-primary" href="{{ route('categories.create') }}">إضافة تصنيف</a>
                </div>
            </section>

            <section class="ah-card" aria-labelledby="ah-sizes">
                <h2 id="ah-sizes">الأحجام المتاحة</h2>
                <p>راجع الأحجام اللي بتظهر عند إضافة صنف أو تعديل سعره.</p>
                <div class="ah-actions">
                    <a class="btn btn-primary" href="{{ route('sizes.index') }}">عرض الأحجام</a>
                    <a class="btn btn-outline-primary" href="{{ route('sizes.create') }}">إضافة حجم</a>
                </div>
            </section>

            <section class="ah-card" aria-labelledby="ah-settings">
                <h2 id="ah-settings">إعدادات المتجر</h2>
                <p>عدّل بيانات المتجر وإعداداته العامة من مكان واحد.</p>
                <div class="ah-actions">
                    <a class="btn btn-primary" href="{{ route('settings.index') }}">فتح الإعدادات</a>
                </div>
            </section>
        </div>
    </main>
@endsection
