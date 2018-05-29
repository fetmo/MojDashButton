<?php

class Shopware_Controllers_Frontend_DashCenter extends Shopware_Controllers_Frontend_Account
{

    public function toggleDirectOrderAction()
    {
        $db = $this->get('db');

        $success = $db->executeUpdate(
            'UPDATE s_user_attributes SET moj_dash_button_directorder = !moj_dash_button_directorder WHERE id = :id',
            ['id' => $this->getUserId()]
        );


        $this->redirect([
            'controller'=> 'DashCenter',
            'success' => $success
        ]);
    }

    public function buttonOverviewAction()
    {
        $buttonCollector = $this->get('moj_dash_button.services.dash_button.db_collector');

        $userData = $this->View()->getAssign('sUserData');

        $this->View()->assign('buttons', $buttonCollector->collectButtonForUser($userData['additional']['user']['userID']));
    }

    public function registerButtonAction()
    {

    }

    public function editButtonAction()
    {
        $buttonCode = $this->Request()->get('buttoncode');

        if (null === $buttonCode) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'buttonOverview'
            ]);
            return;
        }

        $buttonCollector = $this->get('moj_dash_button.services.dash_button.db_collector');
        $button = $buttonCollector->collectButton($buttonCode);

        if ($button->getUserId() !== $this->getUserId()) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'buttonOverview'
            ]);
            return;
        }

        $logs = $this->get('moj_dash_button.services.core.logger')->collectLog($button);

        $basketHandler = $this->get('moj_dash_button.services.dash_button.basket_handler');
        $products = $basketHandler->getProductsForButton($button);

        $this->View()->assign('products', $products);
        $this->View()->assign('logs', $logs);
        $this->View()->assign('button', $button);
    }

    public function saveButtonAction()
    {
        if (!$this->Request()->isPost()) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'registerButton'
            ]);
            return;
        }

        $buttonCollector = $this->get('moj_dash_button.services.dash_button.db_collector');

        $buttonCode = $this->Request()->get('buttoncode');
        $buttonID = $this->Request()->get('buttonid');
        $button = null;

        $this->View()->assign('buttoncode', $buttonCode);

        if ($buttonCode === '') {
            $this->exitOnError('registerButton');
        }

        if ($buttonID === null) {
            try {
                $registerService = $this->get('moj_dash_button.services.dash_button.db_register_service');
                $button = $registerService->registerButton($buttonCode);
            } catch (\Exception $exception) {
                $this->exitOnError('registerButton');
            }

        } else {
            $button = $buttonCollector->collectButton($buttonCode);
        }

        if ($button) {
            $productmode =
                $this->Request()->get('productmode') === null
                    ? \MojDashButton\Models\DashButton::SINGLEPRODUCTMODE
                    : \MojDashButton\Models\DashButton::MULTIPRODUCTMODE;

            $button->setProductMode((int)$productmode);
            $button->setUserId($this->getUserId());

            $this->saveProductPositions($button, (array)$this->Request()->get('products'));

            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'editButton',
                'buttoncode' => $button->getButtonCode()
            ]);
        }

        $this->exitOnError('registerButton');
    }

    public function removeButtonOverlayAction()
    {
        $buttonCode = $this->Request()->get('buttoncode');
        $this->View()->assign('buttoncode', $buttonCode);
    }

    public function removeButtonAction()
    {
        if (!$this->Request()->isPost()) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'registerButton'
            ]);
            return;
        }

        $buttonCode = $this->Request()->get('buttoncode');

        if (!$buttonCode) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'buttonOverview'
            ]);
        }

        $buttonCollector = $this->get('moj_dash_button.services.dash_button.db_collector');
        $button = $buttonCollector->collectButton($buttonCode);

        if ($button->getUserId() !== $this->getUserId()) {
            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'buttonOverview'
            ]);
        }

        $em = $this->get('models');

        $em->remove($button);
        $em->flush($button);

        $this->redirect([
            'controller' => 'DashCenter',
            'action' => 'buttonOverview'
        ]);
    }

    private function saveProductPositions(\MojDashButton\Models\DashButton $button, array $productPositions)
    {
        $em = $this->get('models');

        if ($button->getProductMode() === \MojDashButton\Models\DashButton::SINGLEPRODUCTMODE && count($productPositions) > 0){
            $productPositions = [$productPositions[0]];
        }

        /** @var \MojDashButton\Models\Repository\DashButton $repository */
        $repository = $em->getRepository(\MojDashButton\Models\DashButton::class);
        $repository
            ->setLogger($this->get('moj_dash_button.services.core.logger'))
            ->saveProductPositions($button, $productPositions);
    }

    private function exitOnError($action)
    {
        $this->View()->assign('hasError', true);
        $this->forward($action);
        return;
    }

    private function getUserId()
    {
        $userData = $this->View()->getAssign('sUserData');

        return (int)$userData['additional']['user']['userID'];
    }

}