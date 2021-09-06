<?php


namespace App\Controller;


use App\Exception\RenderException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class Controller
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(templates_path());
        $this->twig = new Environment($loader);
    }

    /**
     * @param string|TemplateWrapper $name
     * @param array $context
     * @return string|null
     */
    protected function render($name, array $context = []): ?string
    {
        try {
            return $this->twig->render($name, $context);
        } catch (LoaderError $e) {
            $this->raiseRenderException('Failed to render template ' . $name);
        } catch (RuntimeError $e) {
            $this->raiseRenderException('Failed to render template ' . $name);
        } catch (SyntaxError $e) {
            $this->raiseRenderException('Failed to render template ' . $name);
        }

        return null;
    }

    /**
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    protected function raiseRenderException($message = '', $code = 0, $previous = null): void
    {
        throw new RenderException($message, $code, $previous);
    }
}
