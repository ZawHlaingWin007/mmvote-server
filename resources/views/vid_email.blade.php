
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> </title>
    <style>

.btn{
    text-decoration: none;
    background-color:blue;
    color:white!important;
    cursor: pointer;
    padding: 10px 10px;
}

hr{
    margin-top: 30px;
}
    </style>
</head>
<body>

    <div>
        <h2>{!! $content !!}</h2>
    <br>
    <!--<a href="{{$link}}" style="text-decoration: none;-->
    <!--background-color:blue;-->
    <!--color:white!important; -->
    <!--cursor: pointer;-->
    <!--padding: 10px 10px;">Click here to Vote</a>-->
    <h3>{{ $link }}</h3>
    <hr>
    {!! $footer !!}
    <br>
    <img src="{{$message->embed($image)}}" style="width: 40%" alt="Image">
    <br>
    <b>{{ $date }}</b>
    <b> / {{ $time }}</b>
    </div>

</body>
</html>
