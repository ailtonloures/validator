<?php

use Validator\Validator;

require './vendor/autoload.php';

$request = $_REQUEST;

$validator = Validator::make($request,
    [
        'name'  => 'required|min:3',
        'email' => 'email',
        'soma'  => function ($value, $input) {
            $result = 4 + (int) $value;

            return $result === 8;
        },
    ],
    [
        'name'  => [
            'required' => 'Campo obrigatório',
            'min'      => 'Este campo deve ter no mínimo 3 caracteres',
        ],
        'email' => [
            'email' => 'E-mail ínvalido :attribute',
        ],
        'soma'  => [
            'callback_function' => 'O resultado não é ',
        ],
    ]
);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}
