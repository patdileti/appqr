<!DOCTYPE html>
<html lang="{{ get_lang() }}" dir="{{ current_language()->direction }}">
<head>
    <title>{{ ___('Redirecting...') }}</title>
</head>
<body>
<p>{{ ___('Please do not refresh this page...') }}</p>
<form method="post" action="{{$url}}" name="f1">
    @foreach ($data as $name => $value)
        <input type="hidden" name="{{$name}}" value="{{$value}}">
    @endforeach
</form>
<script type="text/javascript">
    document.f1.submit();
</script>
</body>
</html>
