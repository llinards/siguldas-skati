<?php

return [
    'title' => 'Mēs izmantojam sīkdatnes',
    'intro' => 'Šī vietne izmanto sīkdatnes, lai uzlabotu lietotāja pieredzi.',
    'link'  => 'Skatiet mūsu <a href=":url">Sīkdatņu politiku</a>, lai iegūtu vairāk informācijas.',

    'essentials' => 'Tikai nepieciešamās',
    'all'        => 'Pieņemt visas',
    'customize'  => 'Pielāgot',
    'manage'     => 'Pārvaldīt sīkdatnes',
    'details'    => [
        'more' => 'Vairāk informācijas',
        'less' => 'Mazāk informācijas',
    ],
    'save'       => 'Saglabāt iestatījumus',
    'cookie'     => 'Sīkdatne',
    'purpose'    => 'Mērķis',
    'duration'   => 'Ilgums',
    'year'       => 'Gads|Gadi',
    'day'        => 'Diena|Dienas',
    'hour'       => 'Stunda|Stundas',
    'minute'     => 'Minūte|Minūtes',

    'categories' => [
        'essentials' => [
            'title'       => 'Nepieciešamās sīkdatnes',
            'description' => 'Ir dažas sīkdatnes, kas mums ir jāiekļauj, lai noteiktas tīmekļa lapas varētu darboties. Šī iemesla dēļ tām nav nepieciešama jūsu piekrišana.',
        ],
        'analytics'  => [
            'title'       => 'Analītikas sīkdatnes',
            'description' => 'Mēs tās izmantojam iekšējiem pētījumiem par to, kā uzlabot pakalpojumu, ko sniedzam visiem mūsu lietotājiem. Šīs sīkdatnes novērtē, kā jūs mijiedarbojaties ar mūsu vietni.',
        ],
        'optional'   => [
            'title'       => 'Izvēles sīkdatnes',
            'description' => 'Šīs sīkdatnes iespējo funkcijas, kas varētu uzlabot jūsu lietotāja pieredzi, bet to trūkums neietekmēs jūsu spēju pārlūkot mūsu vietni.',
        ],
    ],

    'defaults' => [
        'consent' => 'Izmanto, lai saglabātu lietotāja sīkdatņu piekrišanas preferences.',
        'session' => 'Izmanto, lai identificētu lietotāja pārlūkošanas sesiju.',
        'csrf'    => 'Izmanto, lai aizsargātu gan lietotāju, gan mūsu vietni pret starpvietņu viltus pieprasījumu uzbrukumiem.',
        '_ga'     => 'Galvenā sīkdatne, ko izmanto Google Analytics, ļauj pakalpojumam atšķirt vienu apmeklētāju no otra.',
        '_ga_ID'  => 'Izmanto Google Analytics, lai saglabātu sesijas stāvokli.',
        '_gid'    => 'Izmanto Google Analytics, lai identificētu lietotāju.',
        '_gat'    => 'Izmanto Google Analytics, lai ierobežotu pieprasījumu biežumu.',
    ],
];
