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

## Tipos de validação:

- required
- email
- numeric
- min:{int}
- max:{int}

## Features da versão 1.1:

- validação personalizada por callback
- renomear o atributo com a palavra reservada :attr
- receber o valor do input na mensagem com a palavra reservada :value

## Exemplos:

### Uso padrão:

```php
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

Como terceiro parâmetro, o método **make** recebe as mensagems personalizadas. Para fazer-las, apenas crie um array passando pelo parâmetro em que a **chave** 
do array seja sempre o nome do input que está validando e abra um novo array pra esse input e agora as **chaves** serão o nome das validações que está fazendo. 
Como no exemplo, é o **required**.

```php
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

Retorno, caso não seja válido

```json
"validation": 
    {
        "name": "Campo obrigatório",
    }
```

### Encadeamento:

É possível encadear a validação quando necessário fazer em mais de um alvo, além disso é possível dar um nome para identificar o resultado de cada alvo validado no quinto parâmetro.

```php
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


$validator = Validator::make($target, $rules, $messages, null, 'first-target')->make($secondTarget, $secondeRules, null, null, 'second-target');

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

Retorno, caso não seja válido.

```json
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

### Personalizando validação:

Agora é possível criar a sua validação personalizada, através de callbacks onde a mesma recebe dois parametros o **$value** que é o valor do input e próprio **$input**, e retorno dessa callback **deverá ser sempre TRUE** caso não seja, ele dispara como não válido e ai você tem a resposta da sua validação. E obrigatóriamente você deverá personalizar uma mensagem para essa validação, em **$messages** ao ínves de passar o nome da validação, você pode chamar a palavra reservada **callback_function** e criar sua mensagem.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $input) {
        return $value >= 18;
    },
];

$messages = [
    "age" => [
        "callback_function" => "Não é maior de 18 anos"
    ]
];


$validator = Validator::make($target, $rules, $messages);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

Retorno, caso não seja válido.

```json
"validation": 
    {
        "age" : "Não é maior de 18 anos"
    }
```

### Novo nome para o atributo:

É possível também renomear os atributos passando no quarto parâmetro um array onde a chave é o **nome do input** e o valor é o novo nome que deseja dar. E para receber esse valor, você pode usar a palavra reservada **:attr**.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $input) {
        return $value >= 18;
    },
];

$messages = [
    "age" => [
        "callback_function" => "A :attr dessa pessoa não é maior que 18 anos"
    ]
];

$attributes = [
    'age' => 'idade'
];


$validator = Validator::make($target, $rules, $messages, $attributes);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

Retorno, caso não seja válido.

```json
"validation": 
    {
        "age" : "A idade dessa pessoa não é maior que 18 anos"
    }
```

Caso não deseje renomear, pode passar o :attr da mesma forma, mas ao invés disso ele receberia o **nome original** que nesse caso seria como no exemplo abaixo.

```json
"validation": 
    {
        "age" : "A age dessa pessoa não é maior que 18 anos"
    }
```

### Recebendo valor do input:

Você também pode receber o **valor** do input que está sendo validado e passar na mensagem usando a palavra reservada **:value**. 

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $input) {
        return $value >= 18;
    },
];

$messages = [
    "age" => [
        "callback_function" => ":value não é maior que 18"
    ]
];

$validator = Validator::make($target, $rules, $messages);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

Retorno, caso não seja válido.

```json
"validation": 
    {
        "age" : "17 não é maior que 18"
    }
```