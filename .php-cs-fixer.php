<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR1' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@Symfony:risky' => false,
        'native_function_invocation' => [
            'include' => [\PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::SET_INTERNAL],
            'scope' => 'namespaced',
            'strict' => false,
        ],
        'void_return' => true,
        'random_api_migration' => true,
        'pow_to_exponentiation' => true,
        'combine_nested_dirname' => true,
        'phpdoc_separation' => false,
        '@PHP74Migration' => true,
        'global_namespace_import' => [
            'import_classes' => false,
        ],
    ])
    ->setFinder($finder)
;
