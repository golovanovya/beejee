<?php

/**
 * Rules for validation middleware
 */

return [
    'login' => [
        'login' => [
            new \Symfony\Component\Validator\Constraints\NotBlank([]),
        ],
        'password' => [
            new \Symfony\Component\Validator\Constraints\NotBlank([]),
        ],
    ],
    'job' => [
        'name' => [
            new \Symfony\Component\Validator\Constraints\Length([
                'max' => 50,
                'minMessage' => 'Значение не может быть меньше, чем {{ limit }} символов',
                'maxMessage' => 'Значение не может быть больше, чем {{ limit }} символов',
                ]),
            new \Symfony\Component\Validator\Constraints\NotBlank([
                'message' => 'Поле не может быть пустым.',
                ]),
        ],
        'email' => [
            new Symfony\Component\Validator\Constraints\Email([
                'message' => 'Email "{{ value }}" не соответствует шаблону.',
                ]),
            new \Symfony\Component\Validator\Constraints\NotBlank([
                'message' => 'Поле не может быть пустым.',
                ]),
        ],
        'status' => [
            new \Symfony\Component\Validator\Constraints\Choice([
                '0', '1', 0, 1, true, false,
            ])
        ],
        'content' => [
            new \Symfony\Component\Validator\Constraints\Length([
                'max' => 255,
                'minMessage' => 'Значение не может быть меньше, чем {{ limit }} символов',
                'maxMessage' => 'Значение не может быть больше, чем {{ limit }} символов',
                ]),
            new \Symfony\Component\Validator\Constraints\NotBlank([
                'message' => 'Поле не может быть пустым.',
                ]),
        ],
    ]
];
