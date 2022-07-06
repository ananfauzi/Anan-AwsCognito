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
namespace Anan\AwsCognito\Model;


class Exception extends  \Magento\Framework\Exception\LocalizedException
{
    /**
     * Constant representing aws cognito exception code
     * @var string
     */
    const ALIAS_EXISTS = 'AliasExistsException';
    const CODE_DELIVERY_FAILURE = 'CodeDeliveryFailureException';
    const CONCURRENT_MODIFICATION = 'ConcurrentModificationException';
    const DUPLICATE_PROVIDER = 'DuplicateProviderException';
    const ENABLE_SOFTWARE_TOKEN_MFA = 'EnableSoftwareTokenMFAException';
    const GROUP_EXISTS = 'GroupExistsException';
    const INTERNAL_ERROR = 'InternalErrorException';
    const INVALID_EMAIL_ROLE_ACCESS_POLICY = 'InvalidEmailRoleAccessPolicyException';
    const INVALID_LAMBDA_RESPONSE = 'InvalidLambdaResponseException';
    const INVALID_OAUTH_FLOW = 'InvalidOAuthFlowException';
    const INVALID_PARAMETER= 'InvalidParameterException';
    const INVALID_SMS_ROLE_ACCESS_POLICY = 'InvalidSmsRoleAccessPolicyException';
    const INVALID_SMS_ROLE_TRUST_RELATIONSHIP = 'InvalidSmsRoleTrustRelationshipException';
    const INVALID_USER_POOL_CONFIGURATION = 'InvalidUserPoolConfigurationException';
    const LIMIT_EXCEEDED = 'LimitExceededException';
    const MFA_METHOD_NOT_FOUND = 'MFAMethodNotFoundException';
    const NOT_AUTHORIZED= 'NotAuthorizedException';
    const PRECONDITION_NOT_MET = 'PreconditionNotMetException';
    const RESOURCE_NOT_FOUND = 'ResourceNotFoundException';
    const SCOPE_DOES_NOT_EXIST = 'ScopeDoesNotExistException';
    const SOFTWARE_TOKEN_MFA_NOT_FOUND = 'SoftwareTokenMFANotFoundException';
    const TOO_MANY_FAILED_ATTEMPTS = 'TooManyFailedAttemptsException';
    const TOO_MANY_REQUESTS = 'TooManyRequestsException';
    const UNAUTHORIZED = 'UnauthorizedException';
    const UNEXPECTED_LAMBDA = 'UnexpectedLambdaException';
    const UNSUPPORTED_IDENTITY_PROVIDER = 'UnsupportedIdentityProviderException';
    const UNSUPPORTED_OPERATION = 'UnsupportedOperationException';
    const UNSUPPORTED_TOKEN_TYPE = 'UnsupportedTokenTypeException';
    const UNSUPPORTED_USER_STATE = 'UnsupportedUserStateException';
    const USER_IMPORT_IN_PROGRESS = 'UserImportInProgressException';
    const USER_LAMBDA_VALIDATION = 'UserLambdaValidationException';
    const USER_NOT_CONFIRMED = 'UserNotConfirmedException';
    const USER_POOL_ADD_ON_NOT_ENABLED = 'UserPoolAddOnNotEnabledException';
    const USER_POOL_TAGGING = 'UserPoolTaggingException';
    const PASSWORD_RESET_REQUIRE = 'PasswordResetRequireException';
    const USER_NOT_FOUND = 'UserNotFoundException';
    const USERNAME_EXISTS = 'UsernameExistException';
    const INVALID_PASSWORD = 'InvalidPasswordException';
    const CODE_MISMATCH = 'CodeMismatchException';
    const EXPIRED_CODE = 'ExpiredCodeException';

    /**
     * Constant representing list of aws cognito error code
     * @var array
     */
    const ERROR_CODES_LIST = [
        self::ALIAS_EXISTS,
        self::CODE_DELIVERY_FAILURE,
        self::CONCURRENT_MODIFICATION,
        self::DUPLICATE_PROVIDER,
        self::ENABLE_SOFTWARE_TOKEN_MFA,
        self::GROUP_EXISTS,
        self::INTERNAL_ERROR,
        self::INVALID_EMAIL_ROLE_ACCESS_POLICY,
        self::INVALID_LAMBDA_RESPONSE,
        self::INVALID_OAUTH_FLOW,
        self::INVALID_PARAMETER,
        self::INVALID_SMS_ROLE_ACCESS_POLICY,
        self::INVALID_SMS_ROLE_TRUST_RELATIONSHIP,
        self::INVALID_USER_POOL_CONFIGURATION,
        self::LIMIT_EXCEEDED,
        self::MFA_METHOD_NOT_FOUND,
        self::NOT_AUTHORIZED,
        self::PRECONDITION_NOT_MET,
        self::RESOURCE_NOT_FOUND,
        self::SCOPE_DOES_NOT_EXIST,
        self::SOFTWARE_TOKEN_MFA_NOT_FOUND,
        self::TOO_MANY_FAILED_ATTEMPTS,
        self::TOO_MANY_REQUESTS,
        self::UNAUTHORIZED,
        self::UNEXPECTED_LAMBDA,
        self::UNSUPPORTED_IDENTITY_PROVIDER,
        self::UNSUPPORTED_OPERATION,
        self::UNSUPPORTED_TOKEN_TYPE,
        self::UNSUPPORTED_USER_STATE,
        self::USER_IMPORT_IN_PROGRESS,
        self::USER_LAMBDA_VALIDATION,
        self::USER_NOT_CONFIRMED,
        self::USER_POOL_ADD_ON_NOT_ENABLED,
        self::USER_POOL_TAGGING,
        self::PASSWORD_RESET_REQUIRE,
        self::USER_NOT_FOUND,
        self::USERNAME_EXISTS,
        self::INVALID_PASSWORD,
        self::CODE_MISMATCH,
        self::EXPIRED_CODE,
    ];

    /**
     * Constant exception message
     * @var string
     */
    const MESSAGE = 'Cognito system went wrong, please try again later';

    /**
     * Is cognito error code
     * @param string
     * @return bool
     */
    public function isCognitoErrorCode(string $code): bool
    {
        return (\in_array($code, self::ERROR_CODES_LIST));
    }

    /**
     * Get exception message as array
     * @param string $message
     * @return array
     */
    public function getExceptionMessageArray(string $message): array
    {
        try {
            $result = \json_decode($message);
        } catch (\Exception $e) {
            $result = [$message];
        }

        if (\json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        return [$message];
    }
}