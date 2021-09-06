<?php


namespace App\Service\AmoCrm;


use League\OAuth2\Client\Token\AccessTokenInterface;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class AccessToken
 * @package App\Service\AmoCrm
 */
final class AccessToken
{
    /**
     * @return string
     */
    private static function getTokenPath(): string
    {
        return storage_path($_ENV['AMOCRM_TOKEN_PATH'] ?? 'amocrm_token.json');
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public static function getToken(): \League\OAuth2\Client\Token\AccessToken
    {
        $path = self::getTokenPath();

        if (!file_exists($path)) {
            throw new FileNotFoundException(null, 0, null, $path);
        }

        $accessToken = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        if (
            isset($accessToken)
            && !empty($accessToken['accessToken'])
            && !empty($accessToken['refreshToken'])
            && !empty($accessToken['expires'])
            && !empty($accessToken['baseDomain'])
        ) {
            return new \League\OAuth2\Client\Token\AccessToken(
                [
                    'access_token' => $accessToken['accessToken'],
                    'refresh_token' => $accessToken['refreshToken'],
                    'expires' => $accessToken['expires'],
                    'baseDomain' => $accessToken['baseDomain'],
                ]
            );
        }

        throw new RuntimeException('Bad amocrm access token');
    }

    /**
     * @param AccessTokenInterface $accessToken
     * @param string $baseDomain
     */
    public static function saveToken(AccessTokenInterface $accessToken, string $baseDomain): void
    {
        $data = [
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $baseDomain,
        ];

        file_put_contents(self::getTokenPath(), json_encode($data, JSON_THROW_ON_ERROR));
    }
}
