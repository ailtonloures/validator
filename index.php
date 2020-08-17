<?php

use Validator\Validator;

require __DIR__ . '/vendor/autoload.php';

$validator = Validator::make(
    $_GET,
    [
        'name' => 'required|min:4',
        'age' => function(int $value) {
                return $value >= 18;
        },
    ],
    [
        'age' => [
            'callback_function' => 'Deve ter mais do que 18 anos'
        ],
        '*.required' => "Campo :attr é obrigatório",
    ],
    [
        'name' => 'nome',
        'age' => 'idade'
    ]
);

if (!$validator->valid())
    echo json_encode($validator->fails());