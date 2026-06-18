@props(['title', 'subtitle' => null])
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $title }}</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #1f2937;
                max-width: 600px;
                margin: 0 auto;
                padding: 0;
                background-color: #ffffff;
            }

            .email-container {
                background: white;
                margin: 20px auto;
                border-radius: 8px;
                border: 2px solid #181e05;
                overflow: hidden;
            }

            .header {
                background: #181e05;
                color: white;
                padding: 30px 40px;
                text-align: center;
                border-bottom: 2px solid #181e05;
            }

            .logo {
                width: 180px;
                height: auto;
                margin-bottom: 20px;
                filter: brightness(0) invert(1);
            }

            .header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 700;
            }

            .header p {
                margin: 10px 0 0 0;
                font-size: 16px;
            }

            .content {
                padding: 40px;
                background: #ffffff;
            }

            .greeting {
                font-size: 18px;
                color: #181e05;
                margin-bottom: 25px;
                font-weight: 500;
            }

            .form-section {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 25px;
                margin-bottom: 25px;
                border: 2px solid #181e05;
            }

            .form-section h3 {
                color: #181e05;
                margin-top: 0;
                margin-bottom: 20px;
                font-size: 20px;
                font-weight: 600;
            }

            .field {
                margin-bottom: 18px;
            }

            .field:last-child {
                margin-bottom: 0;
            }

            .field-label {
                font-weight: 600;
                color: #181e05;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .field-value {
                background: white;
                padding: 12px 16px;
                border-radius: 4px;
                border: 1px solid #181e05;
                font-size: 16px;
                color: #181e05;
                word-wrap: break-word;
            }

            .field-value.long-text {
                white-space: pre-wrap;
                line-height: 1.6;
            }

            .info-box {
                background: #f2f3ed;
                border-radius: 8px;
                padding: 18px 22px;
                margin-bottom: 25px;
                border: 1px solid #181e05;
                font-size: 15px;
                color: #181e05;
            }

            .consent-section {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 8px;
                margin-top: 30px;
                border: 2px solid #181e05;
            }

            .consent-section h3 {
                color: #181e05;
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 18px;
                font-weight: 600;
                display: flex;
                align-items: center;
            }

            .consent-text {
                font-size: 14px;
                color: #181e05;
                line-height: 1.6;
            }

            .checkmark {
                color: #181e05;
                font-weight: bold;
                font-size: 18px;
                margin-right: 8px;
            }

            .meta-info {
                display: flex;
                justify-content: space-between;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #181e05;
                font-size: 13px;
            }

            .meta-info span {
                background: white;
                padding: 6px 12px;
                border-radius: 4px;
                border: 1px solid #181e05;
            }

            .footer {
                background: #f8f9fa;
                padding: 30px 40px;
                text-align: center;
                border-top: 2px solid #181e05;
            }

            .footer p {
                margin: 0 0 10px 0;
                font-size: 14px;
                color: #181e05;
            }

            .footer p:last-child {
                margin-bottom: 0;
            }

            .action-buttons {
                margin-top: 25px;
                margin-bottom: 5px;
                text-align: center;
            }

            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: #181e05;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: 500;
                margin: 0 10px;
                border: 2px solid #181e05;
            }

            .btn:hover {
                background: #2c350a;
            }

            .btn-secondary {
                background: white;
                color: #181e05;
                border: 2px solid #181e05;
            }

            .btn-secondary:hover {
                background: #f2f3ed;
            }

            @media (max-width: 600px) {
                .email-container {
                    margin: 10px;
                    border-radius: 4px;
                }

                .header {
                    padding: 20px;
                }

                .content {
                    padding: 20px;
                }

                .footer {
                    padding: 20px;
                }

                .logo {
                    width: 140px;
                }

                .header h1 {
                    font-size: 24px;
                }

                .meta-info {
                    flex-direction: column;
                    gap: 10px;
                }

                .btn {
                    display: block;
                    margin: 0 0 12px 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <img src="{{ asset('images/logo.png') }}" alt="Siguldas Skati Logo" class="logo" />
                <h1>{{ $title }}</h1>
                @if ($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
            </div>

            <div class="content">
                {{ $slot }}
            </div>

            <div class="footer">
                @isset($footer)
                    {{ $footer }}
                @else
                    <p>{{ config('app.name') }}</p>
                @endisset
            </div>
        </div>
    </body>
</html>
