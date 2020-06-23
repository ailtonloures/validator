 <h1 align="center"> Validator </h1>
 
<span align="center">  
    
[![Code Quality](https://www.code-inspector.com/project/10166/score/svg)](https://frontend.code-inspector.com/public/project/10166/validator/dashboard)

</span>

Projeto livre, com objetivo de facilitar a validação de formulários, requisições http, e tudo o que for necessário.

## Requisitos

- PHP : ^7.2

## Instalação

Com o [composer](https://getcomposer.org/) instalado, execute o comando abaixo:

```
composer require ailtonloures/validator
```

ou no arquivo **composer.json** do seu projeto

```
"require": {
    "ailtonloures/validator": "^1.*",
}
```

## Exemplo de Uso

### Tipos de validação disponíveis

- required
- email
- numeric
- min:{int}
- max:{int}

### Uso padrão:

```[php]
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    'name' => 'required|min:3',
    'age' => 'numeric|required|max:100',
    'email' => 'email'
];

$validator = Validator::make($target, $rules);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

### Personalizando as mensagens de erro:

```[php]
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "name" => "required",
];

$messages = [
    "name" => [
        "required" => "Campo obrigatório"
    ]
];


$validator = Validator::make($target, $rules, $messages);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

**Retorno, caso não seja válido**

```
"validation": 
    {
        "name": "Campo obrigatório",
    }
```

### Encadeamento, quando for necessário validar mais de um alvo e Apelidar, quando validar mais de um alvo, pode também apelidar o conjunto a ser validado para identificação:

```[php]
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$secondTarget = [
    'email' => 'teste@example.com'
];

$rules  = [
    "name" => "required",
];

$secondRules = [
    'email' => 'email'
];

$messages = [
    "name" => [
        "required" => "Campo obrigatório"
    ]
];


$validator = Validator::make($target, $rules, $messages, 'first-target')->make($secondTarget, $secondeRules, null, 'second-target');

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

**Retorno, caso não seja válido**

```
"validation": 
    {
        "first-target": 
        {
            "name": "Campo obrigatório",
        },
        "second-target": 
        {
            "email": "Invalid e-mail."
        }
    }
```
