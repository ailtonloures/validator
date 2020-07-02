<?php

use Validator\Validator;

require __DIR__ . '/vendor/autoload.php';

$validator = Validator::make(
    [
        'name' => "",
        'age' => '17'
    ],
    [
        'name' => 'required|min:4',
        'age' => [
            'required',
            'not_adult' => function(int $value) {
                return $value >= 18;
            }
        ]
    ],
    [
        'name' => [
            'required' => 'Nome é obrigatório',
            'min' => 'Deve ter mais do que 4 caracteres'
        ],
        'age' => [
            'required' => 'Idade é obrigatória',
            'not_adult' => 'Deve ter mais do que 18 anos'
        ]
    ]
);

if (!$validator->valid())
    echo json_encode($validator->fails());