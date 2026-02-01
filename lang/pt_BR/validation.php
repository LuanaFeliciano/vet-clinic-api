<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensagens de Validação
    |--------------------------------------------------------------------------
    */

    'accepted' => 'O campo :attribute deve ser aceito.',
    'accepted_if' => 'O campo :attribute deve ser aceito quando :other for :value.',
    'active_url' => 'O campo :attribute deve ser uma URL válida.',
    'after' => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash' => 'O campo :attribute deve conter apenas letras, números, traços e sublinhados.',
    'alpha_num' => 'O campo :attribute deve conter apenas letras e números.',
    'any_of' => 'O campo :attribute é inválido.',
    'array' => 'O campo :attribute deve ser um array.',
    'ascii' => 'O campo :attribute deve conter apenas caracteres alfanuméricos de um byte.',
    'before' => 'O campo :attribute deve ser uma data anterior a :date.',
    'before_or_equal' => 'O campo :attribute deve ser uma data anterior ou igual a :date.',

    'between' => [
        'array' => 'O campo :attribute deve ter entre :min e :max itens.',
        'file' => 'O campo :attribute deve ter entre :min e :max kilobytes.',
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'string' => 'O campo :attribute deve ter entre :min e :max caracteres.',
    ],

    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'can' => 'O campo :attribute contém um valor não autorizado.',
    'confirmed' => 'A confirmação do campo :attribute não confere.',
    'contains' => 'O campo :attribute está ausente de um valor obrigatório.',
    'current_password' => 'A senha informada está incorreta.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format' => 'O campo :attribute deve seguir o formato :format.',
    'decimal' => 'O campo :attribute deve ter :decimal casas decimais.',
    'declined' => 'O campo :attribute deve ser recusado.',
    'declined_if' => 'O campo :attribute deve ser recusado quando :other for :value.',
    'different' => 'Os campos :attribute e :other devem ser diferentes.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute possui dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute possui um valor duplicado.',
    'doesnt_contain' => 'O campo :attribute não deve conter nenhum dos seguintes valores: :values.',
    'doesnt_end_with' => 'O campo :attribute não deve terminar com um dos seguintes valores: :values.',
    'doesnt_start_with' => 'O campo :attribute não deve iniciar com um dos seguintes valores: :values.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'encoding' => 'O campo :attribute deve estar codificado em :encoding.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes valores: :values.',
    'enum' => 'O valor selecionado para :attribute é inválido.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'extensions' => 'O campo :attribute deve possuir uma das seguintes extensões: :values.',
    'file' => 'O campo :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve possuir um valor.',
    'hex_color' => 'O campo :attribute deve ser uma cor hexadecimal válida.',
    'image' => 'O campo :attribute deve ser uma imagem.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'json' => 'O campo :attribute deve ser um JSON válido.',
    'lowercase' => 'O campo :attribute deve estar em letras minúsculas.',
    'mac_address' => 'O campo :attribute deve ser um endereço MAC válido.',

    'max' => [
        'array' => 'O campo :attribute não deve ter mais que :max itens.',
        'file' => 'O campo :attribute não deve ser maior que :max kilobytes.',
        'numeric' => 'O campo :attribute não deve ser maior que :max.',
        'string' => 'O campo :attribute não deve ter mais que :max caracteres.',
    ],

    'min' => [
        'array' => 'O campo :attribute deve ter no mínimo :min itens.',
        'file' => 'O campo :attribute deve ter no mínimo :min kilobytes.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],

    'numeric' => 'O campo :attribute deve ser numérico.',
    'password' => [
        'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
        'mixed' => 'O campo :attribute deve conter ao menos uma letra maiúscula e uma minúscula.',
        'numbers' => 'O campo :attribute deve conter pelo menos um número.',
        'symbols' => 'O campo :attribute deve conter pelo menos um símbolo.',
        'uncompromised' => 'A :attribute informada apareceu em um vazamento de dados.',
    ],

    'present' => 'O campo :attribute deve estar presente.',
    'prohibited' => 'O campo :attribute é proibido.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'same' => 'O campo :attribute deve ser igual a :other.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'O valor do campo :attribute já está em uso.',
    'url' => 'O campo :attribute deve ser uma URL válida.',
    'uuid' => 'O campo :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Atributos personalizados
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'e-mail',
        'password' => 'senha',
        'name' => 'nome',
    ],

];
