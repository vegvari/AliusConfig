[![Build Status](https://travis-ci.org/vegvari/AliusConfig.svg?branch=master)](https://travis-ci.org/vegvari/AliusConfig)

# Alius Config

Alius Config is just a simple composer package, all it can do is include php
array files.

**Never commit any sensitive information into your version control!**

### Basic usage:

```
<?php

return [
    'test' => 'this is a test',
];
```

```
$config = new \Alius\Config\Config('path_to_file.php');
print $config->get('test'); // => this is a test
```

### Multiple files, directories

You can include multiple files:

```
$config = new \Alius\Config\Config([
    'path_to_file1.php',
    'path_to_file2.php',
]);
```

One or more directories (includes the subdirectories and files recursively):

```
$config = new \Alius\Config\Config([
    'path_to_directory',
    'path_to_directory2',
]);
```

**All of the files must use php as extension and they must return array.**

### Last file wins

If you include two files with the same variable, the last file always wins:

```
<?php
// file1.php
return [
    'test' => 'one',
];
```

```
<?php
// file2.php
return [
    'test' => 'two',
];
```

```
$config = new \Alius\Config\Config(['file1.php', 'file2.php']);
print $config->get('test'); // => two
```

It's a good idea to name your files starting with numbers:

```
10-something.php
20-something_more.php
```

### Get the source file of a variable

Including lots of files could be confusing so you can get the source file of a
variable:

```
print $config->getOrigin('test'); // => file2.php
```

### Required variables

You can mark a variables required, if none of the included files has this
variable then an exception will be thrown:

```
$config = new \Alius\Config\Config('path_to_file.php', ['test']);
var_dump($config->isRequired('test')); // => true
var_dump($config->isRequired('test2')); // => false
```

### Get the variables in one array

You can get an array with all the variables:

```
$config->compile();
```

### Error thrown, if:

- any of the files doesn't exist or not readable or has extension other than php
- if you try to get a non-existing variable (```$config->get('not_there');```
  or ```$config->getOrigin('not_there');```)
- when a required variable is not found in any of the included files

The point is that it should fail fast so you can fix everything before you
commit your changes or deploy.

### Recommended usage

**Never commit any sensitive information into your version control!**

You should create a directory for all of your non-sensitive data, and another
one for sensitive data (and exclude that one from version control).

For example:

```
config/non-sensitive-data1.php
config/non-sensitive-data2.php
exclude_this_dir_from_vc/database.php
```

Or you can still use [dotenv](https://github.com/vlucas/phpdotenv) or similar
for sensitive data, for example Laravel’s env function with defaults:

```
return [
    'db_host' => env('db_host', 'localhost'),
    'db_user' => env('db_user', 'homestead'),
    'db_pass' => env('db_pass', 'secret'),
    'db_name' => env('db_name', 'homestead'),
];
```

### What’s wrong with dotenv?

Nothing, but sometimes it’s just not enough.

With this package you can include files conditionally, override variables, you
can use bool, int, float, arrays, constants, Closures, even objects or resources.

Or you can extend it, create your DatabaseConfig class so you can inject it as a
dependency.
