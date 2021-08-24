<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Upload</title>
</head>
<body>
<form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
    @csrf
{{--    <img src="https://vti7-dev-ed--c.documentforce.com/sfc/dist/version/download/?oid=00D5g00000A4cc5&ids=0685g0000006E3f&d=%2Fa%2F5g000000wmL5%2F6PdjNJNJubdhspQo40Hoaa9t6Dryf4kNxTPHcCN6D94&asPdf=false" alt="" width="500" height="700">--}}
    <input name="file[]" type="file" accept="image/*" multiple>
    <input type="submit" value="Gửi">
</form>
</body>
</html>
