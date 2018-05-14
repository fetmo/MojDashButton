<?php

class Shopware_Controllers_Frontend_DashAuthentication extends Shopware_Controllers_Api_Rest
{

    public function preDispatch()
    {
        parent::preDispatch();
        $actionName = $this->Request()->getActionName();
        $this->View()->setTemplate();

        if($actionName !== 'authButton'){
            $this->forward('authButton');
        }
    }

    public function authButtonAction()
    {
        $authenticationService = $this->get('moj_dash_button.services.api.authentication_service');

        $buttonCode = $this->Request()->get('buttoncode');

        if(null === $buttonCode){
            throw new \Exception('Parameter "buttoncode" missing');
        }

        $token = $authenticationService->generateToken($buttonCode);

        $this->View()->assign('token', $token);
    }
}