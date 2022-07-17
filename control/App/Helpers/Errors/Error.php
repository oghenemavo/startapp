<?php
/**
 * Project: startup
 * File: Error.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:56 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Errors;


use App\Helpers\Misc\View;
use ErrorException;
use Exception;

class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     *
     * @return void
     * @throws ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }


    /**
     * Exception Handler
     *
     * @param Exception $exception  The exception
     * @throws Exception
     */
    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
//        getenv('ENVIRONMENT') == 'development'
        $r = true;
        if ($r) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Err code: '" . $exception->getCode() . "'</p>";
            echo "<p>Messages: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__, 4) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);

            View::render("/errors/$code.html");
//            View::renderTemplate("/errors/$code.html");

//            require_once dirname(__DIR__, 3) . "/redir/$code.html";

            //View::renderTemplate("$code.html");
        }
    }

}