Simphle - Quick PHP Development Server
=======================================

Simphle is a friendly wrapper for PHP built-in web server, with some handy features for an easy developement.


Requirements
------------

- PHP 5.4 or above with built-in webserver and HTTP extension
- The [Symphony Process Component](https://github.com/symfony/process)


Installation
------------

 1. **Composer**

 ```console
 $ composer require vtardia/simphle
 ```

 Use the `-g` option to install it globally on your system

 2. **Custom**

 Clone this repository to a shared directory, for example `/usr/local/php/simphle` or `/Users/Shared/php/Simphle` on a Mac, then run `composer install`.

 Add the `bin` directory to your local path or create a link to the `bin/simphle` executable:

 ```console
    $ ln -s /usr/local/php/simphle/bin/simphle ~/bin/simphle
 ```

Usage
-----

### Basic

Start a server with default settings in the current directory:

    $ cd /path/to/myapp/docroot
    $ simphle


### Advanced

Create a `server.json` file in your project's directory with your custom settings and then launch `simphle` from inside that path.

```json
{
    "host":"0.0.0.0",
    "port":5000,
    "docroot":"public",
    "router":"myrouter.php|default",
    "ini":"mysettings.ini|default",
    "controller": "mycontroller.php",
    "env": {
        "myvar": "somevalue",
        "anothervar": {
            "one": "foo",
            "two": "bar"
        }
    }
}
```

The `router` and `ini` file paths are relative to the current working directory. The main `Simphle` script searches for these files in the current working directory first, then in Simphle's `share` directory. The `.php` and `.ini` extensions are not required for preset router and INI files.

The `controller` file path is relative to the document root directory.

#### Launch it with Composer

Create a `scripts` section in you `composer.json`:

```json
"scripts": {
    "server": "simphle"
}
```

Then launch it using

```console
$ composer server
```

#### The default router

Simphle's default router tries to simulate a typical Apache `.htaccess` file:

```aconf
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

If the requested URI exists tries to fetch it, if not it searches for an `index.php`, finally falls back to a "404 not found" error.


#### Using a front controller

By specifying a `controller` settings Simphle uses this file as rewrite target instead of `index.php`, so you can use for example `app.php`.


#### Customizing the environment

The `env` section defines environment variables that are passed to the PHP files through the `$_ENV` superglobal. JSON objects are converted to associative arrays.

Sometimes the `$_ENV` superglobal is not populated. It depends on the `variables_order` settings in `php.ini`:

```ini
; This directive determines which super global arrays are registered when PHP
; starts up. If the register_globals directive is enabled, it also determines
; what order variables are populated into the global space. G,P,C,E & S are
; abbreviations for the following respective super globals: GET, POST, COOKIE,
; ENV and SERVER. There is a performance penalty paid for the registration of
; these arrays and because ENV is not as commonly used as the others, ENV is
; is not recommended on productions servers. You can still get access to
; the environment variables through getenv() should you need to.
; Default Value: "EGPCS"
; Development Value: "GPCS"
; Production Value: "GPCS";
; http://php.net/variables-order
variables_order = "GPCS"
```

This setting can be overridden by local `htaccess` or `php.ini` files.


License
-------

Simphle is licensed under the MIT License - see the `LICENSE` file for details
