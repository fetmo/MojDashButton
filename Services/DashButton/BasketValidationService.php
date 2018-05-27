<?php

namespace MojDashButton\Services\DashButton;


use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ProductServiceInterface;
use Shopware\Components\Compatibility\LegacyStructConverter;

class BasketValidationService
{

    private $productService;

    private $legacyConverter;

    private $contextService;

    public function __construct(ProductServiceInterface $productService, LegacyStructConverter $legacyStructConverter, ContextServiceInterface $contextService)
    {
        $this->contextService = $contextService;
        $this->legacyConverter = $legacyStructConverter;
        $this->productService = $productService;
    }

    public function validateSelectedProducts($selected)
    {
        $basket = [];

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
        $products = $this->productService->getList(array_keys($basket), $this->contextService->getProductContext());

        //check products
        foreach ($products as $product) {
            $ordernumber = $product->getNumber();

            $basketPosition = $basket[$ordernumber];
            $basketPosition = array_merge($basketPosition, $this->legacyConverter->convertProductStruct($product));

            $basketPosition['validation'] = $this->validateProduct($basketPosition);
            $basket[$ordernumber] = $this->calculateBasketPosition($basketPosition);;
        }

        return $basket;
    }

    private function calculateBasketPosition($product)
    {
        $purchaseQuantity = $product['quantity'];

        if ($purchaseQuantity > $product['maxpurchase']) {
            $purchaseQuantity = $product['maxpurchase'];
        } else if ($purchaseQuantity < $product['minpurchase']) {
            $purchaseQuantity = $product['minpurchase'];
        }

        if($purchaseQuantity > $product['instock'] && $product['laststock'] === 1){
            $purchaseQuantity = $product['instock'];
        }

        $product['total_numeric'] = $purchaseQuantity * $product['price_numeric'];
        $product['purchase_quantity'] = $purchaseQuantity;

        return $product;
    }

    private function validateProduct($product)
    {
        $errors = [];

        if (!$product['isAvailable']) {
            $errors[] = [
                'message' => 'Produkt nicht verfügbar.'
            ];
        }

        if ($product['quantity'] < $product['minpurchase']) {
            $errors[] = [
                'message' => 'Mindestbestellmenge nicht erreicht.'
            ];
        }

        if ($product['quantity'] > (int)$product['maxpurchase']) {
            $errors[] = [
                'message' => 'Maximalbestellmenge überschritten.'
            ];
        }

        if ($product['quantity'] > (int)$product['instock'] && $product['laststock'] === 1) {
            $errors[] = [
                'message' => 'Verfügbare Menge überschritten.'
            ];
        }

        return $errors;
    }


}