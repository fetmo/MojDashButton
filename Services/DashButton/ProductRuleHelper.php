<?php

namespace MojDashButton\Services\DashButton;


use MojDashButton\Components\Rules\IntervalRule;
use MojDashButton\Components\Rules\RuleInterface;
use MojDashButton\Models\DashButtonProduct;
use MojDashButton\Models\DashButtonRule;
use Shopware\Components\Model\ModelManager;
use Symfony\Component\DependencyInjection\Container;

class ProductRuleHelper
{

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var Container
     */
    private $container;

    public function __construct(ModelManager $em, Container $container)
    {
        $this->em = $em;

        $this->container = $container;
    }

    public function createProductRule(DashButtonProduct $productPosition, $ruleData)
    {
        $configuredRules = $productPosition->getRules();

        $mergedSet = [];
        foreach ($ruleData as $type => $config) {
            $processed = false;

            foreach ($configuredRules as $configuredRule) {
                $ruleConfig = json_decode($configuredRule->getRuledata(), true);
                if ($ruleConfig['type'] === $type) {
                    $processed = true;

                    $ruleConfig['config'] = $config;
                    $configuredRule->setRuledata(json_encode($ruleConfig));
                    $mergedSet[] = $configuredRule;
                    break;
                }
            }

            if (!$processed) {
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

            $config = [
                'config' => $ruleSet['config'],
                'dashproduct' => $productPosition
            ];

            /** @var RuleInterface $classInstance */
            $classInstance =  $this->container->get($class);
            $classInstance->configure($config);

            $ruleSet['class'] = $classInstance;

            $ruleSets[$ruleSet['type']] = $ruleSet;
        }

        return $ruleSets;
    }

    public function getConfiguredRules()
    {
        return [
            IntervalRule::TYPE => IntervalRule::class
        ];
    }

}