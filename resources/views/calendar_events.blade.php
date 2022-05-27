<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DeweSoft Assignment</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
 
    <link href="{{ asset('css/list-groups.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.2/examples/list-groups/" rel="canonical">
</head>
<body>
    <h1 class="m-4 row justify-content-center"> {{ $heading }} </h1>
    <h2 class="row justify-content-center"> {{ $sub_heading }} </h2>

    <ol class="list-group w-auto">
       
        <button class="align-content-center bg-white"><a class="text-black" href="/">Refresh<a></button>
        
        @if (!$calendar_events) 
             <li class="list-group-item">
                    <h4 style="text-align: center;">There are no events to show.</h4> 
            </li>   
        @else
            @foreach ($calendar_events as $calendar_event)
                <li class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                    <div class="d-flex gap-2 w-100 justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ $calendar_event['title'] }}</h6>
                            <p class="mb-0 opacity-75 limit-width">{{ $calendar_event['description'] }}</p>
                        </div>
                        <small class="text-nowrap">{{ $calendar_event['start_date'] }}</small>
                    </div>
                </li>
            @endforeach
        @endif 
    </ol>

</body>
</html>