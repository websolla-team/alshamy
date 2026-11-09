@extends('layouts.master')

@section('css')
    <style>
        .admin-products { direction: rtl; text-align: right; }
        .admin-products .ap-heading { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
        .admin-products .ap-heading h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 5px; color: #22324a; }
        .admin-products .ap-heading p { margin: 0; color: #65748b; }
        .admin-products .ap-card { border: 1px solid #e5eaf1; border-radius: 12px; background: #fff; padding: 22px; }
        .admin-products .ap-filters { display: flex; flex-wrap: wrap; align-items: end; gap: 14px; margin-bottom: 18px; }
        .admin-products .ap-field { min-width: 175px; flex: 1; }
        .admin-products .ap-field--search { flex: 2; min-width: 230px; }
        .admin-products .ap-field label { display: block; font-weight: 600; color: #34435b; margin-bottom: 7px; }
        .admin-products .ap-field input,
        .admin-products .ap-field select { width: 100%; min-height: 43px; border: 1px solid #d6deea; border-radius: 8px; padding: 8px 12px; background: #fff; color: #26354c; }
        .admin-products .ap-field input:focus,
        .admin-products .ap-field select:focus { outline: 2px solid #92b5eb; outline-offset: 1px; }
        .admin-products .ap-reset { min-height: 43px; white-space: nowrap; }
        .admin-products .ap-results { color: #65748b; margin-bottom: 12px; }
        .admin-products .ap-table-wrap { overflow-x: auto; }
        .admin-products .ap-table { width: 100%; min-width: 840px; border-collapse: collapse; }
        .admin-products .ap-table th { background: #f6f8fc; color: #45536b; font-weight: 700; padding: 14px 12px; text-align: right; border-bottom: 1px solid #e4eaf2; }
        .admin-products .ap-table td { padding: 14px 12px; vertical-align: middle; border-bottom: 1px solid #edf0f5; }
        .admin-products .ap-table tbody tr:last-child td { border-bottom: 0; }
        .admin-products .ap-product { display: flex; align-items: center; gap: 12px; min-width: 200px; }
        .admin-products .ap-product img { width: 64px; height: 64px; flex: 0 0 64px; border-radius: 9px; object-fit: cover; background: #f0f2f6; }
        .admin-products .ap-photo-fallback { display: inline-grid; place-items: center; width: 64px; height: 64px; flex: 0 0 64px; border-radius: 9px; background: #f5ecdf; color: #78471f; font-size: .9rem; font-weight: 700; }
        .admin-products .ap-name { display: block; font-weight: 700; color: #24334a; }
        .admin-products .ap-id { display: block; margin-top: 4px; color: #8791a3; font-size: .79rem; }
        .admin-products .ap-variants { display: flex; flex-direction: column; gap: 7px; min-width: 225px; }
        .admin-products .ap-variant { display: flex; flex-wrap: wrap; align-items: baseline; gap: 7px; }
        .admin-products .ap-size { color: #485872; }
        .admin-products .ap-price { color: #24334a; font-weight: 700; }
        .admin-products .ap-old-price { color: #8791a3; font-size: .84rem; }
        .admin-products .ap-stock-note { color: #637189; font-size: .79rem; }
        .admin-products .ap-pill { display: inline-block; border-radius: 999px; padding: 5px 10px; font-weight: 600; font-size: .84rem; white-space: nowrap; }
        .admin-products .ap-pill--available { color: #116c4f; background: #e7f6ef; }
        .admin-products .ap-pill--out { color: #965b1f; background: #fff2df; }
        .admin-products .ap-pill--missing { color: #6b7280; background: #f0f1f4; }
        .admin-products .ap-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .admin-products .ap-actions .btn { min-width: 76px; padding: 8px 10px; font-weight: 600; }
        .admin-products .ap-empty { padding: 26px 12px; color: #66758d; text-align: center; }
        .admin-products .ap-scroll-hint { display: none; margin-bottom: 10px; color: #65748b; font-size: .87rem; }
        @media (max-width: 767px) {
            .admin-products .ap-card { padding: 14px; }
            .admin-products .ap-filters { gap: 12px; }
            .admin-products .ap-field { min-width: 100%; }
            .admin-products .ap-reset { width: 100%; }
            .admin-products .ap-scroll-hint { display: block; }
        }
    </style>
@endsection

@section('content')
    @php
        $categoryOptions = $products->pluck('category')->filter()->unique('id')->sortBy('title');
    @endphp

    <section class="admin-products mt-3 mb-4" dir="rtl" aria-labelledby="products-title">
        <div class="ap-heading">
            <div>
                <h1 id="products-title">الأصناف والأسعار</h1>
                <p>اعرض الأحجام والأسعار والمخزون، وابحث عن أي صنف بسرعة.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('products.create') }}">إضافة صنف جديد</a>
        </div>

        <div class="ap-card">
            <div class="ap-filters" role="search" aria-label="فلترة الأصناف">
                <div class="ap-field ap-field--search">
                    <label for="ap-search">بحث</label>
                    <input id="ap-search" type="search" placeholder="اسم الصنف أو الحجم" autocomplete="off">
                </div>
                <div class="ap-field">
                    <label for="ap-category">التصنيف</label>
                    <select id="ap-category">
                        <option value="">كل التصنيفات</option>
                        @foreach ($categoryOptions as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ap-field">
                    <label for="ap-stock">المخزون</label>
                    <select id="ap-stock">
                        <option value="">كل الحالات</option>
                        <option value="available">متاح</option>
                        <option value="out">نفد</option>
                        <option value="missing">بلا أحجام</option>
                    </select>
                </div>
                <button id="ap-reset" class="btn btn-outline-secondary ap-reset" type="button">مسح الفلاتر</button>
            </div>

            <p id="ap-results" class="ap-results" role="status" aria-live="polite">عدد الأصناف: {{ $products->count() }}</p>

            <p class="ap-scroll-hint">اسحب الجدول يمين وشمال علشان تشوف كل البيانات.</p>
            <div class="ap-table-wrap" tabindex="0" role="region" aria-label="جدول الأصناف قابل للتمرير أفقيًا">
                <table class="ap-table">
                    <thead>
                        <tr>
                            <th scope="col">الصنف</th>
                            <th scope="col">التصنيف</th>
                            <th scope="col">الأحجام والأسعار</th>
                            <th scope="col">المخزون</th>
                            <th scope="col">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="ap-product-rows">
                        @foreach ($products as $product)
                            @php
                                $variants = $product->variants;
                                $stock = (int) $variants->sum('quantity');
                                $stockStatus = $variants->isEmpty() ? 'missing' : ($stock > 0 ? 'available' : 'out');
                                $imagePath = trim((string) $product->image);
                            @endphp
                            <tr class="ap-product-row"
                                data-search="{{ $product->name . ' ' . ($product->category?->title ?? '') . ' ' . $variants->pluck('size')->implode(' ') }}"
                                data-category="{{ $product->category_id }}"
                                data-stock="{{ $stockStatus }}">
                                <td>
                                    <div class="ap-product">
                                        @if ($imagePath !== '' && $imagePath !== '/')
                                            <img src="{{ asset($imagePath) }}" alt="" loading="lazy" width="64" height="64">
                                        @else
                                            <span class="ap-photo-fallback" aria-hidden="true">الشامي</span>
                                        @endif
                                        <div>
                                            <span class="ap-name">{{ $product->name }}</span>
                                            <span class="ap-id">رقم الصنف: {{ $product->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category?->title ?? 'غير مصنف' }}</td>
                                <td>
                                    <div class="ap-variants">
                                        @forelse ($variants as $variant)
                                            <div class="ap-variant">
                                                <span class="ap-size">{{ $variant->size }}:</span>
                                                <span class="ap-price"><bdi>{{ number_format((float) $variant->price, 2) }}</bdi> {{ __('main.currency') }}</span>
                                                @if ($variant->old_price !== null && (float) $variant->old_price > (float) $variant->price)
                                                    <del class="ap-old-price"><bdi>{{ number_format((float) $variant->old_price, 2) }}</bdi></del>
                                                @endif
                                                <small class="ap-stock-note">({{ $variant->quantity > 0 ? 'متاح ' . $variant->quantity : 'نفد' }})</small>
                                            </div>
                                        @empty
                                            <span class="text-muted">لم تُضف أحجام أو أسعار</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    @if ($stockStatus === 'available')
                                        <span class="ap-pill ap-pill--available">متاح: {{ $stock }}</span>
                                    @elseif ($stockStatus === 'out')
                                        <span class="ap-pill ap-pill--out">نفد المخزون</span>
                                    @else
                                        <span class="ap-pill ap-pill--missing">غير مكتمل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="ap-actions">
                                        <a class="btn btn-primary" href="{{ route('products.edit', $product->id) }}" aria-label="تعديل {{ $product->name }}">تعديل</a>
                                        <a class="btn btn-outline-danger modal-effect employee-delete"
                                           data-effect="effect-slide-in-bottom" data-toggle="modal" href="#delete"
                                           data-id="{{ $product->id }}" aria-label="حذف {{ $product->name }}">حذف</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="ap-no-results" class="ap-empty" hidden>مفيش أصناف مطابقة للبحث أو الفلاتر.</p>
            </div>
        </div>
    </section>

    <div class="modal" id="delete" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h2 class="modal-title h5">حذف الصنف</h2>
                    <button aria-label="إغلاق" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('products.destroy', 'delete') }}" method="post">
                    <div class="modal-body">
                        <p>متأكد إنك عايز تحذف الصنف؟</p>
                        <input class="id" type="hidden" name="id" value="">
                        @csrf
                        @method('delete')
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="submit">تأكيد الحذف</button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function () {
            const search = document.getElementById('ap-search');
            const category = document.getElementById('ap-category');
            const stock = document.getElementById('ap-stock');
            const reset = document.getElementById('ap-reset');
            const results = document.getElementById('ap-results');
            const empty = document.getElementById('ap-no-results');
            const rows = Array.from(document.querySelectorAll('.ap-product-row'));

            function filterProducts() {
                const query = search.value.trim().toLocaleLowerCase();
                let count = 0;
                rows.forEach(function (row) {
                    const matches = row.dataset.search.toLocaleLowerCase().includes(query)
                        && (!category.value || row.dataset.category === category.value)
                        && (!stock.value || row.dataset.stock === stock.value);
                    row.hidden = !matches;
                    if (matches) count++;
                });
                results.textContent = 'الأصناف المعروضة: ' + count + ' من ' + rows.length;
                empty.hidden = count !== 0;
            }

            search.addEventListener('input', filterProducts);
            category.addEventListener('change', filterProducts);
            stock.addEventListener('change', filterProducts);
            reset.addEventListener('click', function () {
                search.value = '';
                category.value = '';
                stock.value = '';
                filterProducts();
                search.focus();
            });

            document.querySelectorAll('.employee-delete').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelector('#delete input.id').value = button.dataset.id;
                });
            });
        }());
    </script>
@endsection
