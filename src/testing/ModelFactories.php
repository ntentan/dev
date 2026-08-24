<?php

namespace ntentan\dev\testing;

use ntentan\mvc\MvcModelFactory;
use ntentan\nibii\factories\DefaultModelFactory;

enum ModelFactories: string
{
    case DEFAULT = DefaultModelFactory::class;
    case MVC = MockMvcModelFactory::class;
}
