<h1 align="center"> Validator </h1>
 
<span align="center">  
    
[![Code Quality](https://www.code-inspector.com/project/10166/score/svg)](https://frontend.code-inspector.com/public/project/10166/validator/dashboard)

</span>

Um projeto livre para a comunidade e com objetivo principal de facilitar no seu dia a dia de desenvolvimento com a validaçãode formulários, requisições http, e tudo mais.

## Requisitos

- PHP : ^7.2

## Instalação

Com o [composer](https://getcomposer.org/) instalado, execute o comando abaixo:

```bash
composer require ailtonloures/validator
```

## Opções de validação

- required
- email
- url
- numeric
- min
- max
- max_number
- min_number
- equal_to
- callback
- date

## Uso

```php
<?php

use Validator\Validator;

require __DIR__ . '/../vendor/autoload.php';

$validator = Validator::make(
    [
        'email' => 'jonhdoe@gmail.com',
        'name'  => 'Jonh Doe',
        'site'  => 'myportfolio.com',
        'age'   => '17',
        'birth' => '2003-01-01',
    ],
    [
        'email' => 'email',
        'name'  => 'required',
        'site'  => 'url',
        'age'   => ['required', 'numeric:gte=18', 'max_number:100'],
        'birth' => 'date:lte=2002-12-31',
    ],
    [
        'required.*' => 'Campo obrigatório',
        'age'        => [
            'numeric'    => [
                'default' => 'A :attr está com um valor inválido',
                'gte'     => 'A :attr deve ser maior que :gte.',
            ],
            'max_number' => 'A :attr máxima permitida é de :max_number anos',
        ],
        'url.*'      => 'URL inválida',
        'email.*'    => 'E-mail inválido',
        'date.lte'   => 'A :attr deve ser menor que :date|format:d/m/Y.',
    ],
    [
        'birth' => 'data de nascimento',
        'age'   => 'idade',
    ]
);

if ($validator->invalid()) {
    echo $validator->fails(true);
}
```

## Licença

MIT