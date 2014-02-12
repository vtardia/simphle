<?php
namespace Simphle;

class Server
{
    const DIRECTORY_INDEX = 'index.php';
    public static $root = null;
    
    public static function init($root)
    {
        ini_set('cli_server.color', 1);
        ini_set('expose_php', 0);
        ini_set('html_errors', 1);
        
        if (!is_dir(realpath($root))) {
            throw new Exception("Invalid directory");
        }
        self::$root = realpath($root);
    }

    public static function logAccess($status = 200)
    {
        $color = 32; // green
    
        // Yellow
        if ($status >= 400) {
            $color = 33;
        }
    
        // Red
        if ($status >= 500) {
            $color = 31;
        }
    
        if (!ini_get('cli_server.color')) {
            $color = 0;
        }
    
        file_put_contents(
            "php://stdout",
            sprintf(
                "[%s] \033[%dm%s:%s [%s]: %s\033[0m\n",
                date("D M j H:i:s Y"),
                $color,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['REMOTE_PORT'],
                $status,
                $_SERVER['REQUEST_URI']
            )
        );
    }
    
    public static function quit($code = 404, $exit = true)
    {
        http_response_code($code);
        include self::$root . '/share/errors/' . $code . '.php';
        exit;
    }

}
