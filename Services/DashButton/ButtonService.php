<?php

namespace mojDashButton\Services\DashButton;


use mojDashButton\Models\DashButton;
use mojDashButton\Services\Api\AuthenticationService;
use mojDashButton\Services\Core\ButtonCollector;
use mojDashButton\Services\Core\Logger;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\Product\Price;
use Shopware\Models\Customer\Customer;

class ButtonService
{

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var ButtonCollector
     */
    private $buttonCollector;

    /**
     * @var BasketHandler
     */
    private $basketHandler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var ListProductServiceInterface
     */
    private $listProduct;


    /**
     * ButtonService constructor.
     * @param AuthenticationService $authenticationService
     * @param ButtonCollector $buttonCollector
     * @param BasketHandler $basketHandler
     * @param Logger $logger
     * @param ListProductServiceInterface $listProduct
     * @param ContextServiceInterface $contextService
     */
    public function __construct(AuthenticationService $authenticationService, ButtonCollector $buttonCollector,
                                BasketHandler $basketHandler, Logger $logger, ListProductServiceInterface $listProduct,
                                ContextServiceInterface $contextService)
    {
        $this->authenticationService = $authenticationService;
        $this->buttonCollector = $buttonCollector;
        $this->basketHandler = $basketHandler;
        $this->logger = $logger;

        $this->listProduct = $listProduct;
        $this->contextService = $contextService;
    }

    /**
     * @param $token
     * @return bool
     *
     * @throws \Exception
     */
    public function triggerClick($token)
    {
        $button = $this->fetchButtonForToken($token);

        $this->logger->log('triggerClick', $button, 'Click got triggered');

        $addResponse = $this->basketHandler->addProductForButton($button);

        $type = 'triggerClick';
        $type .= ($addResponse) ? 'Success' : 'Fail';

        $message = 'Product add ';
        $message .= ($addResponse) ? 'succeeded' : 'failed';
        $this->logger->log($type, $button,  $message);

        return $addResponse;
    }

    /**
     * @param $token
     * @return array
     *
     * @throws \Exception
     */
    public function getProduct($token)
    {
        $button = $this->fetchButtonForToken($token);

        $product = $this->listProduct->get($button->getOrdernumber(), $this->contextService->getContext());

        if (null === $product) {
            throw new \Exception('No Product configured');
        }

        $price = $this->getPriceForUser($product, $button->getUser());

        $productData = [
            'title' => $product->getName(),
            'active' => $product->isAvailable(),
            'price' => $price
        ];

        return $productData;
    }

    /**
     * @param $token
     * @return DashButton
     */
    private function fetchButtonForToken($token)
    {
        $button = $this->buttonCollector->collectButton(
            $this->authenticationService->fetchToken($token)
        );
        return $button;
    }

    /**
     * @param ListProduct $product
     * @param Customer $user
     * @return float
     */
    private function getPriceForUser(ListProduct $product, Customer $user)
    {
        $fPrice = 0.0;

        /** @var Price $price */
        foreach ($product->getPrices() as $price) {
            if ($price->getFrom() == 1 && $price->getCustomerGroup()->getKey() == $user->getGroupKey()) {
                $fPrice = $price->getCalculatedPrice();
                break;
            }
        }

        return $fPrice;
    }

}