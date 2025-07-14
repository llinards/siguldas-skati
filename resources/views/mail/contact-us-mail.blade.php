<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontaktu ziņojums</title>
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
            border: 2px solid #000000;
            overflow: hidden;
        }

        .header {
            background: #000000;
            color: white;
            padding: 30px 40px;
            text-align: center;
            border-bottom: 2px solid #000000;
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
            color: #000000;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid #000000;
        }

        .form-section h3 {
            color: #000000;
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
            color: #000000;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .field-value {
            background: white;
            padding: 12px 16px;
            border-radius: 4px;
            border: 1px solid #000000;
            font-size: 16px;
            color: #000000;
            word-wrap: break-word;
        }

        .field-value.long-text {
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .consent-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            border: 2px solid #000000;
        }

        .consent-section h3 {
            color: #000000;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .consent-text {
            font-size: 14px;
            color: #000000;
            line-height: 1.6;
        }

        .checkmark {
            color: #000000;
            font-weight: bold;
            font-size: 18px;
            margin-right: 8px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #000000;
            font-size: 13px;
        }

        .meta-info span {
            background: white;
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #000000;
        }

        .footer {
            background: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 2px solid #000000;
        }

        .footer p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #000000;
        }

        .footer p:last-child {
            margin-bottom: 0;
        }

        .action-buttons {
            margin-top: 25px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #000000;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin: 0 10px;
            border: 2px solid #000000;
        }

        .btn:hover {
            background: #333333;
        }

        .btn-secondary {
            background: white;
            color: #000000;
            border: 2px solid #000000;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
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
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Siguldas Skati Logo" class="logo">
        <h1>Jauns kontaktu ziņojums</h1>
        <p>Saņemts: {{ now()->format('d.m.Y H:i') }}</p>
    </div>

    <div class="content">
        <div class="greeting">
            Sveiki! Jūs esat saņēmuši jaunu ziņojumu no Siguldas Skati mājas lapas kontaktu formas.
        </div>

        <div class="form-section">
            <h3>Klienta informācija</h3>

            <div class="field">
                <div class="field-label">Vārds</div>
                <div class="field-value">{{ $firstName }}</div>
            </div>

            <div class="field">
                <div class="field-label">Uzvārds</div>
                <div class="field-value">{{ $lastName }}</div>
            </div>

            <div class="field">
                <div class="field-label">Telefona numurs</div>
                <div class="field-value">{{ $phoneNumber }}</div>
            </div>

            <div class="field">
                <div class="field-label">E-pasta adrese</div>
                <div class="field-value">{{ $email }}</div>
            </div>

            @if($question)
                <div class="field">
                    <div class="field-label">Jautājums / Ziņojums</div>
                    <div class="field-value long-text">{{ $question }}</div>
                </div>
            @endif
        </div>

        <div class="action-buttons">
            <a href="mailto:{{ $email }}" class="btn">Atbildēt klientam</a>
            <a href="tel:{{ $phoneNumber }}" class="btn btn-secondary">Zvanīt klientam</a>
        </div>

        <div class="consent-section">
            <h3>
                <span class="checkmark">✓</span>
                Datu apstrādes piekrišana
            </h3>
            <div class="consent-text">
                Klients ir piekritis savu personisko datu apstrādei saskaņā ar uzņēmuma privātuma politiku un GDPR
                prasībām.

                <div class="meta-info">
                    <span><strong>Piekrišanas laiks:</strong> {{ now()->format('d.m.Y H:i:s') }}</span>
                    <span><strong>IP adrese:</strong> {{ $ipAddress }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Šis ziņojums ir automātiski ģenerēts no mājas lapas kontaktu formas.</p>
    </div>
</div>
</body>
</html>
