<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @include ('assets/head.blade.php')

        @yield ('view.head')
    </head>

    <body>
        @include ( 'components/page-loader.blade.php' )

        @include ( 'components/loader.blade.php' )



        @yield ('view.body')



        @include( 'components/spinner.blade.php' )



        @include ('assets/body.blade.php')

        @yield ('view.script')



        <script>
        
            
        
        </script>
    </body>
</html>
