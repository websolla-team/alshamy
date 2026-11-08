<!-- Title -->
<title> {{ env('APP_NAME') }} Dashboard </title>
<!-- Favicon -->
<link rel="icon" href="{{ URL::asset('logo/favicon.png') }}" type="image/x-icon" />
<!-- Icons css -->
<link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{ URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css') }}" rel="stylesheet" />
<!--  Sidebar css -->
<link href="{{ URL::asset('assets/plugins/sidebar/sidebar.css') }}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{ URL::asset('assets/css/sidemenu.css') }}">
@yield('css')
<!--- Style css -->
<link href="{{ URL::asset('assets/css/style.css') }}?v=2" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{ URL::asset('assets/css/style-dark.css') }}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{ URL::asset('assets/css/skin-modes.css') }}" rel="stylesheet">
<script src="{{ asset('assets/vendor/ckeditor5/ckeditor.js') }}"></script>
