<?php

class Shopware_Controllers_Frontend_DashButton extends Shopware_Controllers_Api_Rest implements \Shopware\Components\CSRFWhitelistAware
{

    /**
     * @var \MojDashButton\Services\Api\AuthenticationService
     */
    private $authenticationService;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \MojDashButton\Services\DashButton\ButtonService
     */
    private $buttonService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->authenticationService = $this->get('moj_dash_button.services.api.authentication_service');
        $token = $this->Request()->get('token');

        if (null === $token || false === $this->authenticationService->validateToken($token)) {
            throw new \Exception('Token Validation mismatch');
        }

        $this->token = $token;
        $this->buttonService = $this->get('moj_dash_button.services.dash_button.button_service');
    }

    public function getWhitelistedCSRFActions()
    {
        return [
            'getProduct',
            'triggerClick'
        ];
    }


    public function getProductAction()
    {
        $success = true;

        try {
            $productData = $this->buttonService->getProduct($this->token);
        } catch (\Exception $exception) {
            $productData = [];
            $success = false;
        }

        $this->View()->assign('product', $productData);
        $this->View()->assign('token', $this->token);
        $this->View()->assign('success', $success);
    }

    public function triggerClickAction()
    {
        if (!$this->Request()->isPost()) {
            return;
        }

        try {
            $success = $this->buttonService->triggerClick($this->token, $this->Request()->getPost('identifier', ''));
        } catch (\Exception $exception) {
            $success = false;
        }

        $this->View()->assign('token', $this->token);
        $this->View()->assign('success', $success);
    }
}