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
namespace Anan\AwsCognito\Model\Config\Sources;

use Magento\Framework\Option\ArrayInterface;

class Region implements ArrayInterface
{
    /**
     * Constant representing Aws region options
     * @var array
     */
    const REGIONS = [
        ['US East (N. Virginia)', 'us-east-1'],
        ['US East (Ohio)', 'us-east-2'],
        ['US West (N. California)', 'us-west-1'],
        ['US West (Oregon)', 'us-west-2'],
        ['Africa (Cape Town)', 'af-south-1'],
        ['Asia Pacific (Hong Kong)', 'ap-east-1'],
        ['Asia Pacific (Jakarta)', 'ap-southeast-3'],
        ['Asia Pacific (Mumbai)', 'ap-south-1'],
        ['Asia Pacific (Osaka)', 'ap-northeast-3'],
        ['Asia Pacific (Seoul)', 'ap-northeast-2'],
        ['Asia Pacific (Singapore)', 'ap-southeast-1'],
        ['Asia Pacific (Sydney)', 'ap-southeast-2'],
        ['Asia Pacific (Tokyo)', 'ap-northeast-1'],
        ['Canada (Central)',  'ca-central-1'],
        ['Europe (Frankfurt)', 'eu-central-1'],
        ['Europe (Ireland)', 'eu-west-1'],
        ['Europe (London)', 'eu-west-2'],
        ['Europe (Milan)', 'eu-south-1'],
        ['Europe (Paris)', 'eu-west-3'],
        ['Europe (Stockholm)', 'eu-north-1'],
        ['Middle East (Bahrain)', 'me-south-1'],
        ['South America (São Paulo)', 'sa-east-1']
    ];

    /**
     * Get Aws region options
     * @return array
     */
    public function toOptionArray(): array
    {
        $result = [['value' => '', 'label' => __('-- Please Select --')]];
        foreach (self::REGIONS as $key => $value) {
            $result[($key + 1)] = ['value' => $value[1], 'label' => $value[0]];
        }
        return $result;
    }

}