<?php

namespace MojDashButton\Test\Integration\Services\Api;

use MojDashButton\Services\Api\IdentifierService;

class IdentifierServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testIdentifierSplitOnButtonCodeWithNoIdentifier()
    {
        $identifierService = $this->getIdentifierService();

        $buttonCode = 'ASDFTEST1234';
        $result = $identifierService->getButtonCodeFromIdentifier($buttonCode);

        $this->assertEquals($result, $buttonCode);
    }

    public function testIdentifierSplitOnButtonCodeWithIdentifier()
    {
        $identifierService = $this->getIdentifierService();

        $buttonCode = 'ASDFTEST1234';
        $counter = '2134';

        $identifier = $buttonCode . IdentifierService::SEPARATOR . $counter;
        $result = $identifierService->getButtonCodeFromIdentifier($identifier);

        $this->assertNotEquals($result, $identifier);
        $this->assertContains($result, $identifier);
        $this->assertEquals($result, $buttonCode);
    }

    public function testIdentifierGenerationOnProducts()
    {
        $identifierService = $this->getIdentifierService();

        $buttonCode = 'ASDFTEST';
        $products = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ];

        $modifiedProducts = $identifierService->createIdentifierForProducts($buttonCode, $products);

        foreach ($modifiedProducts as $key => $modifiedProduct) {
            $this->assertNotEmpty($modifiedProduct['identifier']);
            $this->assertContains($buttonCode, $modifiedProduct['identifier']);
            $this->assertContains((string)$products[$key]['id'], $modifiedProduct['identifier']);
        }
    }

    private function getIdentifierService():IdentifierService
    {
        return new IdentifierService();
    }

}
