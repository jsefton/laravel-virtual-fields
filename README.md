# Laravel Virtual Fields

Allow eloquent models to store and retreive data against anything without needing a specific database column.

Virtual Fields hook into eloquent attribute fetching to allow it to use a specific field to map data but still support all the core features of eloquent and a hybrid of virtual and physical fields in a database table.


# Installation

```bash
composer require jsefton/laravel-virtual-fields
```

# Usage & Requirements

Virtual Fields is a trait that you can use in any eloquent model. e.g.

```php
use Illuminate\Database\Eloquent\Model;
use JSefton\VirtualFields\VirtualFields;

class Post extends Model 
{
    use VirtualFields;
}
```

For virtual fields to work the database table for the desired model must have a field called `data`. You should add this through a migration for each model / table you wish to use Virtual Fields.

```bash
php artisan make:migration add_virtual_field_data_table_to_{table} --table={table}
```

Once you have created a migration you will want something similar to the below to add the new field and handle removing in the down method.
```php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::table('{table}', function (Blueprint $table) {
        $table->longText('data')->nullable();
    });
}

/**
 * Reverse the migrations.
 *
 * @return void
 */
public function down()
{
    Schema::table('{table}', function (Blueprint $table) {
        $table->dropColumn(['data']);
    });
}
```

The reason for `data` field to be a `longText` is to support long term usage of Virtual Fields. If you keep adding new data to store via Virtual Fields it will end up with a large JSON object within this column. If you know it is only going to be used for a small amount of data then please make your migration use a suitable data type.

## Example

Once setup you can simply use normal attribute getting & setting against a model. This will automatically be handled when being saved into the database againt the model and converted back to actual attributes on query.

In this example we have a Post and a virtual field of `sub_title`. This field does not exist as a physical field in the database schema.
```php
$post = Post::find(1);

// Set a virtual field of sub_title
$post->sub_title = 'Example post sub title';
$post->save();
```
The `sub_title` will be saved in the `data` field mapped as an array with `sub_title` key and `Example post sub title` as a value.

When you want to get the value back out to use you simply use it like any normal eloquent attribute:

```php
echo $post->sub_title;
// outputs: Example post sub title
```

Please note currently for Laravel 7+ until tested and verified in lower versions.

### TODO

- Add in support for querying easily against Virtual Fields without JSON query requirement.
