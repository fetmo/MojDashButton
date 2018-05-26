<?php


class Shopware_Controllers_Frontend_DashCenterBasket extends Shopware_Controllers_Frontend_Account
{

    public function overviewAction()
    {
        $basketHandler = $this->get('moj_dash_button.services.dash_button.basket_handler');

        $products = $basketHandler->getProductsForUser($this->getUserId());

        $this->View()->assign('products', $products);
    }


    public function confirmAction()
    {
        $products = $this->Request()->getPost('products', []);

        $selected = array_filter($products, function ($el) {
            return isset($el['checked']);
        });

        $basket = [];

        $positionIds = array_column($selected, 'id');

        $this->get('db')->delete(
            'moj_basket_details',
            'id IN (' . substr(json_encode($positionIds), 1, -1) . ')'
        );

        //group products
        foreach ($selected as $item) {
            unset($item['checked']);
            unset($item['id']);

            if (isset($basket[$item['ordernumber']])) {
                $basket[$item['ordernumber']]['quantity'] += $item['quantity'];
            } else {
                $basket[$item['ordernumber']] = $item;
            }
        }


        //load products
        $productService = $this->get('shopware_storefront.product_service');
        $products = $productService->getList(array_keys($basket), $this->get('shopware_storefront.context_service')->getProductContext());

        //check products
        $structConverter = $this->get('legacy_struct_converter');
        foreach ($products as $product) {
            $ordernumber = $product->getNumber();

            $basketPosition = $basket[$ordernumber];
            $basketPosition = array_merge($basketPosition, $structConverter->convertProductStruct($product));

            $basketPosition['validation'] = $this->validateProduct($basketPosition);
            $basket[$ordernumber] = $this->calculateBasketPosition($basketPosition);;
        }

        $validation = count(array_filter(array_column($basket, 'validation'))) === 0;

        $this->View()->assign([
            'basket' => $basket,
            'basketValid' => $validation
        ]);
    }

    public function finishOrderAction()
    {
        $products = $this->Request()->get('products');

        $orderhandler = $this->get('moj_dash_button.services.dash_button.order_handler');
        $ordernumber = $orderhandler->createOrder($products, $this->getUserId());

        $this->redirect([
            'controller' => 'Account',
            'action' => 'orders'
        ]);
    }

    private function calculateBasketPosition($product)
    {
        $purchaseQuantity = $product['quantity'];

        if($purchaseQuantity > $product['maxpurchase']){
            $purchaseQuantity = $product['maxpurchase'];
        }else if ($purchaseQuantity < $product['minpurchase']){
            $purchaseQuantity = $product['minpurchase'];
        }

        $product['total_numeric'] = $purchaseQuantity * $product['price_numeric'];
        $product['purchase_quantity'] = $purchaseQuantity;

        return $product;
    }

    private function validateProduct($product)
    {
        $errors = [];

        if(!$product['isAvailable']){
            $errors[] = [
                'message' => 'Produkt nicht verfügbar.'
            ];
        }

        if($product['quantity'] < $product['minpurchase']){
            $errors[] = [
                'message' => 'Mindestbestellmenge nicht erreicht.'
            ];
        }

        if($product['quantity'] > (int)$product['maxpurchase']){
            $errors[] = [
                'message' => 'Maximalbestellmenge überschritten.'
            ];
        }

        if($product['quantity'] > (int)$product['instock']){
            $errors[] = [
                'message' => 'Verfügbare Menge überschritten.'
            ];
        }

        return $errors;
    }

    private function getUserId()
    {
        $userData = $this->View()->getAssign('sUserData');

        return (int)$userData['additional']['user']['userID'];
    }

}