<?php


namespace App\Controller;


use AmoCRM\Exceptions\AmoCRMApiException;
use App\DTO\Form\Form;
use App\Exception\FormProcessingException;
use App\Http\ApiResponse;
use App\Service\AmoCrm\FormProcessor;
use Assert\AssertionFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class FormController
 * @package App\Controller
 */
class FormController extends Controller
{
    /**
     * @return Response
     */
    public function form(): Response
    {
        return new Response($this->render('form.html.twig'), Response::HTTP_OK, [ 'Content-Type' => 'text/html' ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws FormProcessingException
     */
    public function process(Request $request): Response
    {
        try {
            $form = Form::fromArray($request->toArray());
        } catch (AssertionFailedException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            (new FormProcessor())->process($form);
        } catch (AmoCRMApiException $e) {
            throw new FormProcessingException('Ошибка при обработке формы', 0, $e);
        }

        return new ApiResponse('', null, [], Response::HTTP_NO_CONTENT);
    }
}
