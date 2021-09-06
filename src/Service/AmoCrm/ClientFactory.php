<?php


namespace App\Service\AmoCrm;


use AmoCRM\Client\AmoCRMApiClient;

/**
 * Class ClientFactory
 * @package App\Service\AmoCrm
 */
final class ClientFactory
{
    /**
     * @param bool $withToken
     * @return AmoCRMApiClient
     */
    public static function make(bool $withToken = false): AmoCRMApiClient
    {
        $client = new AmoCRMApiClient(
            $_ENV['AMOCRM_CLIENT_ID'],
            $_ENV['AMOCRM_CLIENT_SECRET'],
            $_ENV['AMOCRM_REDIRECT_URI']
        );

        if ($withToken) {
            $token = AccessToken::getToken();
            $client
                ->setAccessToken($token)
                ->setAccountBaseDomain($token->getValues()['baseDomain'])
                ->onAccessTokenRefresh([AccessToken::class, 'saveToken']);
        }

        return $client;
    }
}
