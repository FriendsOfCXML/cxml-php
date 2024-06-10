<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfig;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new Config();

return $config
    ->setParallelConfig((new ParallelConfig()))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR1' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => false,
        'native_function_invocation' => [
            'include' => ['@internal'],
            'scope' => 'namespaced',
            'strict' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => false,
        'void_return' => true,
        'random_api_migration' => true,
        'pow_to_exponentiation' => true,
        'combine_nested_dirname' => true,
        'phpdoc_separation' => false,
        'phpdoc_align' => ['align' => 'left'],
        '@PHP82Migration' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'modernize_strpos' => true,
        'function_declaration' => ['closure_function_spacing' => 'one', 'closure_fn_spacing' => 'one'],
        'phpdoc_to_comment' => ['ignored_tags' => ['todo', 'var', 'property']],
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']],
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => ['space' => 'none'],
        'concat_space' => ['spacing' => 'one'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arguments', 'arrays', 'match', 'parameters']],
    ])
    ->setFinder($finder);
