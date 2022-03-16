
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

In the latest versions of Laravel you can now define mutators & accessor within the new `Illuminate\Database\Eloquent\Casts\Attribute` return type. So your model might look something like this:
```php
/**
 * Get the user's first name.
 *
 * @return \Illuminate\Database\Eloquent\Casts\Attribute
 */
protected function firstName(): Attribute
{
    return new Attribute(
        get: fn ($value, $attributes) => ucfirst(explode(' ', $attributes['name'])[0]),
    );
}
```

With this new way there is no easy way to know what the get function should return. So if you are using this new way you can define a `$mutations` array on your model like so:
```php
public array $mutations = [
    'first_name' => 'string',
];
```
This lets us know it should use value of that as the return type of the attribute.


### Declare global
Generate your interfaces in a global namespace named `model`
```bash
artisn model:typer --global
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
