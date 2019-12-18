# README

I will try to describe the process of making this project.

## Setup

Installing the PHP dependencies (the ones missing from my configuration) :

```bash
sudo apt-get install php-zip
sudo apt-get install php-mbstring
```

Creating the laravel project :

```bash
laravel new mage-laravel
cd new-laravel
git init
git add --all
git commit -m "Initializing Laravel project"
```

## Setting up the testing environment

Laravel documentation :

- [Environment configuration](https://laravel.com/docs/6.x/configuration#environment-configuration)
- [Testing](https://laravel.com/docs/5.8/testing)

I tried to follow the [TDD Laravel Introduction by Coder's Tape][TDD Laravel Introduction by Coder's Tape] along the Laravel documentation to make this up. By making some research on subjects still obscure to me, I ended up with some differences.

__Some useless things in the tutorial__

Following the tutorial, he edits the `phpunit.xml` to set the testing database to SQLite and to handle it inside the memory :

```xml
<server name="DB_CONNECTION" value="sqlite"/>
<server name="DB_DATABASE" value=":memory:"/>
```

But these settings were already in there when I initialized the project with Laravel.

Also, he creates a file `database/database.sqlite`, but this seems useless because the database is not in a file but handled directly in memory.

__Installing `phpunit`__

I tried to install `phpunit` using 

```bash
sudo apt-get install phpunit
```

and run it, but it failed with an error : 

```
Call to undefined method PHPUnit\Util\Configuration::getExtensionConfiguration()
```

It seems that this version [is too old](https://stackoverflow.com/a/41871212) (`phpunit --version` returned 6.5.5). 
Hopefully, a more recent version (8.5.0) was installed with Laravel, which can be found at `vendor/bin/phpunit`. This time, tests passed successfully. 

I added aliases for convenience : 

```bash
alias pu="clear && /mnt/d/dev/mage-laravel/vendor/bin/phpunit"
alias pf="pu --filter"
```
I set an absolute path to be able to run it from anywhere.

__Should I modify `.env` files ?__

Following the [Coder's Tape's tutorial][TDD Laravel Introduction by Coder's Tape], I also started to edit the `.env` file by setting the database to be a SQLite one (`DB_CONNECTION=sqlite`). It bothered me for two reasons :
1. I didn't want a SQLite database in « production »
2. Is this modification that useful, as it's already set in `phpunit.xml` ?

For the first issue, I read in the Laravel documentation that a `.env.testing` file could be used when testing instead. But a new question arised. The doc stipulates to not add the `.env` file to versionning. Does this rule apply to `.env.testing` too ? I didn't found a clear answear during my research, but I read that one reason to not version the `.env` file is because of the `APP_KEY` it contains. I was wondering if this key was used in testing environment. I removed it, and tests failed. I concluded that the key must be kept in the `.env.testing`, thus the same rule as `.env` applies to it. Making some research, I found that `.env` files are not made to be shared, except for the `.env.example` (which doesn't specify the APP_KEY).

__SQLite__

I needed to install PHP's SQLite extension :

```bash
sudo apt-get install php7.2-sqlite3
```

Then I could finally run the first test :

```bash
./vendor/bin/phpunit
```

## Actor's features testing

First, we create Actors resources :

```bash
php artisan make:controller ActorsController
php artisan make:model Actor --migration
```

These commands create 3 files :
- A controller
- A model
- And a migration file to create the table in the database

I then wrote tests for these resources, following the [Coder's Tape's tutorial][TDD Laravel Introduction by Coder's Tape].

## References' features testing

The next resource is Reference, as it's simple to handle and is somehow related to the Actor resource.

I first generated the associated controller, model and migration files :

```bash
php artisan make:controller ReferencesController
php artisan make:model Reference --migration
```

Then, I wrote the same type of tests, as `Actor` and `Reference` have the same attributes.  The main difference lies in Reference having a `category` field which is an enumeration made of two values : 'source' and 'bibliography'. I used the `in` Rule to validate that.

## Actor's References

Actors can have several associated References. This can be represented as a many-to-many relationship, or n:n.

I first wrote the tests. I first create a Reference, then an Actor with an array of References. I then check for the number of References for the created actor.

### Request validation

First, we handle the request data. We receive an array of References' ids, and we have to check that each value corresponds to an existing row inside the `references` table. I figured that I would need an [array validator](https://laravel.com/docs/6.x/validation#validating-arrays) for that :

```php
$data = $request->validate([
    'references.*' => 'exists:references,id',
]);
```

If an empty array is passed to `validate()`, the `$data`'s `references` key will not be set, so we have to check if it exists before using it :

```php
$references = [];
if (array_key_exists('references', $data)) {
    $references = $data['references'];
}
```

### Applying the changes in the database

Then, we need to make the sent references and the information stored in the database to be the same. I.e, when no references are sent, it means that we have to remove all References to the Actor from the database. We use the `sync` method to accomplish that :

```php
$actor->references()->sync($references);
``` 

### Creating the intermediate table

Next, is the intermediate table that we will use to store our many-to-many relationships between Actor and Reference. I first thought that it was generated automatically by Laravel, but not. I used an Artisan command to do that :

```bash
php artisan make:migration actor_reference
```

And then defined the main keys and also the foreign key references, to have my database properly structured :

```php

Schema::create('actor_reference', function (Blueprint $table) {
    $table->unsignedBigInteger('actor_id');
    $table->unsignedBigInteger('reference_id');

    $table->foreign('actor_id')->references('id')->on('actors');
    $table->foreign('reference_id')->references('id')->on('references');
});
```

### Defining the many-to-many relationship

Defining an unidirectionnal [many-to-many relationship](https://laravel.com/docs/6.x/eloquent-relationships#many-to-many) is fairly simple thanks to Eloquent. We just need to indicates that an Actor can have multiple References like this :

```php
public function references()
{
    return $this->belongsToMany('App\Reference');
}
```

At first, it can be a little confusing why an Actor should "belongs to many References"... But it makes sens, as it's only defined in this way (References do not "belong to many Actors"). And at the end, the Actor object really has the `references` stored in it.

### Questionning the Request validator position

---

## Kinship

```bash
php artisan make:controller KinshipsController
php artisan make:model Kinship --migration
php artisan make:migration create_actor_kinship_table
```

[Defining Custom Intermediate Table Models](https://laravel.com/docs/6.x/eloquent-relationships#defining-custom-intermediate-table-models)

php artisan make:migration create_kinshippables_table

- [X] Move ActorReferenceTest into Actor (or else we will have too many files)
- [ ] Polymorphic one-to-many References, so we will not have to handle too many tables
- [ ] Polymorphic one-to-many Places (Family, ActorRole). Attention for ActorRoleAndPlace : date on the relation actor_has_role, and not place.
- [ ] Use Model Factories ?
- [ ] PHP Traits for Dates (and possibly Notes too ?)

---

## Notes

Possible Traits :
- [ ] Reference : No, because Reference model exists independantly
- [X] Date : date_start, date_end, date_start_accuracy, date_end_accuracy
- [X] Note
Because, they are part of the Model.
Why not put them into separate tables (and models) ? Performance, so we don't need to abuse of JOINing tables everytime.


[TDD Laravel Introduction by Coder's Tape]: https://www.youtube.com/watch?v=0Rjsuw1ScXg&list=PLpzy7FIRqpGAbkfdxo1MwOS9xjG3O3z1y&index=1
