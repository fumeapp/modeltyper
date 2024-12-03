# Model Typer

[![Github actions](https://github.com/fumeapp/modeltyper/actions/workflows/phpstan.yml/badge.svg)](https://packagist.org/packages/fumeapp/modeltyper)
[![Latest Stable Version](https://poser.pugx.org/fumeapp/modeltyper/v)](https://packagist.org/packages/fumeapp/modeltyper)
[![Total Downloads](https://poser.pugx.org/fumeapp/modeltyper/downloads)](https://packagist.org/packages/fumeapp/modeltyper)
[![License](https://poser.pugx.org/fumeapp/modeltyper/license)](https://choosealicense.com/licenses/mit/)
[![PHP Version Require](https://poser.pugx.org/fumeapp/modeltyper/require/php)](https://packagist.org/packages/fumeapp/modeltyper)

<p align="center">
  <a href="https://laravel.com"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" width="92" height="92" /></a>
  <a href="https://www.typescriptlang.org/"><img src="https://miro.medium.com/max/816/1*mn6bOs7s6Qbao15PMNRyOA.png" width="92" height="92" /></a>
</p>

Model Typer is a powerful tool designed for developers working with Laravel and TypeScript. Its primary purpose is to simplify the generation of TypeScript interfaces from Laravel models, enhancing type safety and consistency in your applications.

## Upgrade Guide

Please read the upgrade guide [here](https://github.com/fumeapp/modeltyper/UPGRADE.md)

## Installation

Starting support is for Laravel >=v11.33.0 and PHP v8.2+

> [!IMPORTANT]
> For Laravel >=10.43.0 || <11.33.0, use v2 instead

Require this package with composer using the following command:

```bash
composer require --dev fumeapp/modeltyper
```

Optionally, you can publish the config file using the following command:

```bash
php artisan vendor:publish --provider="FumeApp\ModelTyper\ModelTyperServiceProvider" --tag=config
```

## Usage

You can simply run the following command to generate TypeScript interfaces:

```bash
php artisan model:typer
```

The output is an accurate, type-safe representation of Laravel models in TypeScript, such as:

```ts
export interface User {
    // columns
    id: number;
    email: string;
    name: string;
    created_at?: Date;
    updated_at?: Date;
    // mutators
    first_name: string;
    initials: string;
    // relations
    teams: Teams;
}
export type Users = Array<User>;

export interface Team {
    // columns
    id: number;
    name: string;
    logo: string;
    created_at?: Date;
    updated_at?: Date;
    // mutators
    initials: string;
    slug: string;
    url: string;
    // relations
    users: Users;
}
export type Teams = Array<Team>;
```

### How does it work?

This command will go through all of your models and make [TypeScript Interfaces](https://www.typescriptlang.org/docs/handbook/2/everyday-types.html#interfaces) based on the database columns, mutators, and relationships.

You can then pipe the output into your preferred `???.d.ts`, or set the [optional argument](#optional-arguments) `output-file` to generate it

> [!TIP]
> To view the current mappings that are being used, use the following command:
>
> ```bash
> php artisan model:typer-mappings
> ```
>
> These mappings can be [extended or overridden](#override-default-mappings--add-new-ones) in the config

### Requirements

1. You must have a [return type](https://www.php.net/manual/en/language.types.declarations.php) for your model relationships

```php
public function providers(): HasMany // <- this
{
    return $this->hasMany(Provider::class);
}
```

2. You must have a [return type](https://www.php.net/manual/en/language.types.declarations.php) for your model mutations

```php
protected function firstName(): Attribute
{
    return Attribute::make(
        get: fn (string $value): string => ucfirst($value), // <- this
    );
}
```

### Optional Arguments

- output-file : Echo the definitions into a file


### Additional Options

- --model= : Generate typescript interfaces for a specific model
- --global : Generate typescript interfaces in a global namespace named models
- --json : Output the result as json
- --use-enums : Use typescript enums instead of object literals
- --plurals : Output model plurals
- --no-relations : Do not include relations
- --optional-relations : Make relations optional fields on the model type
- --no-hidden : Do not include hidden model attributes
- --timestamps-date : Output timestamps as a Date object type
- --optional-nullables : Output nullable attributes as optional fields
- --api-resources : Output api.MetApi interfaces
- --fillables : Output model fillables
- --fillable-suffix= : Appends to fillables
- --ignore-config : Ignore options set in config

### Custom Interfaces

If you have custom interfaces you are using for your models you can specify them in a reserved `interfaces` array

For example for a custom `Point` interface in a `Location` model you can put this in the model

```php
public array $interfaces = [
    'coordinate' => [
        'import' => "@/types/api",
        'type' => 'Point',
    ],
];
```

And it will generate:

```ts
import { Point } from "@/types/api";

export interface Location {
    // override
    coordinate: Point;
}
```

This will override all columns, mutators and relationships

You can also specify an interface is nullable:

```php
public array $interfaces = [
    'choices' => [
        'import' => '@/types/api',
        'type' => 'ChoicesWithPivot',
        'nullable' => true,
    ],
];
```

You can also choose to leave off the import and just use the type:

```php
public array $interfaces = [
    'choices' => [
        'type' => "'good' | 'bad'",
    ],
];
```

And it should generate:

```ts
export interface Location {
    // columns
    choices: "good" | "bad";
}
```

Using the custom interface is also a good place to append any additional properties you want to add to the interface.

For example, if your interface keeps some additional state in something like Vuex, you can add it to the interfaces:

```php
    public array $interfaces = [
        'state' => [
            'type' => "found' | 'not_found' | 'searching' | 'reset'",
        ],
    ];
```

This will generate:

```ts
export interface Location {
    // ...
    // overrides
    state: "found" | "not_found" | "searching" | "reset";
    // ...
}
```

### Override default mappings / add new ones

You can override the default mappings provided by Model Typer or add new ones by [publishing the config](#installation)

Then inside `custom_mappings` add the Laravel type as the key and assign the TypeScript type as its value

You can also add mappings for your [Custom Casts](https://laravel.com/docs/11.x/eloquent-mutators#custom-casts)


```php
'custom_mappings' => [
    'App\Casts\YourCustomCast' => 'string | null',
    'binary' => 'Blob',
    'bool' => 'boolean',
    'point' => 'CustomPointInterface',
    'year' => 'string',
],
```

### Declare global

Generate your interfaces in a global namespace named `model`

```bash
artisan model:typer --global
```

```ts
export {}
declare global {
  export namespace models {

    export interface Provider {
      // columns
      id: number
      user_id: number
      avatar?: string
...
```

### Output plural interfaces for collections

```bash
artisan model:typer --plurals
```
Exports for example, when a `User` model exists:

```ts
export type Users = User[]
```

### Output Api.MetApi* resources

```bash
artisan model:typer --api-resources
```

Exports:

```ts
export interface UserResults extends api.MetApiResults { data: Users }
export interface UserResult extends api.MetApiResults { data: User }
export interface UserMetApiData extends api.MetApiData { data: User }
export interface UserResponse extends api.MetApiResponse { data: UserMetApiData }
```

### Enable all output options

```bash
artisan model:typer --all
```

Exports both plurals & api-resources. i.e. it is equivalent to:

```bash
artisan model:typer --plurals --api-resources
```

### For Single Model

Generate your interfaces for a single model

```bash
artisan model:typer --model=User
```

### Output as JSON

Generate your interfaces as JSON

```bash
artisan model:typer --json
```

### Enum Eloquent Attribute Casting

Laravel lets you cast [Enums in your models](https://laravel.com/docs/11.x/eloquent-mutators#enum-casting). This will get detected and bring in your enum class with your comments:

> [!NOTE]
> ModelTyper uses Object Literals by default instead of TS Enums [for opinionated reasons](https://maxheiber.medium.com/alternatives-to-typescript-enums-50e4c16600b1). But you can use `--use-enums` option to use TS Enums instead of Object Literals.

`app/Enums/UserRoleEnum.php`

```php
<?php

namespace App\Enums;

/**
 * @property ADMIN - Can do anything
 * @property USER - Standard read-only
 */
enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
}
```

Then inside our User model

`app/Models/User.php`

```php
protected $casts = [
    'role' => App\Enums\UserRoleEnum::class,
];
```

Now our ModelTyper output will look like the following:

```ts
const UserRoleEnum = {
  /** Can do anything */
  ADMIN: 'admin',
  /** Standard read-only */
  USER: 'user',
}
export type UseRoleEnum = typeof UseRoleEnum[keyof typeof UserRoleEnum]
export interface User {
  ...
  role: UserRoleEnum
  ...
}
```

> [!NOTE]
> Notice how the comments are found and parsed - they must follow the specified format
