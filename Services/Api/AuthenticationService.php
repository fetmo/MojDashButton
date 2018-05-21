<?php

namespace MojDashButton\Services\Api;

use MojDashButton\Services\Core\Logger;

class AuthenticationService
{

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $tokenSecret = 'TOPSECRET';

    /**
     * @var array
     */
    private $tokenSave;

    /**
     * AuthenticationService constructor.
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param Logger $logger
     */
    public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db, Logger $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->tokenSave = [];
    }

    /**
     * @param $buttonCode
     * @return string
     * @throws \Exception
     */
    public function generateToken($buttonCode)
    {
        $secretSha = $this->getSecret();

        $token = $this->db->fetchOne(
            'SELECT ' . $secretSha . ' as token FROM moj_dash_button WHERE button_code = :buttoncode',
            ['buttoncode' => $buttonCode]);

        if (!$token) {
            $this->logger->log('tokenGenerationFailed', null, 'Token generation for button code "'.$buttonCode.'" failed');
            throw new \Exception('Button not registered');
        }
        $this->logger->log('tokenGenerationSucceeded', null, 'Token generation for button code "'.$buttonCode.'" was successful');

        return $token;
    }

    /**
     * @param $token
     * @return string
     */
    public function fetchToken($token)
    {
        if (null === $this->tokenSave[$token]) {
            $secretSha = $this->getSecret();

            $this->tokenSave[$token] = $this->db->fetchOne(
                'SELECT button_code as token FROM moj_dash_button WHERE ' . $secretSha . ' = :token',
                ['token' => $token]);
        }

        return (string)$this->tokenSave[$token];
    }

    /**
     * @param $token
     * @return bool
     * @throws \Exception
     */
    public function validateToken($token)
    {
        $validToken = ($this->fetchToken($token) !== '');

        if (!$validToken) {
            $this->logger->log('tokenValidationFailed', null, 'Token validation for token "'.$token.'" failed');
            throw new \Exception('Token invalid');
        }

        return $validToken;
    }

    /**
     * @return string
     */
    private function getSecret()
    {
        return 'SHA1(CONCAT("' . $this->tokenSecret . '", button_code, user_id, "' . $this->tokenSecret . '"))';
    }

}