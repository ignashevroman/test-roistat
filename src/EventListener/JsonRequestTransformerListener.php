<?php


namespace App\EventListener;


use JsonException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonRequestTransformerListener implements EventSubscriberInterface
{
    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->supports($request)) {
            return;
        }

        try {
            $data = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (is_array($data)) {
                $request->request->replace($data);
            }
        } catch (JsonException $exception) {
            $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function supports(Request $request): bool
    {
        return $request->getContentType() === 'json' && $request->getContent();
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100]
        ];
    }
}
