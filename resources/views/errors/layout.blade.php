<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            body {
                background-color: #f3f4f6; /* Light gray background */
                font-family: 'Nunito', sans-serif;
                color: #374151; /* Darker gray for text */
                margin: 0;
                padding: 0;
            }

            .container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                text-align: center;
                padding: 2rem;
            }

            .error-code {
                font-size: 10rem; /* Much larger */
                font-weight: 700; /* Bold */
                color: #ef4444; /* A nice red color */
                line-height: 1;
                letter-spacing: -0.05em;
            }

            .error-title {
                font-size: 1.5rem; /* Larger message */
                font-weight: 600;
                color: #1f2937; /* Even darker for the main message */
                margin-top: 1rem;
                max-width: 600px;
            }
            
            .error-message {
                font-size: 1rem;
                color: #6b7280; /* Lighter gray for description */
                margin-top: 0.5rem;
            }

            .home-link {
                display: inline-block;
                margin-top: 2.5rem;
                padding: 0.75rem 1.5rem;
                background-color: #3b82f6; /* A nice blue */
                color: #ffffff;
                font-weight: 600;
                text-decoration: none;
                border-radius: 0.5rem;
                transition: background-color 0.3s ease;
            }

            .home-link:hover {
                background-color: #2563eb; /* Darker blue on hover */
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-code">
                @yield('code')
            </div>
            <div class="error-title">
                @yield('title')
            </div>
            <div class="error-message">
                @yield('message')
            </div>
            <a href="{{ url('/') }}" class="home-link">Volver al Inicio</a>
        </div>
    </body>
</html>
