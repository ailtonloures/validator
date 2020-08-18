 <h1 align="center"> Validator </h1>
 
<span align="center">  
    
[![Code Quality](https://www.code-inspector.com/project/10166/score/svg)](https://frontend.code-inspector.com/public/project/10166/validator/dashboard)

</span>

Projeto livre, com objetivo de facilitar a validação de formulários, requisições http, e tudo o que for necessário.

## Requisitos

- PHP : ^7.2

## Instalação

Com o [composer](https://getcomposer.org/) instalado, execute o comando abaixo:

```bash
composer require ailtonloures/validator
```

## Tipos de validação:

- required
- email
- numeric
- min:{int}
- max:{int}
- validação personalizada por funções anônimas

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
do array seja sempre o nome do atributo que está validando e abra um novo array pra esse atributo e agora as **chaves** serão o nome das validações que está fazendo. 
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
        "name": "Campo obrigatório"
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

Agora é possível criar a sua validação personalizada, através de uma função anônima onde a mesma é uma **callback** e recebe três parâmetros, o primeiro é **valor do atributo**, o segundo é o **nome do atributo** e o terceiro é todo o conteúdo do alvo válidado que por exemplo pode ser o corpo de uma requisição. O retorno dessa função **deverá ser sempre TRUE** caso não seja, ele dispara como não válido e ai você tem a resposta da sua validação. E obrigatóriamente você deverá personalizar uma mensagem para essa validação, em **$messages** ao ínves de passar o nome da validação, você pode chamar a palavra reservada **callback_function** e criar sua mensagem.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $attribute, $post) {
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

Pode também dar um nome para essa função anônima caso queira fazer mais de uma validação personalizada para um atributo ou outra já existente e obrigatóriamente para receber a mensagem dessa função nomeada, você deve passar o nome da função também em **$messages**.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => [
        'required',
        'not_adult' => function($value, $attribute, $post) {
            return $value >= 18;
        }
    ],
];

$messages = [
    "age" => [
        "not_adult" => "Não é maior de 18 anos",
        "required" => "Campo obrigatório"
    ]
];


$validator = Validator::make($target, $rules, $messages);

if (!$validator->valid()) {
    echo json_encode($validator->fails());
}

```

Retorno, caso não seja válido continuará sendo...

```json
"validation": 
    {
        "age" : "Não é maior de 18 anos"
    }
```

### Novo nome para o atributo:

É possível também renomear os atributos passando no quarto parâmetro um array onde a chave é o **nome do atributo** e o valor é o novo nome que deseja dar. E para receber esse valor, você pode usar a palavra reservada **:attr**.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $attribute, $post) {
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
        "age" :  "A age dessa pessoa não é maior que 18 anos"
    }
```

### Recebendo valor do atributo:

Você também pode receber o **valor** do atributo que está sendo validado e passar na mensagem usando a palavra reservada **:value**. 

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$target = $_POST;

$rules  = [
    "age" => function($value, $attribute, $post) {
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
        "age" :  "17 não é maior que 18"
    }
```

### Validação genérica:

Simplificando a validação de vários atributos com a mesma regra.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$validator = Validator::make(
    $_GET,
    [
        'name' => 'required|min:4',
        'age' => [
            'required',
            'not_adult' => function(int $value) {
                return $value >= 18;
            },
        ],
    ],
    [
        'age' => [
            'not_adult' => 'Deve ter mais do que 18 anos'
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
```

Retorno, caso não seja válido.

```json
"validation": {
    "name": "Campo nome é obrigatório",
    "age": "Campo idade é obrigatório"
  }
```

Outro exemplo.

```php
<?php

use Validator\Validator;

require './vendor/autoload.php';

$validator = Validator::make(
    $_GET,
    [
        'nome' => 'required|min:2',
        'sobrenome' => 'min:4',
    ],
    [
        '*.min' => "Este campo deve ter no mínimo :min caracteres",
    ],
);

if (!$validator->valid())
    echo json_encode($validator->fails());
```

Retorno, caso não seja válido.

```json
"validation": {
    "nome": "Este campo deve ter no mínimo 2 caracteres",
    "sobrenome": "Este campo deve ter no mínimo 4 caracteres"
  }
```