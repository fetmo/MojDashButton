<?php

namespace MojDashButton\Models;


use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashButtonRule
 * @package MojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_dash_rule")
 */
class DashButtonRule extends ModelEntity
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ruledata", type="string", nullable=true)
     */
    private $ruledata;

    /**
     * @var DashButtonProduct
     *
     * @ORM\ManyToOne(targetEntity="\MojDashButton\Models\DashButtonProduct")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRuledata(): string
    {
        return $this->ruledata;
    }

    /**
     * @param string $ruledata
     */
    public function setRuledata(string $ruledata)
    {
        $this->ruledata = $ruledata;
    }

    /**
     * @return DashButtonProduct
     */
    public function getProduct(): DashButtonProduct
    {
        return $this->product;
    }

    /**
     * @param DashButtonProduct $product
     */
    public function setProduct(DashButtonProduct $product)
    {
        $this->product = $product;
    }

}