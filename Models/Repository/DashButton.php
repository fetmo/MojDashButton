<?php

namespace MojDashButton\Models\Repository;

use MojDashButton\Models\DashButtonProduct;
use MojDashButton\Services\Core\Logger;
use Shopware\Components\Model\ModelRepository;


class DashButton extends ModelRepository
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     * @return DashButton
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param \MojDashButton\Models\DashButton $button
     * @param array $productPositions
     */
    public function saveProductPositions(\MojDashButton\Models\DashButton $button, array $productPositions)
    {
        foreach ($productPositions as $productPosition) {
            if (!empty($productPosition['id'])) {
                $dashProduct = $this->_em->getRepository(DashButtonProduct::class)->find($productPosition['id']);
                unset($productPosition['id']);
            } else {
                $dashProduct = new DashButtonProduct();
            }

            $dashProduct->fromArray($productPosition);
            $dashProduct->setButton($button);

            $this->_em->persist($dashProduct);
            $this->_em->flush($dashProduct);

            if($this->logger){
                $this->logger->log('buttonSave', $button,
                    sprintf('Button successfully saved (%s, %d)', $dashProduct->getOrdernumber(), $dashProduct->getQuantity())
                );
            }
        }


        $this->_em->persist($button);
        $this->_em->flush($button);
    }

}