<?php


namespace App\EventListener;


use App\Http\ApiResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Class ExceptionListener
 * @package App\EventListener
 */
class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (in_array('application/json', $request->getAcceptableContentTypes(), true)) {
            $event->setResponse($this->createApiResponse($exception));
            return;
        }

        $event->setResponse($this->createResponse($exception));
    }

    private function createResponse(Throwable $exception): Response
    {
        if (method_exists($exception, 'render')) {
            $result = $exception->render();
            if ($result instanceof Response) {
                return $result;
            }
        }

        $message = 'Oops, something went wrong';
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof HttpExceptionInterface) {
            $message = $exception->getMessage();
            $status = $exception->getStatusCode();
        }

        return new Response($message, $status);
    }

    /**
     * @param Throwable $exception
     * @return ApiResponse
     */
    private function createApiResponse(Throwable $exception): ApiResponse
    {
        if (method_exists($exception, 'renderApi')) {
            $result = $exception->renderApi();
            if ($result instanceof ApiResponse) {
                return $result;
            }
        }

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        // TODO: fill errors array
        return new ApiResponse($exception->getMessage(), null, [], $statusCode);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException']
        ];
    }
}
