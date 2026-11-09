@extends('layouts.master')

@section('css')
    <style>
        .product-editor { direction: rtl; text-align: right; max-width: 1200px; margin: 24px auto; }
        .product-editor .editor-heading { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
        .product-editor h1 { font-size: 1.6rem; font-weight: 700; color: #26354c; margin: 0; }
        .product-editor .editor-panel { background: #fff; border: 1px solid #e1e7f0; border-radius: 12px; padding: 26px; margin-bottom: 20px; }
        .product-editor h2 { font-size: 1.15rem; font-weight: 700; margin: 0 0 9px; }
        .product-editor .editor-help { color: #66768d; margin: 0 0 18px; }
        .product-editor .editor-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .product-editor .editor-field { min-width: 0; }
        .product-editor .editor-field--full { grid-column: 1 / -1; }
        .product-editor .editor-field label { display: block; margin-bottom: 7px; font-weight: 600; color: #314159; }
        .product-editor .editor-field .form-control { min-height: 43px; border-radius: 7px; }
        .product-editor .editor-field textarea.form-control { min-height: 95px; }
        .product-editor .variant-row { display: grid; grid-template-columns: minmax(155px, 1.35fr) minmax(125px, 1fr) minmax(125px, 1fr) minmax(125px, 1fr) auto; gap: 12px; align-items: end; padding: 17px; margin-bottom: 12px; border: 1px solid #e4eaf2; border-radius: 9px; background: #fafbfe; }
        .product-editor .variant-row .editor-field label { margin-bottom: 6px; }
        .product-editor .variant-row .btn { min-height: 43px; }
        .product-editor .editor-actions { display: flex; justify-content: flex-start; gap: 12px; flex-wrap: wrap; }
        .product-editor .editor-actions .btn { min-height: 44px; min-width: 130px; }
        .product-editor .editor-validation { display: block; color: #b42318; font-size: .86rem; margin-top: 5px; }
        @media (max-width: 1050px) {
            .product-editor .variant-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 650px) {
            .product-editor .editor-panel { padding: 16px; }
            .product-editor .editor-grid, .product-editor .variant-row { grid-template-columns: minmax(0, 1fr); }
            .product-editor .editor-field--full { grid-column: auto; }
        }
    </style>
@endsection

@section('content')
    @php
        $formVariants = old('variants') ?: [['size' => '', 'quantity' => 0, 'price' => '', 'old_price' => '']];
    @endphp
    <div class="product-editor" dir="rtl">
        <div class="editor-heading">
            <h1>إضافة صنف جديد</h1>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">رجوع للأصناف</a>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">راجع الحقول المعلّمة قبل الحفظ.</div>
        @endif
        <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <section class="editor-panel" aria-labelledby="details-title">
                <h2 id="details-title">بيانات الصنف</h2>
                <p class="editor-help">الصورة والوصف والاسم الإنجليزي ممكن تضيفهم بعدين. لحد ما ترفع صورة، هيظهر شعار المطعم.</p>
                <div class="editor-grid">
                    <div class="editor-field">
                        <label for="product-name-ar">اسم الصنف بالعربي *</label>
                        <input id="product-name-ar" class="form-control" name="name[ar]" value="{{ old('name.ar') }}" maxlength="255" required>
                        @error('name.ar') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field">
                        <label for="product-name-en">الاسم الإنجليزي (اختياري)</label>
                        <input id="product-name-en" class="form-control" name="name[en]" value="{{ old('name.en') }}" maxlength="255">
                        @error('name.en') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field">
                        <label for="product-category">التصنيف *</label>
                        <select id="product-category" class="form-control" name="category_id" required>
                            <option value="">اختار التصنيف</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field">
                        <label for="product-image">صورة الصنف (اختياري، حتى 1 ميجابايت)</label>
                        <input id="product-image" type="file" class="form-control" name="image" accept="image/*">
                        @error('image') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field">
                        <label for="product-desc-ar">الوصف بالعربي (اختياري)</label>
                        <textarea id="product-desc-ar" name="description[ar]" class="form-control" rows="3">{{ old('description.ar') }}</textarea>
                        @error('description.ar') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field">
                        <label for="product-desc-en">الوصف الإنجليزي (اختياري)</label>
                        <textarea id="product-desc-en" name="description[en]" class="form-control" rows="3">{{ old('description.en') }}</textarea>
                        @error('description.en') <span class="editor-validation">{{ $message }}</span> @enderror
                    </div>
                    <div class="editor-field editor-field--full">
                        <label for="product-featured">
                            <input id="product-featured" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            عرض الصنف ضمن الأكثر طلبًا
                        </label>
                    </div>
                </div>
            </section>

            <section class="editor-panel" aria-labelledby="variants-title">
                <h2 id="variants-title">الأحجام والأسعار</h2>
                <p class="editor-help">سجّل كل حجم وسعره بنفسك. الكمية صفر معناها الصنف مش متاح للطلب. سعر قبل الخصم اختياري.</p>
                @error('variants') <p class="editor-validation">{{ $message }}</p> @enderror
                <datalist id="available-sizes">
                    @foreach ($sizes as $size)
                        <option value="{{ $size->name }}"></option>
                    @endforeach
                </datalist>
                <div id="variant-rows">
                    @foreach ($formVariants as $key => $variant)
                        <div class="variant-row" data-variant-row>
                            <div class="editor-field">
                                <label>الحجم *</label>
                                <input class="form-control" list="available-sizes" name="variants[{{ $key }}][size]" value="{{ $variant['size'] ?? '' }}" maxlength="100" required>
                                @error('variants.' . $key . '.size') <span class="editor-validation">{{ $message }}</span> @enderror
                            </div>
                            <div class="editor-field">
                                <label>السعر (جنيه) *</label>
                                <input class="form-control" type="number" step="0.01" min="0.01" name="variants[{{ $key }}][price]" value="{{ $variant['price'] ?? '' }}" required>
                                @error('variants.' . $key . '.price') <span class="editor-validation">{{ $message }}</span> @enderror
                            </div>
                            <div class="editor-field">
                                <label>سعر قبل الخصم</label>
                                <input class="form-control" type="number" step="0.01" min="0" name="variants[{{ $key }}][old_price]" value="{{ $variant['old_price'] ?? '' }}">
                                @error('variants.' . $key . '.old_price') <span class="editor-validation">{{ $message }}</span> @enderror
                            </div>
                            <div class="editor-field">
                                <label>الكمية *</label>
                                <input class="form-control" type="number" step="1" min="0" name="variants[{{ $key }}][quantity]" value="{{ $variant['quantity'] ?? 0 }}" required>
                                @error('variants.' . $key . '.quantity') <span class="editor-validation">{{ $message }}</span> @enderror
                            </div>
                            <button class="btn btn-outline-danger" type="button" data-remove-variant>إزالة</button>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-outline-primary" id="add-variant" type="button">+ إضافة حجم وسعر</button>
            </section>

            <div class="editor-actions">
                <button class="btn btn-primary" type="submit">حفظ الصنف</button>
                <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">إلغاء</a>
            </div>
        </form>
    </div>

    <template id="variant-template">
        <div class="variant-row" data-variant-row>
            <div class="editor-field"><label>الحجم *</label>
                <input class="form-control" list="available-sizes" name="variants[__INDEX__][size]" maxlength="100" required>
            </div>
            <div class="editor-field"><label>السعر (جنيه) *</label>
                <input class="form-control" type="number" step="0.01" min="0.01" name="variants[__INDEX__][price]" required>
            </div>
            <div class="editor-field"><label>سعر قبل الخصم</label>
                <input class="form-control" type="number" step="0.01" min="0" name="variants[__INDEX__][old_price]">
            </div>
            <div class="editor-field"><label>الكمية *</label>
                <input class="form-control" type="number" step="1" min="0" name="variants[__INDEX__][quantity]" value="0" required>
            </div>
            <button class="btn btn-outline-danger" type="button" data-remove-variant>إزالة</button>
        </div>
    </template>
@endsection

@section('js')
    <script>
        (function () {
            const rows = document.getElementById('variant-rows');
            const template = document.getElementById('variant-template');
            let nextIndex = 0;
            document.getElementById('add-variant').addEventListener('click', function () {
                let index;
                do { index = 'new_' + nextIndex++; }
                while (rows.querySelector('[name="variants[' + index + '][size]"]'));
                rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
                rows.lastElementChild.querySelector('[name$="[size]"]').focus();
            });
            rows.addEventListener('click', function (event) {
                if (!event.target.matches('[data-remove-variant]')) return;
                if (rows.querySelectorAll('[data-variant-row]').length <= 1) return;
                event.target.closest('[data-variant-row]').remove();
            });
        }());
    </script>
@endsection
