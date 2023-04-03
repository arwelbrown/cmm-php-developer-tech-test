<?php

return [
    '/form' => [
        [
            'type'      => 'GET',
            'handler'   => 'FormController@index',
        ],
    ],
    '/' => [
        [
            'type'      => 'GET',
            'handler'   => 'FormController@index'
        ]
    ],
    '/results' => [
        [
            'type'      => 'POST',
            'handler'   => 'FormController@submit'
        ]
    ]
];
