<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Run After Migrate
    |--------------------------------------------------------------------------
    |
    | Specifies whether to execute modeltyper after running database migrations.
    | This can be useful for keeping your models definition in sync with your
    | database schema.
    |
    | Requires output-file set to true
    */
    'run-after-migrate' => false,

    /*
    |--------------------------------------------------------------------------
    | Output TypeScript Definitions to a File
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output the TypeScript definitions to a file. If set
    | to true, the definitions will be saved to the specified file path.
    */
    'output-file' => false,

    /*
    |--------------------------------------------------------------------------
    | Path for Output TypeScript Definitions File
    |--------------------------------------------------------------------------
    |
    | Defines the file path where the TypeScript definitions will be saved.
    |
    | Requires output-file set to true
    */
    'output-file-path' => './resources/js/types/models.d.ts',

    /*
    |--------------------------------------------------------------------------
    | Generate TypeScript Interfaces in a Global Namespace
    |--------------------------------------------------------------------------
    |
    | Specifies whether to generate TypeScript interfaces within a global
    | namespace. This helps in organizing the interfaces and
    | avoiding naming conflicts.
    |
    | Uses config 'global-namespace' as Namespace
    */
    'global' => false,

    /*
    |--------------------------------------------------------------------------
    | Global Namespace Name for TypeScript Interfaces
    |--------------------------------------------------------------------------
    |
    | Defines the name of the global namespace where the TypeScript interfaces
    | will be generated. This helps in maintaining a clear structure for the
    | TypeScript codebase.
    |
    | Requires global set to true
    */
    'global-namespace' => 'models',

    /*
    |--------------------------------------------------------------------------
    | Output the Result in JSON Format
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output the TypeScript definitions in JSON format. This
    | can be useful for further processing or integration with other tools.
    */
    'json' => false,

    /*
    |--------------------------------------------------------------------------
    | Use TypeScript Enums Instead of Object Literals
    |--------------------------------------------------------------------------
    |
    | Determines whether to use TypeScript enums instead of object literals for
    | representing certain data structures. Enums provide a more type-safe and
    | expressive way to define sets of related constants.
    */
    'use-enums' => false,

    /*
    |--------------------------------------------------------------------------
    | Output Plural Form for Models
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output the plural form of model names. This can be
    | useful for consistency and clarity when dealing with collections of models.
    |
    | Uses Laravel Pluralizer
    */
    'plurals' => false,

    /*
    |--------------------------------------------------------------------------
    | Exclude Model Relationships
    |--------------------------------------------------------------------------
    |
    | Determines whether to exclude model relationships from the TypeScript
    | definitions. This can be useful if relationships are not needed in the
    | TypeScript codebase.
    */
    'no-relations' => false,

    /*
    |--------------------------------------------------------------------------
    | Make Model Relationships Optional
    |--------------------------------------------------------------------------
    |
    | Specifies whether to make model relationships optional in the TypeScript
    | definitions. This allows for more flexibility in handling models that may
    | or may not have related data.
    */
    'optional-relations' => false,

    /*
    |--------------------------------------------------------------------------
    | Exclude Hidden Model Attributes
    |--------------------------------------------------------------------------
    |
    | Determines whether to exclude hidden model attributes from the TypeScript
    | definitions. Hidden attributes are typically not needed in the client-side
    | code.
    */
    'no-hidden' => false,

    /*
    |--------------------------------------------------------------------------
    | Output Timestamps as Date Object Types
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output timestamps as Date object types. This allows
    | for more accurate and type-safe handling of date and time values in the
    | TypeScript code.
    */
    'timestamps-date' => false,

    /*
    |--------------------------------------------------------------------------
    | Make Nullable Attributes Optional
    |--------------------------------------------------------------------------
    |
    | Determines whether to make nullable attributes optional in the TypeScript
    | definitions. This provides better handling of attributes that may not have
    | a value.
    */
    'optional-nullables' => false,

    /*
    |--------------------------------------------------------------------------
    | Output api.MetApi Interfaces
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output TypeScript interfaces for api.MetApi resources.
    */
    'api-resources' => false,

    /*
    |--------------------------------------------------------------------------
    | Output Fillable Model Attributes
    |--------------------------------------------------------------------------
    |
    | Specifies whether to output fillable model attributes in the TypeScript
    | definitions. Fillable attributes are those that can be mass-assigned.
    */
    'fillables' => false,

    /*
    |--------------------------------------------------------------------------
    | Suffix for Fillable Model Attributes
    |--------------------------------------------------------------------------
    |
    | Defines a suffix to be added to fillable model attributes in the TypeScript
    | definitions. This can help in distinguishing fillable attributes from
    | other attributes.
    |
    | Requires fillables set to true
    */
    'fillable-suffix' => 'fillable',

    /*
    |--------------------------------------------------------------------------
    | Override or Add New Type Mappings
    |--------------------------------------------------------------------------
    |
    | Custom mappings allow you to add support for types that are considered
    | unknown or override existing mappings. You can also add mappings for your
    | custom casts.
    |
    | Example:
    | 'App\Casts\YourCustomCast' => 'string | null',
    | 'binary' => 'Blob',
    | 'bool' => 'boolean',
    | 'point' => 'CustomPointInterface',
    | 'year' => 'string',
    */
    'custom_mappings' => [
        // 'binary' => 'Blob',
    ],

    /*
    |--------------------------------------------------------------------------
    | Define Custom Relationships
    |--------------------------------------------------------------------------
    |
    | Custom relationships allow you to add support for relationships from
    | external packages that are not a part of the Laravel core. Note that
    | relationship method names are case sensitive.
    |
    | Singular: relationships that return a single model
    | Plural: relationships that return multiple models
    |
    | Example:
    |   'singular' => [
    |       'belongsToThrough',
    |   ],
    */
    'custom_relationships' => [
        'singular' => [
            // 'belongsToThrough',
        ],

        'plural' => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Case for Model Attributes and Relationships
    |--------------------------------------------------------------------------
    | Options: snake, camel, pascal
    | Defines the case style for model attributes and relationships in the
    | TypeScript definitions. For keeping a consistent naming
    | convention throughout the codebase.
    */
    'case' => [
        'columns' => 'snake',
        'relations' => 'snake',
    ],
];
