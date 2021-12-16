<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body {
            background: #f7f7f7;
        }

        .email {
            padding: 0 35px;
        }

        .email>.header {
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #08b059;
        }

        .button-link {
            display: flex;
            justify-content: center;
        }

        .btn-link {
            padding: 10px;
            width: 300px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #ffffff;
            background-color: #08b059;
            border-radius: 4px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .email>.body {
            background-color: #fff;
            padding: 10px 20px;
        }

        .email>.body>h2 {
            font-size: 20px;
            text-align: center;
        }

        .email>.body>.thanks {
            font-size: 14px;
            margin-bottom: 25px;
            text-align: center;
        }

        .email>.footer {
            text-align: center;
            background-color: #0a4a7a;
            border-top: 3px solid #08b059;
            padding: 2px;
            color: #cecece;
            font-size: 14px
        }

        .email>.footer>p>a {
            text-decoration: none;
            color: #fff;
        }

        .contact {
            text-align: center;
            font-size: 14px;
            color: #63686c;
        }

        .contact a {
            color: #63686c;
        }

        .contact>ul {
            margin: 0;
            padding: 0;
        }

        .contact>ul>li {
            margin: 0;
            display: inline-block;
        }


        @media (max-width: 650px) {
            .email {
                padding: 0 20px;
            }

            .btn-link {
                padding: 6px;
                font-size: 11px;
            }

            .email>.body {
                padding: 10px 15px;
            }

            .email>.body>h2 {
                font-size: 14px;
                margin-top: 25px;
                text-align: center;
                margin-bottom: 40px;
            }

            .email>.body>p,
            .email>.body>i {
                font-size: 13px
            }

            .email>.body>.info {
                color: #888;
                font-size: 15px
            }

            .email>.body>.thanks {
                font-size: 11px;
            }

            .email>.footer {
                font-size: 13px;
            }

            .email>.footer>p:first-child {
                margin-bottom: 0;
            }

            .email>.footer>p>a {
                text-decoration: none;
                color: #fff;
            }

            .contact {
                font-size: 11px;
            }
        }

    </style>
</head>

<body>
    <div class="email">
        <div class="header">
            <img src="" alt="company logo" />
        </div>
        <div class="body">
            @yield('content')
        </div>
        <div class="footer">
            <p>Visit our site to learn more</p>
            <p><a href="{{ config('app.url') }}"> {{ config('app.name') }} </a></p>
        </div>
        <div class="contact">
            <p>Contact us at <a href="mailto:{{ config('app.email') }}"> {{ config('app.email') }} </a>.
            </p>
            <p>Copyright © {{ date('Y') }} - {{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
