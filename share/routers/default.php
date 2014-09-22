<?php
require_once realpath(dirname(__FILE__) . "/../../vendor/autoload.php");

use Simphle\Server;

Server::init(dirname(__FILE__) . '/../../');

if (!($directoryIndex = getenv('index'))) {
    $directoryIndex = Server::DIRECTORY_INDEX;
}

if (!($controller = getenv('controller'))) {
    $controller = null;
}

// Load environment
$env = @unserialize(getenv('env'));
if (false !== $env) {
    foreach ($env as $k => $val) {

        // Always insert using putenv()
        if (is_array($val) || is_object($val)) {
            putenv(sprintf("%s=%s", $k, serialize($val)));
        } else {
            putenv(sprintf("%s=%s", $k, $val));
        }

        // Insert in $_ENV if settings allow this
        if (false !== stristr(ini_get('variables_order'), "E")) {
            $_ENV[$k] = $val;
        }

        // Insert in $_SERVER if settings allow this
        if (false !== stristr(ini_get('variables_order'), "S")) {
            $_SERVER[$k] = $val;
        }
    }
}

// If requesting a directory then serve the default index
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);
if (empty($ext)) {
    if (is_dir($_SERVER['DOCUMENT_ROOT'] . $path)) {
        $path = rtrim($path, '/') . '/' . $directoryIndex;
    } elseif ($controller) {
        
        // Use provided front controller
        $path = '/' . $controller;

    } else {
        
        // Use index.php as front controller
        $path = '/' . $directoryIndex;
    }
}

// If the file exists then return false and let the server handle it
if (is_readable($_SERVER['DOCUMENT_ROOT'] . $path)) {

    // Access logged by the server process
    if ($path == '/' . $controller
        && is_readable($_SERVER['DOCUMENT_ROOT'] . '/' . $controller)) {
        
        Server::logAccess(200);

        // URL Rewriting
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT']
            . '/' . $controller;
        $_SERVER['SCRIPT_NAME'] = '/' . $controller;
        
        // Process controller
        include $_SERVER['DOCUMENT_ROOT'] . '/' . $controller;
        exit;
    }
    
    // Process static resource
    return false;
    
} elseif($controller 
    && is_readable($_SERVER['DOCUMENT_ROOT'] . '/' . $controller)) {
    
    // Log a 404 on the server side because the document
    // does not exist
    Server::logAccess(404);
    
    // URL Rewriting
    $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT']
        . '/' . $controller;
    $_SERVER['SCRIPT_NAME'] = '/' . $controller;
    
    // Process controller
    include $_SERVER['DOCUMENT_ROOT'] . '/' . $controller;
    exit;

} elseif ($error404 = getenv('error_404')) {

    // Use the app error 404 feature...

    // Log the original URL
    Server::logAccess(404);

    // Rewrite the internal URI to the Error URI/Document
    $_SERVER['REQUEST_URI'] = $error404;

    // Exec the index document or front controller and exit
    if ($controller
        && is_readable($_SERVER['DOCUMENT_ROOT'] . '/' . $controller)) {
        include $_SERVER['DOCUMENT_ROOT'] . '/' . $controller;
    } else {
        include $_SERVER['DOCUMENT_ROOT'] . '/' . $directoryIndex;
    }
    exit;
}

// Default behavior
Server::logAccess(404);
Server::quit(404);
