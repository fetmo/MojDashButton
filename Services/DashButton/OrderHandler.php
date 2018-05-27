<?php

namespace MojDashButton\Services\DashButton;


use MojDashButton\Components\Rules\RuleInterface;
use MojDashButton\Models\DashButtonProduct;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class OrderHandler
 * @package MojDashButton\Services\DashButton
 */
class OrderHandler
{

    /**
     * @var \sBasket
     */
    private $basket;

    /**
     * @var \sAdmin
     */
    private $admin;

    /**
     * @var \Shopware_Components_Modules
     */
    private $modules;

    /**
     * @var array
     */
    private $oldbasket;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \Shopware_Components_Config
     */
    private $config;

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * OrderHandler constructor.
     * @param \Shopware_Components_Modules $modules
     * @param Container $container
     * @param \Enlight_Components_Session_Namespace $session
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param \Shopware_Components_Config $config
     */
    public function __construct(\Shopware_Components_Modules $modules, Container $container, \Enlight_Components_Session_Namespace $session,
                                \Enlight_Components_Db_Adapter_Pdo_Mysql $db, \Shopware_Components_Config $config)
    {
        $this->modules = $modules;

        $this->container = $container;

        $this->basket = $modules->Basket();
        $this->admin = $modules->Admin();

        $this->db = $db;
        $this->config = $config;

        $this->session = $session;
    }

    /**
     * @param $products
     * @param $userID
     * @param bool $auto
     * @return bool|string
     */
    public function createOrder(array $products, $userID, $auto = false)
    {
        $this->session->offsetSet('sUserId', $userID);

        if ($auto) {
            $products = $this->filterOnRules($products);
        }

        if(count($products) === 0){
            return false;
        }

        $this->clearBasket();

        $this->fillBasket($products);

        $view = new \stdClass();
        $view->sUserData = $this->getUserData();
        $view->sBasket = $this->getBasket($view->sUserData);

        $ordernumber = $this->saveOrder($view, $this->session->offsetGet('sDispatch'));

        $this->resetBasket();

        return $ordernumber;
    }

    /**
     * @return array|false
     */
    private function getUserData()
    {
        $system = $this->get('system');
        $userData = $this->admin->sGetUserData();
        if (!empty($userData['additional']['countryShipping'])) {
            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow('
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ', [$system->sUSERGROUP]);

            if ($this->isTaxFreeDelivery($userData)) {
                $system->sUSERGROUPDATA['tax'] = 0;
                $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
                $this->session->sUserGroupData = $system->sUSERGROUPDATA;
                $userData['additional']['charge_vat'] = false;
                $userData['additional']['show_net'] = false;
                $this->session->sOutputNet = true;
            } else {
                $userData['additional']['charge_vat'] = true;
                $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
                $this->session->sOutputNet = empty($system->sUSERGROUPDATA['tax']);
            }
        }

        return $userData;
    }

    /**
     * @param $userData
     * @return bool
     */
    protected function isTaxFreeDelivery($userData)
    {
        if (!empty($userData['additional']['countryShipping']['taxfree'])) {
            return true;
        }

        if (empty($userData['additional']['countryShipping']['taxfree_ustid'])) {
            return false;
        }

        if (empty($userData['shippingaddress']['ustid']) &&
            !empty($userData['billingaddress']['ustid']) &&
            !empty($userData['additional']['country']['taxfree_ustid'])
        ) {
            return true;
        }

        return !empty($userData['shippingaddress']['ustid']);
    }

    /**
     * Save and clear old basket
     */
    private function clearBasket()
    {
        $this->oldbasket = $this->basket->sGetBasket();
        $this->basket->clearBasket();
    }

    /**
     * @param $products
     */
    private function fillBasket($products)
    {
        foreach ($products as $product) {
            $this->basket->sAddArticle($product['ordernumber'], $product['quantity']);
        }
    }

    /**
     * Refill basket from saved old basket
     */
    private function resetBasket()
    {
        foreach ($this->oldbasket['content'] as $item) {
            $this->basket->sAddArticle($item['ordernumber'], $item['quantity']);
        }
    }

    /**
     * @param $products
     * @return mixed
     */
    private function filterOnRules($products)
    {
        $dashProductRepository = $this->container->get('models')->getRepository(DashButtonProduct::class);
        $productRuleHelper = $this->container->get('moj_dash_button.services.dash_button.product_rule_helper');

        $filtered = [];
        foreach ($products as $product){
            $dashProduct = $dashProductRepository->find($product["dashproductid"]);
            $rules = $productRuleHelper->getProductRules($dashProduct);

            $valid = true;

            foreach ($rules as $rule) {
                /** @var RuleInterface $ruleInstance */
                $ruleInstance = $rule['class'];
                $valid = $valid && $ruleInstance->validate();
            }

            if($valid){
                $filtered[] = $product;
            }
        }

        return $filtered;
    }

    /**
     * @param $userData
     * @return array
     */
    private function getBasket($userData)
    {
        $shippingcosts = $this->getShippingCosts($userData);

        $basket = $this->basket->sGetBasket();

        /** @var \Shopware\Models\Shop\Currency $currency */
        $currency = $this->get('shop')->getCurrency();

        $basket['sCurrencyId'] = $currency->getId();
        $basket['sCurrencyName'] = $currency->getCurrency();
        $basket['sCurrencyFactor'] = $currency->getFactor();
        $basket['sShippingcostsWithTax'] = $shippingcosts['brutto'];
        $basket['sShippingcostsNet'] = $shippingcosts['netto'];
        $basket['sShippingcostsTax'] = $shippingcosts['tax'];

        if (!empty($shippingcosts['brutto'])) {
            $basket['AmountNetNumeric'] += $shippingcosts['netto'];
            $basket['AmountNumeric'] += $shippingcosts['brutto'];
            $basket['sShippingcostsDifference'] = $shippingcosts['difference']['float'];
        }
        if (!empty($basket['AmountWithTaxNumeric'])) {
            $basket['AmountWithTaxNumeric'] += $shippingcosts['brutto'];
        }
        if ((!Shopware()->System()->sUSERGROUPDATA['tax'] && Shopware()->System()->sUSERGROUPDATA['id'])) {
            $basket['sTaxRates'] = $this->getTaxRates($basket);

            $basket['sShippingcosts'] = $shippingcosts['netto'];
            $basket['sAmount'] = round($basket['AmountNetNumeric'], 2);
            $basket['sAmountTax'] = round($basket['AmountWithTaxNumeric'] - $basket['AmountNetNumeric'], 2);
            $basket['sAmountWithTax'] = round($basket['AmountWithTaxNumeric'], 2);
        } else {
            $basket['sTaxRates'] = $this->getTaxRates($basket);

            $basket['sShippingcosts'] = $shippingcosts['brutto'];
            $basket['sAmount'] = $basket['AmountNumeric'];

            $basket['sAmountTax'] = round($basket['AmountNumeric'] - $basket['AmountNetNumeric'], 2);
        }

        return $basket;
    }

    /**
     * @param $userData
     * @return array|false
     */
    private function getShippingCosts($userData)
    {
        $country = $userData['additional']['country'];
        $payment = $userData['additional']['payment'];

        if (empty($country) || empty($payment)) {
            return ['brutto' => 0, 'netto' => 0];
        }

        $dispatches = $this->admin->sGetPremiumDispatches($country['id'], $payment['id']);
        $dispatch = \array_keys($dispatches)[0];

        $this->session->offsetSet('sDispatch', $dispatch);
        $shippingcosts = $this->admin->sGetPremiumShippingcosts($country);

        return empty($shippingcosts) ? ['brutto' => 0, 'netto' => 0] : $shippingcosts;
    }

    /**
     * @param $view
     * @param $sDispatch
     * @return string
     */
    private function saveOrder($view, $sDispatch)
    {
        $order = $this->modules->Order();

        $order->sUserData = $view->sUserData;
        $order->sBasketData = $view->sBasket;
        $order->sAmount = $view->sBasket['sAmount'];
        $order->sAmountWithTax = !empty($view->sBasket['AmountWithTaxNumeric']) ? $view->sBasket['AmountWithTaxNumeric'] : $view->sBasket['AmountNumeric'];
        $order->sAmountNet = $view->sBasket['AmountNetNumeric'];
        $order->sShippingcosts = $view->sBasket['sShippingcosts'];
        $order->sShippingcostsNumeric = $view->sBasket['sShippingcostsWithTax'];
        $order->sShippingcostsNumericNet = $view->sBasket['sShippingcostsNet'];
        $order->dispatchId = $sDispatch;
        $order->sNet = !$view->sUserData['additional']['charge_vat'];
        $order->deviceType = 'Dash Center';

        return $order->sSaveOrder();
    }

    /**
     * @param $basket
     * @return array
     */
    private function getTaxRates($basket)
    {
        $result = [];

        if (!empty($basket['sShippingcostsTax'])) {
            $basket['sShippingcostsTax'] = number_format((float)$basket['sShippingcostsTax'], 2);

            $result[$basket['sShippingcostsTax']] = $basket['sShippingcostsWithTax'] - $basket['sShippingcostsNet'];
            if (empty($result[$basket['sShippingcostsTax']])) {
                unset($result[$basket['sShippingcostsTax']]);
            }
        }

        if (empty($basket['content'])) {
            ksort($result, SORT_NUMERIC);

            return $result;
        }

        foreach ($basket['content'] as $item) {
            if (!empty($item['tax_rate'])) {
            } elseif (!empty($item['taxPercent'])) {
                $item['tax_rate'] = $item['taxPercent'];
            } elseif ($item['modus'] == 2) {
                // Ticket 4842 - dynamic tax-rates
                $resultVoucherTaxMode = $this->db->fetchOne(
                    'SELECT taxconfig FROM s_emarketing_vouchers WHERE ordercode=?
                ', [$item['ordernumber']]);
                // Old behaviour
                if (empty($resultVoucherTaxMode) || $resultVoucherTaxMode === 'default') {
                    $tax = $this->config->get('sVOUCHERTAX');
                } elseif ($resultVoucherTaxMode === 'auto') {
                    // Automatically determinate tax
                    $tax = $this->basket->getMaxTax();
                } elseif ($resultVoucherTaxMode === 'none') {
                    // No tax
                    $tax = '0';
                } elseif ((int)$resultVoucherTaxMode) {
                    // Fix defined tax
                    $tax = $this->db->fetchOne('
                    SELECT tax FROM s_core_tax WHERE id = ?
                    ', [$resultVoucherTaxMode]);
                }
                $item['tax_rate'] = $tax;
            } else {
                // Ticket 4842 - dynamic tax-rates
                $taxAutoMode = $this->config->get('sTAXAUTOMODE');
                if (!empty($taxAutoMode)) {
                    $tax = $this->basket->getMaxTax();
                } else {
                    $tax = $this->config->get('sDISCOUNTTAX');
                }
                $item['tax_rate'] = $tax;
            }

            if (empty($item['tax_rate']) || empty($item['tax'])) {
                continue;
            } // Ignore 0 % tax

            $taxKey = number_format((float)$item['tax_rate'], 2);

            $result[$taxKey] += str_replace(',', '.', $item['tax']);
        }

        ksort($result, SORT_NUMERIC);

        return $result;
    }

    /**
     * @param $name
     * @return object
     */
    private function get($name)
    {
        return $this->container->get($name);
    }

}