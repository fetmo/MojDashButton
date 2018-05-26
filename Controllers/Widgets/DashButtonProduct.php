<?php

class Shopware_Controllers_Widgets_DashButtonProduct extends Enlight_Controller_Action
{

    public function getProductRulesAction()
    {
        $positionId = $this->Request()->get('productPosition');

        if ($positionId === null) {
            return;
        }

        $position = $this->getPosition($positionId);
        $ruleHelper = $this->get('moj_dash_button.services.dash_button.product_rule_helper');
        $rules = $ruleHelper->getProductRules($position);

        $this->View()->assign([
            'configuredRules' => $rules,
            'rules' => $ruleHelper->getConfiguredRules(),
            'position' => $position
        ]);
    }

    public function saveProductRulesAction()
    {
        $ruleData = $this->Request()->getPost('rule', []);
        $positionId = $this->Request()->getPost('productid', 0);

        $position = $this->getPosition($positionId);

        $ruleHelper = $this->get('moj_dash_button.services.dash_button.product_rule_helper');

        $ruleHelper->createProductRule($position, $ruleData);

        $this->redirect([
            'controller' => 'DashCenter',
            'action' => 'editButton',
            'buttoncode' => $position->getButton()->getButtonCode()
        ]);
    }

    /**
     * @param $id
     * @return null|\MojDashButton\Models\DashButtonProduct
     */
    private function getPosition($id)
    {
        $repo = $this->get('models')
            ->getRepository(\MojDashButton\Models\DashButtonProduct::class);

        return $repo->find($id);
    }

}