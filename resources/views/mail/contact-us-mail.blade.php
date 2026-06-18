<x-mail.layout title="Jauns kontaktu ziņojums" :subtitle="'Saņemts: '.now()->format('d.m.Y H:i')">
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

        @if ($question)
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
                <span>
                    <strong>Piekrišanas laiks:</strong>
                    {{ now()->format('d.m.Y H:i:s') }}
                </span>
                <span>
                    <strong>IP adrese:</strong>
                    {{ $ipAddress }}
                </span>
            </div>
        </div>
    </div>

    <x-slot:footer>
        <p>Šis ziņojums ir automātiski ģenerēts no mājas lapas kontaktu formas.</p>
    </x-slot:footer>
</x-mail.layout>
