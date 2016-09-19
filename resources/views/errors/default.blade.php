<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Error</title>

        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Feature!</div>
                <h1>
        {{ $exception->getStatusCode() }}
    </h1>
 
    <p>
        @if(!empty($exception->getMessage()))
            {{ $exception->getMessage() }}
        @else
            {{ \Symfony\Component\HttpFoundation\Response::$statusTexts[$exception->getStatusCode()] }}
        @endif
    </p>
            </div>
        </div>
    </body>
</html>
