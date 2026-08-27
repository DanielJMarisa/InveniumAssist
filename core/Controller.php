<?php

declare(strict_types=1);

namespace Core;

use Core\Http\Response;

abstract class Controller
{
    /**
     * Render a template.
     */
    protected function view(
        string $template,
        array $data = []
    ): Response {
        extract($data, EXTR_SKIP);

        $file = TEMPLATE_PATH
            . DS
            . str_replace('.', DS, $template)
            . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException(
                "View [{$template}] not found."
            );
        }

        ob_start();

        require $file;

        $content = ob_get_clean();

        return Response::make($content);
    }

    /**
     * Redirect.
     */
    protected function redirect(
        string $path,
        int $status = 302
    ): Response {
        return Response::redirect(
            $path,
            $status
        );
    }

    /**
     * Return JSON.
     */
    protected function json(
        array $data,
        int $status = 200
    ): Response {
        return Response::json(
            $data,
            $status
        );
    }
}