<?php

return [
    [
        'name' => 'main',
        'pattern' => '{controller}/{action}/{*params}',
        'parameters' => ['default' => ['controller' => 'Home', 'action' => 'index']]
    ]
];