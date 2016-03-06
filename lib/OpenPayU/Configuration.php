<?php

/**
 * OpenPayU Standard Library
 *
 * @copyright  Copyright (c) 2011-2015 PayU
 * @license    http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 * http://www.payu.com
 * http://developers.payu.com
 */

class OpenPayU_Configuration
{
    private static $_availableEnvironment = array('custom', 'secure');
    private static $_availableHashAlgorithm = array('SHA', 'SHA-256', 'SHA-384', 'SHA-512');

    private static $env = 'secure';

    /**
     * Merchant Pos ID for Auth Basic
     * @deprecated deprecated since version 2.2 - use OAuth
     */
    private static $merchantPosId = '';

    /**
     * Signature Key for Auth Basic
     * @deprecated deprecated since version 2.2 - use OAuth
     */
    private static $signatureKey = '';

    /**
     * OAuth protocol - client_id
     */
    private static $oauthClientId = '';

    /**
     * OAuth protocol - client_secret
     */
    private static $oauthClientSecret = '';

    /**
     * OAuth protocol - endpoint address
     */
    private static $oauthEndpoint = '';

    private static $serviceUrl = '';
    private static $serviceDomain = '';
    private static $apiVersion = '2.1';
    private static $hashAlgorithm = 'SHA-256';

    private static $sender = 'Generic';

    const COMPOSER_JSON = "/composer.json";
    const DEFAULT_SDK_VERSION = 'PHP SDK 2.2.X-DEV / OAUTH';
    const OAUTH_CONTEXT = 'pl/standard/user/oauth/authorize';

    /**
     * @param string $version
     * @throws OpenPayU_Exception_Configuration
     */
    public static function setApiVersion($version)
    {
        if (empty($version)) {
            throw new OpenPayU_Exception_Configuration('Invalid API version');
        }

        self::$apiVersion = (string)$version;
    }

    /**
     * @return string
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * @param string
     * @throws OpenPayU_Exception_Configuration
     */
    public static function setHashAlgorithm($value)
    {
        if (!in_array($value, self::$_availableHashAlgorithm)) {
            throw new OpenPayU_Exception_Configuration('Hash algorithm "' . $value . '"" is not available');
        }

        self::$hashAlgorithm = $value;
    }

    /**
     * @return string
     */
    public static function getHashAlgorithm()
    {
        return self::$hashAlgorithm;
    }

    /**
     * @param string $environment
     * @param string $domain
     * @param string $api
     * @param string $version
     * @throws OpenPayU_Exception_Configuration
     */
    public static function setEnvironment($environment = 'secure', $domain = 'payu.com', $api = 'api/', $version = 'v2_1/')
    {
        $environment = strtolower($environment);
        $domain = strtolower($domain) . '/';

        if (!in_array($environment, self::$_availableEnvironment)) {
            throw new OpenPayU_Exception_Configuration($environment . ' - is not valid environment');
        }

        if ($environment == 'secure') {
            self::$env = $environment;
            self::$serviceDomain = $domain;
            self::$serviceUrl = 'https://' . $environment . '.' . $domain . $api . $version;
            self::$oauthEndpoint = 'https://' . $environment . '.' . $domain . self::OAUTH_CONTEXT;
        } else if ($environment == 'custom') {
            self::$env = $environment;
            self::$serviceUrl = $domain . $api . $version;
            self::$oauthEndpoint = $domain . self::OAUTH_CONTEXT;
        }
    }

    /**
     * @return string
     */
    public static function getServiceUrl()
    {
        return self::$serviceUrl;
    }

    /**
     * @return string
     */
    public static function getOauthEndpoint()
    {
        return self::$oauthEndpoint;
    }

    /**
     * @return string
     */
    public static function getEnvironment()
    {
        return self::$env;
    }

    /**
     * @param string
     * @deprecated deprecated since version 2.2 - use OAuth
     */
    public static function setMerchantPosId($value)
    {
        self::$merchantPosId = trim($value);
    }

    /**
     * @return string
     * @deprecated deprecated since version 2.2 - use OAuth
     */
    public static function getMerchantPosId()
    {
        return self::$merchantPosId;
    }

    /**
     * @param string
     * @deprecated deprecated since version 2.2 - use OAuth
     */
    public static function setSignatureKey($value)
    {
        self::$signatureKey = trim($value);
    }

    /**
     * @deprecated deprecated since version 2.2 - use OAuth
     * @return string
     */
    public static function getSignatureKey()
    {
        return self::$signatureKey;
    }

    /**
     * @return string
     */
    public static function getOauthClientId()
    {
        return self::$oauthClientId;
    }

    /**
     * @return string
     */
    public static function getOauthClientSecret()
    {
        return self::$oauthClientSecret;
    }

    /**
     * @param mixed $oauthClientId
     */
    public static function setOauthClientId($oauthClientId)
    {
        self::$oauthClientId = $oauthClientId;
    }

    /**
     * @param mixed $oauthClientSecret
     */
    public static function setOauthClientSecret($oauthClientSecret)
    {
        self::$oauthClientSecret = $oauthClientSecret;
    }


    /**
     * @param string $sender
     */
    public static function setSender($sender)
    {
        self::$sender = $sender;
    }

    /**
     * @return string
     */
    public static function getSender()
    {
        return self::$sender;
    }

    /**
     * @return string
     */
    public static function getFullSenderName()
    {
        return sprintf("%s@%s", self::getSender(), self::getSdkVersion());
    }

    /**
     * @return string
     */
    public static function getSdkVersion()
    {
        $composerFilePath = self::getComposerFilePath();
        if (file_exists($composerFilePath)) {
            $fileContent = file_get_contents($composerFilePath);
            $composerData = json_decode($fileContent);
            if (isset($composerData->version) && isset($composerData->extra[0]->engine)) {
                return sprintf("%s %s", $composerData->extra[0]->engine, $composerData->version);
            }
        }

        return self::DEFAULT_SDK_VERSION;
    }

    /**
     * @return string
     */
    private static function getComposerFilePath()
    {
        return realpath(dirname(__FILE__)) . '/../../' . self::COMPOSER_JSON;
    }
}
