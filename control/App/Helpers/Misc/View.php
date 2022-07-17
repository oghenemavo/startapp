<?php
/**
 * Project: startup
 * File: Views.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:57 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Misc;


use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;


class View
{
    /**
     * Render a view file
     *
     * @param string $view The view file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws Exception
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__, 4) . "/views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     * @return string
     */
    public static function getTemplate ($template, $args = []) {
        static $twig = null;

        if ($twig === null) {
            $file = dirname(__DIR__, 4) . "/views";
            $loader = new FilesystemLoader($file);
            $twig = new Environment($loader);
        }

//        try {
            return $twig->render($template, $args);
//        } catch (LoaderError $e) {
//        } catch (RuntimeError $e) {
//        } catch (SyntaxError $e) {
//        }
    }

    /**
     * @param $template
     * @param array $args
     */
    static public function renderTemplate ($template, $args = []) {
        echo static::getTemplate($template, $args);
    }

}