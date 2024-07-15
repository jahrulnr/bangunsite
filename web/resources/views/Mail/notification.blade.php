<!DOCTYPE html>
<html>
<head>
    <title>{{ $mailData['title'] }} - {{env('APP_NAME')}}</title>
</head>
<body>
    <h4 style="margin: 0">{{ $mailData['title'] }}</h4>
    <p>{{ $mailData['body'] }}</p>

    <p>Thank you</p>
    <hr>
</body>
</html>