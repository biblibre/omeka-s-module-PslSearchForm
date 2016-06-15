<?php
return [
    'form_elements' => [
        'factories' => [
            'PslSearchForm\Form\PslForm' => 'PslSearchForm\Service\Form\PslFormFactory',
            'PslSearchForm\Form\PslFormConfigFieldset' => 'PslSearchForm\Service\Form\PslFormConfigFieldsetFactory',
        ],
    ],
    'search' => [
        'form_adapters' => [
            'psl' => 'PslSearchForm\FormAdapter\PslFormAdapter',
        ],
    ]
];
