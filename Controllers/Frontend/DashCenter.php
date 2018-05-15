<?php

class Shopware_Controllers_Frontend_DashCenter extends Shopware_Controllers_Frontend_Account
{


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

        $logs = $this->get('db')->fetchAll(
            'SELECT * FROM moj_dash_log WHERE button_id = :buttonid ORDER BY log_Date DESC ', ['buttonid' => $button->getId()]);

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
        }

        $buttonCollector = $this->get('moj_dash_button.services.dash_button.db_collector');

        var_dump($this->Request()->getPost());

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
            $button->setOrdernumber($this->Request()->get('ordernumber'));
            $button->setQuantity($this->Request()->get('quantity'));
            $button->setUserId($this->getUserId());

            $em = $this->get('models');

            $em->persist($button);
            $em->flush($button);

            $this->get('moj_dash_button.services.core.logger')->log('buttonSave', $button,
                sprintf('Button successfully saved (%s, %d)', $button->getOrdernumber(), $button->getQuantity())
            );

            $this->redirect([
                'controller' => 'DashCenter',
                'action' => 'editButton',
                'buttoncode' => $button->getButtonCode()
            ]);
        }

        $this->exitOnError('registerButton');
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