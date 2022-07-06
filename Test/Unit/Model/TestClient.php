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
namespace Anan\AwsCognito\Test\Unit\Mode;

class TestClient extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up params
     */
    protected function setUp()
    {
        $this->username = '';
        $this->password = '';
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject("\Anan\AwsCognito\Model\Client");
    }

    /**
     * Test function authenticate
     */
    public function testAuthenticate()
    {
        $result = $this->model->authenticate($this->username, $this->password);
        $this->assertTrue($result instanceof \Aws\Result);
    }

    /**
     * Test function register
     */
    public function testRegister()
    {
        $result = $this->model->register($this->username, $this->password);
        $this->assertTrue($result instanceof \Aws\Result);
    }
}