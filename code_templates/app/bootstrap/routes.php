<?php

return [
    'default_pipeline' => [['mvc', []]],
    'routes' =>[
        [
            'name' => 'main',
            'pattern' => '{controller}/{action}/{*params}',
            'parameters' => ['default' => ['controller' => 'Home', 'action' => 'index']]
        ]
    ]
];