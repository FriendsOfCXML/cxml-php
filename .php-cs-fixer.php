<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;

$finder = Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

$config = new Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR1' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@Symfony:risky' => false,
        'native_function_invocation' => [
            'include' => [NativeFunctionInvocationFixer::SET_INTERNAL],
            'scope' => 'namespaced',
            'strict' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => false,
        'void_return' => true,
        'random_api_migration' => true,
        'pow_to_exponentiation' => true,
        'combine_nested_dirname' => true,
        'phpdoc_separation' => false,
        '@PHP82Migration' => true,
        'global_namespace_import' => [
            'import_classes' => false,
        ],
    ])
    ->setFinder($finder)
;
