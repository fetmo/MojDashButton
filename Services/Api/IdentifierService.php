<?php

namespace MojDashButton\Services\Api;


class IdentifierService
{

    /**
     * @var string
     */
    CONST SEPARATOR = '#';

    /**
     * @param string $buttoncode
     * @return string
     */
    public function getButtonCodeFromIdentifier(string $buttoncode): string
    {
        $pos = strpos($buttoncode, self::SEPARATOR);

        return $pos !== false ? substr($buttoncode, 0, $pos) : $buttoncode;
    }

    /**
     * @param string $buttoncode
     * @param array $products
     * @return array
     */
    public function createIdentifierForProducts(string $buttoncode, array $products): array
    {
        foreach ($products as &$product) {
            $product['identifier'] = $this->createIdentifierForProduct($buttoncode, $product['id']);
        }

        return $products;
    }

    /**
     * @param $buttoncode
     * @param $id
     * @return string
     */
    public function createIdentifierForProduct($buttoncode, $id): string
    {
        return $buttoncode . self::SEPARATOR . $id;
    }

}