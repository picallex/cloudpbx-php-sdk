<?php

/*
 * This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
 * this repository contains the full copyright notices and license terms.
 */

// php-cs-fixer >= 3.9x ya no acepta ejecutarse sin archivo de configuracion,
// por eso declaramos explicitamente el ruleset que antes tomaba por defecto.

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);
