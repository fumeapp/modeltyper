
<p align="center">
  <a href="https://laravel.com"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" width="92" height="92" /></a>
  <a href="https://www.typescriptlang.org/"><img src="https://miro.medium.com/max/816/1*mn6bOs7s6Qbao15PMNRyOA.png" width="92" height="92" /></a>
</p>


# Model Typer
> Generate TypeScript interfaces from Laravel Models

[![Packagist License](https://poser.pugx.org/fumeapp/modeltyper/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/fumeapp/modeltyper/version.png)](https://packagist.org/packages/fumeapp/modeltyper)
[![Total Downloads](https://poser.pugx.org/fumeapp/modeltyper/d/total.png)](https://packagist.org/packages/fumeapp/modeltyper)

```bash
composer require --dev fumeapp/modeltyper
php artisan model:typer
```

will output 

```ts

export interface User {
  // columns
  id: number
  email: string
  name: string
  created_at?: Date
  updated_at?: Date
  // mutators
  first_name: string
  initials: string
  // relations
  teams: Teams
}
export type Users = Array<User>

export interface Team {
  // columns
  id: number
  name: string
  logo: string
  created_at?: Date
  updated_at?: Date
  // mutators
  initials: string
  slug: string
  url: string
  // relations
  users: Users
}
export type Teams = Array<Team>
```


### What does this do?
This command will go through all of your models and make [TypeScript Interfaces](https://www.typescriptlang.org/docs/handbook/2/objects.html) based on the columns, mutators, and relationships.  You can then pipe the output into your preferred `???.d.ts`

### Requirements
Starting support is for Laravel v8+ and PHP v8+ 

1. You must have a [return type](https://www.php.net/manual/en/language.types.declarations.php) for your model relationships
```php
public function providers(): HasMany // <- this
{
    return $this->hasMany(Provider::class);
}
```
2. You must have a [return type](https://www.php.net/manual/en/language.types.declarations.php) for your model mutations
```php
public function getFirstNameAttribute(): string // <- this
{
    return explode(' ', $this->name)[0];
}
```

### Custom Interfaces
If you have custom interfaces you are using for your models you can specify them in a reserved `interfaces` array

For example for a custom `Point` interface in a `Location` model you can put this in the model

```php
public array $interfaces = [
    'coordinate' => [
        'name' => 'Point',
        'import' => "@/types/api",
    ],
];
```

And it should generate:

```ts
import { Point } from '@/types/api'

export interface Location {
  // columns
  coordinate: Point
}
```

This will override all columns, mutators and relationships

You can also specify an interface is nullable:

```php
    public array $interfaces = [
        'choices' => [
            'name' => 'ChoicesWithPivot',
            'import' => '@/types/api',
            'nullable' => true,
        ],
    ];
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

### Laravel V9 Attribute support
Laravel now has a very different way of specifying [accessors and mutators](https://laravel.com/docs/9.x/eloquent-mutators#accessors-and-mutators).  
In order to tell modeltyper the types of your attributes - be sure to add the type the attribute returns:

```php
    /**
     * Determine if the user is a captain of a team
     *
     * @return Attribute
     */
    public function isCaptain(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->teams[0]->pivot->captain ?? false,
        );
    }
```

This will generate something like:

```ts
export interface User {
  // columns
  id: number
  email: string
  name?: string
  created_at?: Date
  updated_at?: Date
  // mutators
  is_captain: boolean
  // relations
  teams: TeamUsers

}

```

### Enum Eloquent Attribute Casting
Laravel now lets you cast [Enums in your models](https://laravel.com/docs/9.x/releases#enum-casting).  This will get detected and bring in your enum class with your comments:

> `app/Enums/UserRoleEnum.php`
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
> `app/Models/User.php`
```php
protected $casts = [
    'role' => App\Enums\UserRoleEnum::class,
];
```

Now our modeltyper output will look like the following:
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

> ModelTyper uses Object Literals instead of TS Enums (for opinionated reasons)[https://maxheiber.medium.com/alternatives-to-typescript-enums-50e4c16600b1]
> Notice how the comments are found and parsed - they must follow the specified format



