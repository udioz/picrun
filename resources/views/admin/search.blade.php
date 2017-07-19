<!doctype html>
<html>
    <head>
    </head>

    <body>
        @foreach ($data['response'] as $phraseData)

            <h1>Phrase : {{ $phraseData['original'] }}</h1>
            @foreach ($phraseData['images'] as $image)
                @if (str_contains($image['url'],'.mp4'))
                <video controls="controls">
                    <source src="{{ $image['url'] }}" type="video/mp4">
                </video>
                @else
                  <img src="{{ $image['url'] }}"/>
                @endif

            @endforeach

        @endforeach
    </body>
</html>
