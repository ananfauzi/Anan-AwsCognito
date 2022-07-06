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
namespace Anan\AwsCognito\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    /**
     * Constant representing system.xml path
     * @var string
     */
    const AWS_KEY_PATH = 'anan_awscognito/credentials/aws_key';
    const AWS_SECRET_PATH = 'anan_awscognito/credentials/aws_secret';
    const REGION_PATH = 'anan_awscognito/general/region';
    const VERSION_PATH = 'anan_awscognito/general/version';
    const APP_CLIENT_ID_PATH = 'anan_awscognito/general/app_client_id';
    const APP_CLIENT_SECRET_PATH = 'anan_awscognito/general/app_client_secret';
    const USER_POOL_ID_PATH = 'anan_awscognito/general/user_pool_id';

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get value from path on system.xml
     * @param  string $path
     * @return string
     */
    public function getValue(string $path): string
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get aws_key
     * @return string
     */
    public function getAwsKey(): string
    {
        return $this->getValue(self::AWS_KEY_PATH);
    }

    /**
     * Get aws_secret
     * @return string
     */
    public function getAwsSecret(): string
    {
        return $this->getValue(self::AWS_SECRET_PATH);
    }

    /**
     * Get region
     * @return string
     */
    public function getRegion(): string
    {
        return $this->getValue(self::REGION_PATH);
    }

    /**
     * Get version
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getValue(self::VERSION_PATH);
    }

    /**
     * Get app_client_id
     * @return string
     */
    public function getAppClientId(): string
    {
        return $this->getValue(self::APP_CLIENT_ID_PATH);
    }

    /**
     * Get app_client_secret
     * @return string
     */
    public function getAppClientSecret(): string
    {
        return $this->getValue(self::APP_CLIENT_SECRET_PATH);
    }

    /**
     * Get user_pool_id
     * @return string
     */
    public function getUserPoolId(): string
    {
        return $this->getValue(self::USER_POOL_ID_PATH);
    }

}