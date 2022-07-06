<?php
/**
 * Anan AwsCognito, Magento 2.4 package to manage Web and API authentication with AWS Cognito
 * php version 8.1
 * 
 * @category Authentication
 * @package  Aws Cognito
 * @author   Anan Fauzi <mr.ananfauzi@gmail.com>
 * @license  MIT license <https://opensource.org/licenses/MIT>
 * @link     https://github.com/ananfauzi
 */
namespace Anan\AwsCognito\Model\Client;

class Token
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Set token
     * @param string
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Get token
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Is token valid
     * @return bool
     */
    public function isValid(): bool
    {
        $parts = \explode('.', $this->token);
        if (count($parts) !== 3) {
            return false;
        }

        $parts = array_filter(array_map('trim', $parts));
        if (count($parts) !== 3 || implode('.', $parts) !== $token) {
            return false;
        }

        return true;
    }
}