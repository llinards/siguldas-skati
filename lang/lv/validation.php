<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'               => ':attribute lauks ir jāapstiprina.',
    'accepted_if'            => ':attribute lauks ir jāapstiprina, kad :other ir :value.',
    'active_url'             => ':attribute laukā ir jābūt derīgai URL adresei.',
    'after'                  => ':attribute laukā ir jābūt datumam pēc :date.',
    'after_or_equal'         => ':attribute laukā ir jābūt datumam pēc vai vienādam ar :date.',
    'alpha'                  => ':attribute laukā drīkst būt tikai burti.',
    'alpha_dash'             => ':attribute laukā drīkst būt tikai burti, cipari, domuzīmes un pasvītrojumi.',
    'alpha_num'              => ':attribute laukā drīkst būt tikai burti un cipari.',
    'any_of'                 => ':attribute lauks nav derīgs.',
    'array'                  => ':attribute laukam ir jābūt masīvam.',
    'ascii'                  => ':attribute laukā drīkst būt tikai viena baita alfabētiski cipariski simboli un zīmes.',
    'before'                 => ':attribute laukā ir jābūt datumam pirms :date.',
    'before_or_equal'        => ':attribute laukā ir jābūt datumam pirms vai vienādam ar :date.',
    'between'                => [
        'array'   => ':attribute laukam ir jābūt no :min līdz :max elementiem.',
        'file'    => ':attribute laukam ir jābūt no :min līdz :max kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt no :min līdz :max.',
        'string'  => ':attribute laukam ir jābūt no :min līdz :max simboliem.',
    ],
    'boolean'                => ':attribute laukam ir jābūt patiesam vai nepatiesam.',
    'can'                    => ':attribute laukā ir neatļauta vērtība.',
    'confirmed'              => ':attribute lauka apstiprinājums nesakrīt.',
    'contains'               => ':attribute laukā trūkst nepieciešamās vērtības.',
    'current_password'       => 'Parole nav pareiza.',
    'date'                   => ':attribute laukā ir jābūt derīgam datumam.',
    'date_equals'            => ':attribute laukā ir jābūt datumam, kas vienāds ar :date.',
    'date_format'            => ':attribute laukam ir jāatbilst formātam :format.',
    'decimal'                => ':attribute laukam ir jābūt :decimal decimālvietām.',
    'declined'               => ':attribute lauks ir jānoraida.',
    'declined_if'            => ':attribute lauks ir jānoraida, kad :other ir :value.',
    'different'              => ':attribute lauks un :other ir jābūt atšķirīgiem.',
    'digits'                 => ':attribute laukam ir jābūt :digits cipariem.',
    'digits_between'         => ':attribute laukam ir jābūt no :min līdz :max cipariem.',
    'dimensions'             => ':attribute laukam ir nederīgi attēla izmēri.',
    'distinct'               => ':attribute laukam ir dubulta vērtība.',
    'doesnt_end_with'        => ':attribute lauks nedrīkst beigties ar kādu no šīm vērtībām: :values.',
    'doesnt_start_with'      => ':attribute lauks nedrīkst sākties ar kādu no šīm vērtībām: :values.',
    'email'                  => ':attribute laukā ir jābūt derīgai e-pasta adresei.',
    'ends_with'              => ':attribute laukam ir jābeidzas ar kādu no šīm vērtībām: :values.',
    'enum'                   => 'Izvēlētais :attribute nav derīgs.',
    'exists'                 => 'Izvēlētais :attribute nav derīgs.',
    'extensions'             => ':attribute laukam ir jābūt vienam no šiem paplašinājumiem: :values.',
    'file'                   => ':attribute laukam ir jābūt failam.',
    'filled'                 => ':attribute laukam ir jābūt vērtībai.',
    'gt'                     => [
        'array'   => ':attribute laukam ir jābūt vairāk nekā :value elementiem.',
        'file'    => ':attribute laukam ir jābūt lielākam par :value kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt lielākam par :value.',
        'string'  => ':attribute laukam ir jābūt vairāk nekā :value simboliem.',
    ],
    'gte'                    => [
        'array'   => ':attribute laukam ir jābūt :value vai vairāk elementiem.',
        'file'    => ':attribute laukam ir jābūt lielākam vai vienādam ar :value kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt lielākam vai vienādam ar :value.',
        'string'  => ':attribute laukam ir jābūt lielākam vai vienādam ar :value simboliem.',
    ],
    'hex_color'              => ':attribute laukā ir jābūt derīgai heksadecimālai krāsai.',
    'image'                  => ':attribute laukam ir jābūt attēlam.',
    'in'                     => 'Izvēlētais :attribute nav derīgs.',
    'in_array'               => ':attribute laukam ir jāeksistē :other.',
    'integer'                => ':attribute laukam ir jābūt veselam skaitlim.',
    'ip'                     => ':attribute laukā ir jābūt derīgai IP adresei.',
    'ipv4'                   => ':attribute laukā ir jābūt derīgai IPv4 adresei.',
    'ipv6'                   => ':attribute laukā ir jābūt derīgai IPv6 adresei.',
    'json'                   => ':attribute laukā ir jābūt derīgai JSON virknei.',
    'list'                   => ':attribute laukam ir jābūt sarakstam.',
    'lowercase'              => ':attribute laukam ir jābūt ar mazajiem burtiem.',
    'lt'                     => [
        'array'   => ':attribute laukam ir jābūt mazāk nekā :value elementiem.',
        'file'    => ':attribute laukam ir jābūt mazākam par :value kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt mazākam par :value.',
        'string'  => ':attribute laukam ir jābūt mazāk nekā :value simboliem.',
    ],
    'lte'                    => [
        'array'   => ':attribute laukam nedrīkst būt vairāk par :value elementiem.',
        'file'    => ':attribute laukam ir jābūt mazākam vai vienādam ar :value kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt mazākam vai vienādam ar :value.',
        'string'  => ':attribute laukam ir jābūt mazākam vai vienādam ar :value simboliem.',
    ],
    'mac_address'            => ':attribute laukā ir jābūt derīgai MAC adresei.',
    'max'                    => [
        'array'   => ':attribute laukam nedrīkst būt vairāk par :max elementiem.',
        'file'    => ':attribute laukam nedrīkst būt lielākam par :max kilobaitiem.',
        'numeric' => ':attribute laukam nedrīkst būt lielākam par :max.',
        'string'  => ':attribute laukam nedrīkst būt vairāk par :max simboliem.',
    ],
    'max_digits'             => ':attribute laukam nedrīkst būt vairāk par :max cipariem.',
    'mimes'                  => ':attribute laukam ir jābūt faila tipam: :values.',
    'mimetypes'              => ':attribute laukam ir jābūt faila tipam: :values.',
    'min'                    => [
        'array'   => ':attribute laukam ir jābūt vismaz :min elementiem.',
        'file'    => ':attribute laukam ir jābūt vismaz :min kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt vismaz :min.',
        'string'  => ':attribute laukam ir jābūt vismaz :min simboliem.',
    ],
    'min_digits'             => ':attribute laukam ir jābūt vismaz :min cipariem.',
    'missing'                => ':attribute laukam ir jātrūkst.',
    'missing_if'             => ':attribute laukam ir jātrūkst, kad :other ir :value.',
    'missing_unless'         => ':attribute laukam ir jātrūkst, ja vien :other nav :value.',
    'missing_with'           => ':attribute laukam ir jātrūkst, kad :values ir klāt.',
    'missing_with_all'       => ':attribute laukam ir jātrūkst, kad :values ir klāt.',
    'multiple_of'            => ':attribute laukam ir jābūt :value daudzkārtnim.',
    'not_in'                 => 'Izvēlētais :attribute nav derīgs.',
    'not_regex'              => ':attribute lauka formāts nav derīgs.',
    'numeric'                => ':attribute laukam ir jābūt skaitlim.',
    'password'               => [
        'letters'       => ':attribute laukā ir jābūt vismaz vienam burtam.',
        'mixed'         => ':attribute laukā ir jābūt vismaz vienam lielajam un vienam mazajam burtam.',
        'numbers'       => ':attribute laukā ir jābūt vismaz vienam ciparam.',
        'symbols'       => ':attribute laukā ir jābūt vismaz vienam simbolam.',
        'uncompromised' => 'Norādītā :attribute ir parādījusies datu noplūdē. Lūdzu, izvēlieties citu :attribute.',
    ],
    'present'                => ':attribute laukam ir jābūt klāt.',
    'present_if'             => ':attribute laukam ir jābūt klāt, kad :other ir :value.',
    'present_unless'         => ':attribute laukam ir jābūt klāt, ja vien :other nav :value.',
    'present_with'           => ':attribute laukam ir jābūt klāt, kad :values ir klāt.',
    'present_with_all'       => ':attribute laukam ir jābūt klāt, kad :values ir klāt.',
    'prohibited'             => ':attribute lauks ir aizliegts.',
    'prohibited_if'          => ':attribute lauks ir aizliegts, kad :other ir :value.',
    'prohibited_if_accepted' => ':attribute lauks ir aizliegts, kad :other ir apstiprināts.',
    'prohibited_if_declined' => ':attribute lauks ir aizliegts, kad :other ir noraidīts.',
    'prohibited_unless'      => ':attribute lauks ir aizliegts, ja vien :other nav :values.',
    'prohibits'              => ':attribute lauks aizliedz :other klātbūtni.',
    'regex'                  => ':attribute lauka formāts nav derīgs.',
    'required'               => 'Šis lauks ir obligāts.',
    'required_array_keys'    => 'Šis laukā ir jābūt ierakstiem: :values.',
    'required_if'            => 'Šis lauks ir obligāts, kad :other ir :value.',
    'required_if_accepted'   => 'Šis lauks ir obligāts, kad :other ir apstiprināts.',
    'required_if_declined'   => 'Šis lauks ir obligāts, kad :other ir noraidīts.',
    'required_unless'        => 'Šis lauks ir obligāts, ja vien :other nav :values.',
    'required_with'          => 'Šis lauks ir obligāts, kad :values ir klāt.',
    'required_with_all'      => 'Šis lauks ir obligāts, kad :values ir klāt.',
    'required_without'       => 'Šis lauks ir obligāts, kad :values nav klāt.',
    'required_without_all'   => 'Šis lauks ir obligāts, kad neviens no :values nav klāt.',
    'same'                   => ':attribute laukam ir jāsakrīt ar :other.',
    'size'                   => [
        'array'   => ':attribute laukā ir jābūt :size elementiem.',
        'file'    => ':attribute laukam ir jābūt :size kilobaitiem.',
        'numeric' => ':attribute laukam ir jābūt :size.',
        'string'  => ':attribute laukam ir jābūt :size simboliem.',
    ],
    'starts_with'            => ':attribute laukam ir jāsākas ar kādu no šīm vērtībām: :values.',
    'string'                 => ':attribute laukam ir jābūt virknei.',
    'timezone'               => ':attribute laukā ir jābūt derīgai laika joslai.',
    'unique'                 => ':attribute jau ir aizņemts.',
    'uploaded'               => ':attribute neizdevās augšupielādēt.',
    'uppercase'              => ':attribute laukam ir jābūt ar lielajiem burtiem.',
    'url'                    => ':attribute laukā ir jābūt derīgai URL adresei.',
    'ulid'                   => ':attribute laukā ir jābūt derīgam ULID.',
    'uuid'                   => ':attribute laukā ir jābūt derīgam UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'pielāgots-ziņojums',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
