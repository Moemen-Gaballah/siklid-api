<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        // > PHPUnit
        'php_unit_method_casing' => ['case' => 'snake_case'],
        // > Strict
        'declare_strict_types' => true,
        // > Operator
        'not_operator_with_successor_space' => true,
        // > Cast Notation
        'cast_spaces' => ['space' => 'none'],
        // > Import
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
    ])->setFinder($finder);
