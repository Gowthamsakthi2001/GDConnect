<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Ticket Portal')</title>
  <link rel="shortcut icon" href="{{ url('/') }}/storage/setting/ycsbDAa4bOn4ouFfSKkJ0o5C8prSzthSJEUHG078.png?v=1"> 
  @include('components.ticket_portal.web_style')
  @yield('style_css')
</head>
<body>

@include('components.ticket_portal.web_navbar')

@yield('contents')

@include('components.ticket_portal.web_scripts')
@yield('script_js')

</body>
</html>
