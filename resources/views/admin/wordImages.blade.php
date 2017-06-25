<!doctype html>
<html>
    <head>
    </head>

    <body>
        <ul>
          @foreach ($images as $image)
            <img src="{{ $image->link }}"/>
          @endforeach
        </ul>
    </body>
</html>
