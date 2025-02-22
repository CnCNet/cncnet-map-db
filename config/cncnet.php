<?php
return [
    'games' => [
        'td',
        'ra',
        'ts',
        'dta',
        'yr',
        'd2',
    ],

    'map_verifiers' => [
        'td' => \App\Extensions\Services\Maps\MapHandler\TDMapVerifier::class,
        'ra' => \App\Extensions\Services\Maps\MapHandler\RedAlertMapVerifier::class,
        'ts' => \App\Extensions\Services\Maps\MapHandler\TiberiumSunMapVerifier::class,
        'dta' => \App\Extensions\Services\Maps\MapHandler\DawnOfTheTiberiumAgeMapVerifier::class,
        'yr' => \App\Extensions\Services\Maps\MapHandler\YuriMapHandler::class,
        'd2' => \App\Extensions\Services\Maps\MapHandler\Dune2000MapVerifier::class,
    ]
];