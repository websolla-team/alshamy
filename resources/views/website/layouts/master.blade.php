<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Author" content="Web Solla">
    <meta name="author" content="websolla.com">
    <meta name="keywords" content="{{ $Setting->keywords }}">
    <meta name="description" content="{{ $Setting->description }}">

    @include('website.layouts.head')
</head>


<body class="bg-color2">

	<div class="site-content">


        @include('website.layouts.topbar')
        @yield('content')
        @include('website.layouts.footer')

    </div>
    @include('website.layouts.footer-scripts')
    <script>
        $(document).ready(function() {
            var url = window.location.href;
            url = url.split('?', 1)[0];
            $('.nav li').filter(function() {
                return $(this).find('a').attr('href') == url;
            }).addClass('active');
        });
    </script>
</body>

</html>
