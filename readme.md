
# Model Typer
> Generate TypeScript interfaces from Laravel Models

[![Packagist License](https://poser.pugx.org/fumeapp/modeltyper/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/fumeapp/modeltyper/version.png)](https://packagist.org/packages/fumeapp/modeltyper)
[![Total Downloads](https://poser.pugx.org/fumeapp/modeltyper/d/total.png)](https://packagist.org/packages/fumeapp/modeltyper)

```bash
php artisan model:typer
```

will output 

```ts

export interface User {
  // columns
  id: number
  email: string
  name: string
  is_sub: boolean
  created_at: Date
  updated_at: Date
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
  created_at: Date
  updated_at: Date
  // mutators
  initials: string
  slug: string
  url: string
  // relations
  users: Users
}
export type Teams = Array<Team>
```
