<?php

namespace mojDashButton\Services\DashButton;


use mojDashButton\Models\DashButton;
use mojDashButton\Services\Api\AuthenticationService;
use mojDashButton\Services\Core\ButtonCollector;
use mojDashButton\Services\Core\Logger;
use Shopware\Models\Article\Detail;
use Shopware\Models\Article\Price;
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
     * ButtonService constructor.
     * @param AuthenticationService $authenticationService
     * @param ButtonCollector $buttonCollector
     * @param BasketHandler $basketHandler
     * @param Logger $logger
     */
    public function __construct(AuthenticationService $authenticationService, ButtonCollector $buttonCollector,
                                BasketHandler $basketHandler, Logger $logger)
    {
        $this->authenticationService = $authenticationService;
        $this->buttonCollector = $buttonCollector;
        $this->basketHandler = $basketHandler;
        $this->logger = $logger;
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

        $product = $button->getVariant();

        if (null === $product) {
            throw new \Exception('No Product configured');
        }

        $article = $product->getArticle();

        $price = $this->getPriceForUser($product, $button->getUser());

        $productData = [
            'title' => $article->getName(),
            'active' => $product->getActive() && $article->getActive(),
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
     * @param Detail $product
     * @param Customer $user
     * @return float
     */
    private function getPriceForUser(Detail $product, Customer $user)
    {
        $fPrice = 0.0;

        /** @var Price $price */
        foreach ($product->getPrices() as $price) {
            if ($price->getFrom() == 0 && $price->getCustomerGroup() == $user->getGroupKey()) {
                #@todo: check for VAT
                $fPrice = $price->getPrice();
                break;
            }
        }

        return $fPrice;
    }

}