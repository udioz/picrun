<!doctype html>
<html>
    <head>
    </head>

    <body>
        @foreach ($data['response'] as $phraseData)
            
            <h1>Phrase : {{ $phraseData['original'] }}</h1>
            @foreach ($phraseData['images'] as $image)

                <img src="{{ $image['url'] }}"/>

            @endforeach

        @endforeach
    </body>
</html>
