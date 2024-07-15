<?php

return [
    'ctrl' => [
        'title' => 'Cache Expressions',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'adminOnly' => true,
        'rootLevel' => 1,
        'groupName' => 'cache_analyzer',
        'versioningWS' => false,
        'typeicon_classes' => [
            'default' => 'cache-analyzer-table-expression',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'title,
                --palette--;Exception Handling;exception,
                --palette--;Define Expression;expression,
                cache_configurations',
        ],
    ],
    'palettes' => [
        'exception' => [
            'showitem' => 'throw_exception, throw_exception_fe_only',
        ],
        'expression' => [
            'showitem' => 'expression, is_regexp',
        ],
    ],
    'columns' => [
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
            ],
        ],
        'is_regexp' => [
            'label' => 'Is regular expression?',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'throw_exception' => [
            'label' => 'Throw exception?',
            'description' => 'Deactivated: just protocol the match. ' .
                'Activated: protocol the match and throws exception to prevent inserting invalid cache entries. No further caches will be analyzed.',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'throw_exception_fe_only' => [
            'label' => 'Throw exception in frontend only?',
            'description' => 'If "throw exception" is activated, it may also throw exceptions in backend, because of page previews. Activate to throw exception in frontend only.',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
            ],
        ],
        'expression' => [
            'label' => 'Expression',
            'description' => 'For non regular expressions it searches with PHP:strpos. For regular expressions it uses PHP:preg_match. No need to add or escape any delimiter. It uses "/" internally.',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
            ],
        ],
        'cache_configurations' => [
            'label' => 'Cache ConfigurationsBackends',
            'description' => 'Choose the Cache Configurations you want to analyze. If you are unsure add all cache configurations.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 10,
                'minitems' => 1,
                'itemsProcFunc' => \StefanFroemken\CacheAnalyzer\UserFunc\RegisteredCacheConfigurationsUserFunc::class . '->getCacheConfigurations',
            ],
        ],
    ],
];
