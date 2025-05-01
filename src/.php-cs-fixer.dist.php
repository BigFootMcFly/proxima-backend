<?php


use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
        'new_with_parentheses' => [
            'anonymous_class' => false,
        ],
        'braces_position' => [
            'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'function_declaration' => [
            'closure_fn_spacing' => 'one',
            'closure_function_spacing' => 'one',
        ],
        'concat_space' => [
            'spacing' => 'none',
        ],
        'single_trait_insert_per_statement' => false,
        'no_blank_lines_after_class_opening' => false,
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
            ->exclude([
                'storage/framework/views',
                'bootstrap/cache',
            ])
    )
;
