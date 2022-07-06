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

class Data
{
    /**
     * Constant representing \Aws\Result key
     * @var string
     */
    const AUTHENTICATION_RESULT = 'AuthenticationResult';

    /**
     * Constant representing message
     * @var string
     */
    const INVALID_RESULT = 'Aws\Result data is invalid';
    const INVALID_AUTHENTICATION_RESULT = 'Malformed AWS Authentication Result';

    /**
     * @var \Aws\Result
     */
    protected $result;

    /**
     * Set result
     * @param \Aws\Result $result
     * @return void
     */
    public function setResult(\Aws\Result $result): void
    {
        $this->result = $result;
    }

    /**
     * Get result
     * @return \Aws\Result
     * @throw  \Magento\Framework\Exception\LocalizedException
     */
    public function getResult(): \Aws\Result
    {
        if ($this->result instanceof \Aws\Result) {
            return $this->result;
        }

        throw new \Anan\AwsCognito\Model\Exception(__(self::INVALID_RESULT));
    }

    /**
     * Get Aws Auth Result
     * @return array
     * @throw  \Magento\Framework\Exception\LocalizedException
     */
    public function getAuthResult(): array
    {
        $authResult = $this->getResult()[self::AUTHENTICATION_RESULT];
        if (!is_array($authResult)) {
            throw new \Anan\AwsCognito\Model\Exception(
                self::INVALID_AUTHENTICATION_RESULT, 400
            );
        }

        return $authResult;
    }

    /**
     * Get token
     * @return string
     */
    public function getToken(): string
    {
        return $this->getAuthResult()[\Anan\AwsCognito\Model\Client::ACCESS_TOKEN];
    }
}