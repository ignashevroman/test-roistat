<?php


namespace App\Controller;


use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Service\AmoCrm\AccessToken;
use App\Service\AmoCrm\ClientFactory;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class OAuthController
 * @package App\Controller
 */
class OAuthController
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
        $this->session->start();
    }

    /**
     * @return RedirectResponse
     * @throws Exception
     */
    public function redirect(): RedirectResponse
    {
        $client = ClientFactory::make();

        $state = bin2hex(random_bytes(16));
        $this->session->set('oauth2state', $state);

        $redirectUrl = $client->getOAuthClient()->getAuthorizeUrl(
            [
                'state' => $state,
                'mode' => 'post_message'
            ]
        );

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws AmoCRMoAuthApiException
     */
    public function callback(Request $request): Response
    {
        $state = $request->get('state');
        $oauth2state = $this->session->get('oauth2state');
        if (!$oauth2state || !$state || $state !== $oauth2state) {
            unset($_SESSION['oauth2state']);
            throw new BadRequestHttpException('Invalid state');
        }

        $code = $request->get('code');
        if (!$code) {
            throw new BadRequestHttpException('Code is missing');
        }

        $client = ClientFactory::make();

        $referer = $request->get('referer');
        $client->setAccountBaseDomain($referer);

        $accessToken = $client->getOAuthClient()->getAccessTokenByCode($code);
        if (!$accessToken->hasExpired()) {
            AccessToken::saveToken($accessToken, $client->getAccountBaseDomain());
        }

        return new Response("Access token received");
    }
}
