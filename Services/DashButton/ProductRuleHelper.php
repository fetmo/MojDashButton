<?php

namespace MojDashButton\Services\DashButton;


use MojDashButton\Components\Rules\IntervalRule;
use MojDashButton\Models\DashButtonProduct;
use MojDashButton\Models\DashButtonRule;
use Shopware\Components\Model\ModelManager;

class ProductRuleHelper
{

    /**
     * @var ModelManager
     */
    private $em;

    public function __construct(ModelManager $em)
    {
        $this->em = $em;
    }

    public function createProductRule(DashButtonProduct $productPosition, $ruleData)
    {
        $configuredRules = $productPosition->getRules();

        $mergedSet = [];
        foreach ($ruleData as $type => $config) {
            $processed = false;

            foreach ($configuredRules as $configuredRule) {
                $ruleConfig = json_decode($configuredRule->getRuledata(), true);
                if ($ruleConfig['type'] === $type){
                    $processed = true;

                    $ruleConfig['config'] = $config;
                    $configuredRule->setRuledata(json_encode($ruleConfig));
                    $mergedSet[] = $configuredRule;
                    break;
                }
            }

            if(!$processed){
                $newRule = (new DashButtonRule())->fromArray([
                    'ruledata' => json_encode([
                        'type' => $type,
                        'config' => $config
                    ]),
                    'product' => $productPosition
                ]);

                $this->em->persist($newRule);

                $mergedSet[] = $newRule;
            }
        }

        $productPosition->setRules($mergedSet);

        $this->em->persist($productPosition);
        $this->em->flush();
    }

    public function getProductRules(DashButtonProduct $productPosition)
    {
        $rules = $productPosition->getRules();

        $ruleSets = [];
        foreach ($rules as $rule) {
            $ruleData = $rule->getRuledata();
            $ruleSet = json_decode($ruleData, true);

            $class = $this->getConfiguredRules()[$ruleSet['type']];
            $ruleSet['class'] = new $class($ruleSet['config']);

            $ruleSets[$ruleSet['type']] = $ruleSet;
        }

        return $ruleSets;
    }

    public function getConfiguredRules()
    {
        return [
            'orderinteval' => IntervalRule::class
        ];
    }

}