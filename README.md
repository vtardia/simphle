Simphle - Quick PHP Development Server
=======================================

Simphle is a friendly wrapper for PHP built-in web server, with some handy features for an easy developement.


Requirements
------------

- PHP 5.4 or above with built-in webserver and HTTP extension
- The [Symphony Process Component](https://github.com/symfony/process)


Installation
------------

Clone this repository to a shared directory, for example `/usr/local/php/simphle` or `/Users/Shared/php/Simphle` on a Mac, then run `composer install`.

Add the `bin` directory to your local path or create a link to the `bin/sserver` executable:

    $ ln -s /usr/local/php/simphle/bin/sserver ~/bin/sserver


Usage
-----

### Basic

Start a server with default settings in the current directory:

    $ cd /path/to/myapp/docroot
    $ sserver


### Advanced

Create a `server.json` file in your project's directory with your custom settings and then launch `sserver` from inside that path.

    {
        "host":"0.0.0.0",
        "port":5000,
        "docroot":"public",
        "router":"myrouter.php|default",
        "ini":"mysettings.ini|default"
    }

The `router` and `ini` file paths are relative to the current working directory. The main `Simphle` script searches for these files in the current working directory first, then in Simphle's `share` directory. The `.php` and `.ini` extensions are not required for preset router and INI files.


#### The default router

Simphle's default router tries to simulate a typical Apache `.htaccess` file:

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]


If the requested URI exists tries to fetch it, if not it searches for an `index.php`, finally falls back to a "404 not found" error.


License
-------

Simphle is licensed under the MIT License - see the `LICENSE` file for details
