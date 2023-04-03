<?php

namespace App\Controller;

use PDO;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected PDO $db;

    protected function db(): PDO
    {
        $this->db = new PDO(
            sprintf(
                '%s:host=%s;port=%d;dbname=%s',
                $_ENV['DB_TYPE'],
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_NAME']
            ),
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );

        return $this->db;
    }

    /**
     * Renders a twig template with the params provided.
     *
     * @param string $view
     * @param array $params
     * @return void
     */
    protected function render(string $view, array $params = []): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../resources/views');
        $twig = new Environment($loader);

        try {
            echo $twig->render(sprintf('/layouts/%s', $view), $params);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            error_log($e);
        }
    }
}
