<?php

use \MojDashButton\Services\Api\AuthenticationService;

class AuthenticationServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \MojDashButton\Services\Api\AuthenticationService
     */
    private $authenticationService;

    protected function setUp()
    {
        parent::setUp();

        $this->db = Shopware()->Db();
        $this->authenticationService = null;
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testAuthTokenGenerateNotSuccessfully()
    {
        $this->expectException(\Exception::class);

        $authenticationService = $this->getAuthenticationService();

        $authenticationService->generateToken('TEST123' . rand());
    }

    public function testAuthTokenFetchNotSuccessfully()
    {
        $authenticationService = $this->getAuthenticationService();

        $this->assertEmpty($authenticationService->fetchToken('TEST123' . rand()));
    }

    public function testAuthTokenValidateNotSuccessfully()
    {
        $this->expectException(\Exception::class);

        $authenticationService = $this->getAuthenticationService();

        $authenticationService->validateToken('TEST123' . rand());
    }

    public function testAuthTokenGenerateSuccessfully()
    {
        $buttonCode = time() . 'BUTTON';

        $this->createButton($buttonCode);

        $authenticationService = $this->getAuthenticationService();

        $authToken = $authenticationService->generateToken($buttonCode);

        $this->assertNotEmpty($authToken);

        return ['buttoncode' => $buttonCode, 'token' => $authToken];
    }

    /**
     * @depends testAuthTokenGenerateSuccessfully
     * @param $data
     */
    public function testFetchTokenSuccessfully($data)
    {
        $buttoncode = $data['buttoncode'];
        $token = $data['token'];

        $authenticationService = $this->getAuthenticationService();

        $this->assertSame($authenticationService->fetchToken($token), $buttoncode);

        return $token;
    }

    /**
     * @depends testFetchTokenSuccessfully
     * @param $token
     */
    public function testValidateTokenSuccessfully($token)
    {
        $authenticationService = $this->getAuthenticationService();

        $this->assertTrue($authenticationService->validateToken($token));

        $this->deleteButton();
    }

    private function generateLoggerMock()
    {
        $loggerMock = $this->getMockBuilder(\MojDashButton\Services\Core\Logger::class)
            ->disableOriginalConstructor()
            ->setMethods(['log'])
            ->getMock();

        return $loggerMock;
    }

    private function createButton($buttoncode)
    {
        $this->db
            ->insert(
                'moj_dash_button',
                [
                    'user_id' => -1,
                    'button_code' => $buttoncode
                ]
            );
    }

    private function deleteButton()
    {
        $this->db
            ->delete(
                'moj_dash_button',
                'user_id = -1'
            );
    }

    /**
     * @return \MojDashButton\Services\Api\AuthenticationService
     */
    private function getAuthenticationService()
    {
        if (null == $this->authenticationService) {
            $this->authenticationService = new AuthenticationService($this->db, $this->generateLoggerMock());
        }

        return $this->authenticationService;
    }

}