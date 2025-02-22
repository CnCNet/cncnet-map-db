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
        'td' => \App\Extensions\Services\Maps\MapHandler\TdMapHandler::class,
        'ra' => \App\Extensions\Services\Maps\MapHandler\RaMapHandler::class,
        'ts' => \App\Extensions\Services\Maps\MapHandler\TsMapHandler::class,
        'dta' => \App\Extensions\Services\Maps\MapHandler\DtaMapHandler::class,
        'yr' => \App\Extensions\Services\Maps\MapHandler\YuriMapHandler::class,
        'd2' => \App\Extensions\Services\Maps\MapHandler\D2MapHandler::class,
    ]
];