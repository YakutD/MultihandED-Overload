# MultihandED-Overload
An Artisan command that simplifies the process of creating trait-based method overloads by the number of arguments.

The package allows you to quickly create templates for traits, on the basis of which you can implement the mechanism of overloading methods by the number of arguments.

The *overload:create* command takes 2 arguments: *method* (the name of the method used in the created trait) and *namespace* (the namespace of the trait, where the last segment is the name of the trait.
All traits are created in the *app* directory, by default in the *Traits\Overloads* subdirectories, however you can set your own subdirectories by setting the *TRAITS_DIRECTORY* variable in .env to your liking.

The resulting namespace for all newly created traits already includes the *App\TRAITS_DIRECTORY* segments, so there is no need to specify them in the namespace parameter when generating.

For each namespace, a single trait is created, with the postfix Main. It connects all other traits of the same namespace. When generating each new trait, this file is automatically updated.

**EXAMPLE:**

*php artisan overload:create test_func Test\TestOverload*

Get generated files *TestOverload*
```
<?php

namespace App\Traits\Overloads\Test;

trait TestOverload
{
    function test_func(...$args) 
    {
        switch(func_num_args())
        {
        /** Example
            case 0:
                echo 'no arguments';
                break;
            case 1:
                echo $args[0];
                break;
	    case 2:
		[0 => $param1, 1 => $param2] = $args;
                echo "$param1, $param2";
                break;
	    */
        }
    }
}
```

and *TestMain.php*

```
<?php

namespace App\Traits\Overloads\Test;

trait TestMain 
{
	use TestOverload;
}
```
By removing the comments in the *test_func* method, we can use it in any project model, we just need to connect the trait using a single trait *TestMain*:

```
use App\Traits\Overloads\Test\TestMain;
```


