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

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Anan\AwsCognito\Model\Config\Config;

class Client
{
    /**
     * Constant represent payload param
     * @var string
     */
    const AUTH_FLOW = 'AuthFlow';
    const AUTH_PARAMETERS = 'AuthParameters';
    const USERNAME = 'USERNAME';
    const PASSWORD = 'PASSWORD';
    const NEW_PASSWORD = 'NEW_PASSWORD';
    const SECRET_HASH = 'SECRET_HASH';
    const CLIENT_ID = 'ClientId';
    const USER_POOL_ID = 'UserPoolId';
    const USER_ATTRIBUTES = 'UserAttributes';
    const CLIENT_METADATA = 'ClientMetadata';
    const CONFIRMATION_CODE = 'ConfirmationCode';
    const TEMPORARY_PASSWORD = 'TemporaryPassword';
    const MESSAGE_ACTION = 'MessageAction';
    const DESIRE_DELIVERY_MEDIUM = 'DesiredDeliveryMediums';
    const SESSION = 'Session';
    const CHALLENGE_RESPONSES = 'ChallengeResponses';
    const CHALLENGE_NAME = 'ChallengeName';
    const PERMANENT = 'Permanent';
    const ACCESS_TOKEN = 'AccessToken';
    const PREVIOUS_PASSWORD = 'PreviousPassword';
    const PROPOSED_PASSWORD = 'ProposedPassword';

    /**
     * Constant represent response param
     * @var string
     */
    const USER_CONFIRMED = 'UserConfirmed';

    /**
     * Constant for attribute format
     * @var string
     */
    const NAME = 'Name';
    const VALUE = 'Value';
    const EMAIL = 'email';
    const EMAIL_VERIFIED = 'email_verified';

    /**
     * Constant represent cognito config param or value
     * @var string
     */
    const ADD_USER_DELIVERY_MEDIUMS = 'cognito.add_user_delivery_mediums';
    const DEFAULT = 'DEFAULT';
    const DELETE_USER = 'cognito.delete_user';

    /**
     * Constant representing the user needs a new password
     * @var string
     */
    const NEW_PASSWORD_REQUIRED = 'NEW_PASSWORD_REQUIRED';

    /**
     * Constant representing the user force to change password
     * @var string
     */
    const FORCE_CHANGE_PASSWORD = 'FORCE_CHANGE_PASSWORD';

    /**
     * Constant representing admin authentication flow
     * @var string
     */
    const ADMIN_NO_SRP_AUTH = 'ADMIN_NO_SRP_AUTH';

    /**
     * Cognito Client
     * @var \Aws\CognitoIdentityProvider\CognitoIdentityProviderClient
     */
    protected $client;

    /**
     * Cognito config
     * @var \Anan\AwsCognito\Model\Config\Config
     */
    protected $config;

    /**
     * Cognito Client ID
     * @var string
     */
    protected $clientId;

    /**
     * Cognito Client Secret
     * @var string
     */
    protected $clientSecret;

    /**
     * Cognitor Pool ID
     * @var string
     */
    protected $poolId;

    /**
     * @var bool
     */
    protected $boolClientSecret;

    /**
     * Constructor
     * @param \Aws\CognitoIdentityProvider\CognitoIdentityProviderClient $client
     * @param \Anan\AwsCognito\Model\Config\Config $config
     * @param boolean $boolClientSecret
     */
    public function __construct(
        CognitoIdentityProviderClient $client,
        Config $config,
        bool $boolClientSecret = true
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->clientId = $this->config->getAppClientId();
        $this->clientSecret = $this->config->getAppClientSecret();
        $this->poolId = $this->config->getUserPoolId();
        $this->boolClientSecret = $boolClientSecret;
    }

    /**
     * Check if user credentials valid
     * @param string $email
     * @param string $password
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function authenticate(string $email, string $password): \Aws\Result
    {
        try {
            $payload = [
                self::AUTH_FLOW => self::ADMIN_NO_SRP_AUTH,
                self::AUTH_PARAMETERS => [
                    self::USERNAME => $username,
                    self::PASSWORD => $password
                ],
                self::CLIENT_ID => $this->clientId,
                self::USER_POOL_ID => $this->poolId,
            ];

            if ($this->boolClientSecret) {
                $payload[self::AUTH_PARAMETERS] = array_merge($payload[self::AUTH_PARAMETERS], [
                    self::SECRET_HASH => $this->cognitoSecretHash($username)
                ]);
            }

            $response = $this->client->adminInitiateAuth($payload);
        } catch (CognitoIdentityProviderException $exception) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $response;
    }

    /**
     * Registers a user in the given user pool.
     * @param string $username
     * @param string $password
     * @param array $attributes
     * @return bool
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function register(string $username, string $password, array $attributes = []): bool
    {
        try {
            $payload = [
                self::CLIENT_ID => $this->clientId,
                \ucfirst(\strtolower(self::PASSWORD)) => $password,
                self::USER_ATTRIBUTES => $this->formatAttributes($attributes),
                \ucfirst(\strtolower(self::USERNAME)) => $username,
            ];

            if ($this->boolClientSecret) {
                $payload = array_merge($payload, [
                    \ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase(\strtolower(self::SECRET_HASH)))
                         => $this->cognitoSecretHash($username)
                ]);
            }

            $response = $this->client->signUp($payload);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return (bool)$response[self::USER_CONFIRMED];
    }

    /**
     * Send a password reset code to a user
     * @param string $username
     * @param array $clientMetadata
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function sendResetLink(string $username, array $clientMetadata = []): \Aws\Result
    {
        try {
            $payload = [
                self::CLIENT_ID => $this->clientId,
                self::CLIENT_METADATA => $this->buildClientMetadata(
                    [\strtolower(self::USERNAME) => $username], 
                    $clientMetadata
                ),
                \ucfirst(\strtolower(self::USERNAME)) => $username,
            ];

            if ($this->boolClientSecret) {
                $payload = array_merge($payload, [
                    \ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase(\strtolower(self::SECRET_HASH)))
                        => $this->cognitoSecretHash($username)
                ]);
            }

            $result = $this->client->forgotPassword($payload);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Reset a users password based on reset code
     * @param string $code
     * @param string $username
     * @param string $password
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function resetPassword(string $code, string $username, string $password): \Aws\Result
    {
        try {
            $payload = [
                self::CLIENT_ID => $this->clientId,
                self::CONFIRMATION_CODE => $code,
                \ucfirst(\strtolower(self::PASSWORD)) => $password,
                \ucfirst(\strtolower(self::USERNAME)) => $username,
            ];

            if ($this->boolClientSecret) {
                $payload = array_merge($payload, [
                    \ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase(\strtolower(self::SECRET_HASH)))
                        => $this->cognitoSecretHash($username)
                ]);
            }

            $result = $this->client->confirmForgotPassword($payload);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Register a user and send them an email to set their password
     * @param string $username
     * @param string $password
     * @param array $attributes
     * @param array $clientMetadata
     * @param string $messageAction
     * @param bool $isUserEmailForcedVerified
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function inviteUser(
        string $username, 
        string $password = '', 
        array $attributes = [], 
        array $clientMetadata = [],
        string $messageAction = '',
        bool $isUserEmailForcedVerified = false
    ) {
        if ($attributes[self::EMAIL] && $isUserEmailForcedVerified) {
            $attributes[self::EMAIL_VERIFIED] = 'true';
        }

        $payload = [
            self::USER_POOL_ID => $this->poolId,
            \ucfirst(\strtolower(self::USERNAME)) => $username,
            self::USER_ATTRIBUTES => $this->formatAttributes($attributes)
        ];

        if (!empty($clientMetadata)) {
            $payload[self::CLIENT_METADATA] = $this->buildClientMetadata([], $clientMetadata);
        }

        if (!empty($password)) {
            $payload[self::TEMPORARY_PASSWORD] = $password;
        }

        if (!empty($messageAction)) {
            $payload[self::MESSAGE_ACTION] = $messageAction;
        }

        if (config(self::ADD_USER_DELIVERY_MEDIUM) != self::DEFAULT) {
            $payload[self::DESIRE_DELIVERY_MEDIUM] = [
                config(self::ADD_USER_DELIVERY_MEDIUM)
            ];
        }

        try {
            $result = $this->client->adminCreateUser($payload);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Set a new password for a user that has been flagged as needing a password change
     * @param string $username
     * @param string $password
     * @param string $session
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function confirmPassword(string $username, string $password, string $session): \Aws\Result
    {
        try {
            $payload = [
                self::CLIENT_ID => $this->clientId,
                self::USER_POOL_ID => $this->poolId,
                self::SESSION => $session,
                self::CHALLENGE_RESPONSES => [
                    self::NEW_PASSWORD => $password,
                    self::USERNAME => $username
                ],
                self::CHALLENGE_NAME => self::NEW_PASSWORD_REQUIRED,
            ];

            if ($this->boolClientSecret) {
                $payload[self::CHALLENGE_RESPONSES] = array_merge($payload[self::CHALLENGE_RESPONSES], [
                    self::SECRET_HASH => $this->cognitoSecretHash($username)
                ]);
            }

            $result = $this->client->AdminRespondToAuthChallenge($payload);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Delete user
     * @param string $username
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function deleteUser(string $username): \Aws\Result
    {
        try {
            if (config(self::DELETE_USER)) {
                $result = $this->client->adminDeleteUser([
                    self::USER_POOL_ID => $this->poolId,
                    \ucfirst(\strtolower(self::USERNAME)) => $username,
                ]);
            }
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Sets the specified user's password in a user pool as an administrator
     * @param string $username
     * @param string $password
     * @param bool $permanent
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function setUserPassword(string $username, string $password, bool $permanent = true): \Aws\Result
    {
        try {
            $result = $this->client->adminSetUserPassword([
                \ucfirst(\strtolower(self::PASSWORD)) => $password,
                self::PERMANENT => $permanent,
                \ucfirst(\strtolower(self::USERNAME)) => $username,
                self::USER_POOL_ID => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Changes the password for a specified user in a user pool
     * @param string $accessToken
     * @param string $passwordOld
     * @param string $passwordNew
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function changePassword(string $accessToken, string $passwordOld, string $passwordNew): \Aws\Result
    {
        try {
            $result = $this->client->changePassword([
                self::ACCESS_TOKEN => $accessToken,
                self::PREVIOUS_PASSWORD => $passwordOld,
                self::PROPOSED_PASSWORD => $passwordNew
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Invalidate user password
     * @param string $username
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function invalidatePassword(string $username): \Aws\Result
    {
        try {
            $result =  $this->client->adminResetUserPassword([
                self::USER_POOL_ID => $this->poolId,
                \ucfirst(\strtolower(self::USERNAME)) => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Sign Up confirmation
     * @param string $username
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function confirmSignUp(string $username): \Aws\Result
    {
        try {
            $result = $this->client->adminConfirmSignUp([
                self::USER_POOL_ID => $this->poolId,
                \ucfirst(\strtolower(self::USERNAME)) => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * User sign up confirmation
     * @param string $username
     * @param string $confirmationCode
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function confirmUserSignUp(string $username, string $confirmationCode): \Aws\Result
    {
        try {
            $result = $this->client->confirmSignUp([
                self::CLIENT_ID => $this->clientId,
                \ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase(\strtolower(self::SECRET_HASH)))
                    => $this->cognitoSecretHash($username),
                \ucfirst(\strtolower(self::USERNAME)) => $username,
                self::CONFIRMATION_CODE => $confirmationCode,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Resend user token
     * @param string $username
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function resendToken(string $username): \Aws\Result
    {
        try {
            $result = $this->client->resendConfirmationCode([
                self::CLIENT_ID => $this->clientId,
                \ucfirst(SimpleDataObjectConverter::snakeCaseToCamelCase(\strtolower(self::SECRET_HASH)))
                    => $this->cognitoSecretHash($username),
                \ucfirst(\strtolower(self::USERNAME)) => $username
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }
        return $result;
    }

    /**
     * Set user attributes
     * @param string $username
     * @param array $attributes
     * @return \Aws\Result
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    public function setUserAttributes(string $username, array $attributes): \Aws\Result
    {
        try {
            $result = $this->client->AdminUpdateUserAttributes([
                \ucfirst(\strtolower(self::USERNAME)) => $username,
                self::USER_POOL_ID => $this->poolId,
                self::USER_ATTRIBUTES => $this->formatAttributes($attributes),
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw new Exception(__((\json_encode([
                $e->getAwsErrorCode() => $e->getAwsErrorMessage()
            ]))??Exception::MESSAGE));
        }

        return $result;
    }

    /**
     * Creates the Cognito secret hash.
     * @param string $username
     * @return string
     */
    protected function cognitoSecretHash(string $username): string
    {
        return $this->hash($username . $this->clientId);
    }


    /**
     * Creates a has HMAC from a string.
     * @param string $message
     * @return string
     */
    protected function hash(string $message): string
    {
        return \base64_encode(\hash_hmac('sha256', $message, $this->clientSecret, true));
    }

    /**
     * Format attributes in Name/Value array.
     * @param array $attributes
     * @return array
     */
    protected function formatAttributes(array $attributes): array
    {
        $userAttributes = [];
        foreach ($attributes as $key => $value) {
            $userAttributes[] = [ self::NAME => $key, self::VALUE => $value ];
        }

        return $userAttributes;
    }

    /**
     * Build Client Metadata to be forwarded to Cognito.
     * @param array $attributes
     * @param array $clientMetadata
     * @return array
     */
    protected function buildClientMetadata(array $attributes, array $clientMetadata = []): array
    {
        $userAttributes = $attributes;
        if (!empty($clientMetadata)) {
            $userAttributes = array_merge($userAttributes, $clientMetadata);
        }

        return $userAttributes;
    }
}