<?php


namespace App\Exception;


use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class RenderException extends RuntimeException
{
    public function render(): Response
    {
        return new Response('Failed to render template');
    }
}
