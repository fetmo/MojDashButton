<?php

namespace MojDashButton\Services\DashButton;


use MojDashButton\Models\DashButton;
use MojDashButton\Services\Api\AuthenticationService;
use MojDashButton\Services\Api\IdentifierService;
use MojDashButton\Services\Core\ButtonCollector;
use MojDashButton\Services\Core\Logger;
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
     * @var IdentifierService
     */
    private $identifierService;

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
     * @param IdentifierService $identifierService
     * @param ListProductServiceInterface $listProduct
     * @param ContextServiceInterface $contextService
     */
    public function __construct(AuthenticationService $authenticationService, ButtonCollector $buttonCollector,
                                BasketHandler $basketHandler, Logger $logger, IdentifierService $identifierService,
                                ListProductServiceInterface $listProduct, ContextServiceInterface $contextService)
    {
        $this->authenticationService = $authenticationService;
        $this->buttonCollector = $buttonCollector;
        $this->basketHandler = $basketHandler;
        $this->logger = $logger;
        $this->identifierService = $identifierService;

        $this->listProduct = $listProduct;
        $this->contextService = $contextService;
    }

    /**
     * @param string $token
     * @param string $identifier
     * @return bool
     *
     * @throws \Exception
     */
    public function triggerClick(string $token, string $identifier = '')
    {
        $dashButtonProduct = null;
        $button = $this->fetchButtonForToken($token);

        $this->logger->log('triggerClick', $button, 'Click got triggered');

        $dashButtonProduct = $this->getDashProductForIdentifier($identifier, $button);

        if (null === $dashButtonProduct) {
            throw new \Exception('No Product configured');
        }

        $addResponse = $this->basketHandler->addProductForButton($button, $dashButtonProduct);

        $type = 'triggerClick';
        $type .= ($addResponse) ? 'Success' : 'Fail';

        $message = 'Product add ';
        $message .= ($addResponse) ? 'succeeded' : 'failed';
        $this->logger->log($type, $button, $message);

        return $addResponse;
    }

    /**
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public function getProduct(string $token)
    {
        $button = $this->fetchButtonForToken($token);
        $productPosition = [];

        foreach ($button->getProducts() as $dashButtonProduct) {
            $productPosition[$dashButtonProduct->getOrdernumber()] = [
                'id' => $dashButtonProduct->getId(),
                'quantity' => $dashButtonProduct->getQuantity()
            ];
        }

        $products = $this->listProduct->getList(\array_keys($productPosition), $this->contextService->getContext());

        if (0 === \count($products)) {
            throw new \Exception('No Product configured');
        }

        $productData = [];
        foreach ($products as $product) {
            $price = $this->getPriceForUser($product, $button->getUser());
            $productPositionData = $productPosition[$product->getNumber()];

            $productData[] = [
                'id' => $productPositionData['id'],
                'title' => $product->getName(),
                'active' => $product->isAvailable(),
                'price' => $price,
                'quantity' => $productPositionData['quantity']
            ];
        }

        $productData = $this->identifierService->createIdentifierForProducts($button->getButtonCode(), $productData);
        if (DashButton::SINGLEPRODUCTMODE === $button->getProductMode()) {
            $productData = $productData[0];
        }

        return $productData;
    }

    /**
     * @param string $token
     * @return DashButton
     */
    private function fetchButtonForToken(string $token): DashButton
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

    /**
     * @param string $identifier
     * @param $button
     * @return mixed
     */
    private function getDashProductForIdentifier(string $identifier, DashButton $button)
    {
        $dashButtonProduct = null;

        if ($identifier !== '') {
            $dashButtonProducts = $button->getProducts();
            foreach ($dashButtonProducts as $dashProduct) {
                if ($identifier === $this->identifierService->createIdentifierForProduct($button->getButtonCode(), $dashProduct->getId())) {
                    $dashButtonProduct = $dashProduct;
                    break;
                }
            }
        } else {
            $dashButtonProduct = $button->getProducts()[0];
        }

        return $dashButtonProduct;
    }

}