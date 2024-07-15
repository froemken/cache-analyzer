<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Caching Framework Analyzer',
    'description' => 'Analyze data sent to TYPO3 Caching Framework before it is written',
    'category' => 'service',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'stable',
    'author_company' => '',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
