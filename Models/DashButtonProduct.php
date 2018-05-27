<?php

namespace MojDashButton\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashButtonProduct
 * @package MojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_dash_button_product")
 */
class DashButtonProduct extends ModelEntity
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", options={"default": 1} )
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    private $ordernumber;

    /**
     * @var DashButton
     *
     * @ORM\ManyToOne(targetEntity="\MojDashButton\Models\DashButton")
     * @ORM\JoinColumn(name="button_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $button;

    /**
     * @var int
     *
     * @ORM\Column(name="button_id", type="integer", nullable=true)
     */
    private $buttonId;

    /**
     * @var DashButtonRule[]
     *
     * @ORM\OneToMany(targetEntity="\MojDashButton\Models\DashButtonRule", mappedBy="product")
     */
    private $rules;

    /**
     * DashButtonProduct constructor.
     */
    public function __construct()
    {
        $this->rules = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getOrdernumber(): string
    {
        return $this->ordernumber;
    }

    /**
     * @param string $ordernumber
     */
    public function setOrdernumber(string $ordernumber)
    {
        $this->ordernumber = $ordernumber;
    }

    /**
     * @return DashButton
     */
    public function getButton(): DashButton
    {
        return $this->button;
    }

    /**
     * @param DashButton $button
     */
    public function setButton(DashButton $button)
    {
        $this->button = $button;
        $this->button->addProduct($this);
    }

    /**
     * @return int
     */
    public function getButtonId(): int
    {
        return $this->buttonId;
    }

    /**
     * @param int $buttonId
     */
    public function setButtonId(int $buttonId)
    {
        $this->buttonId = $buttonId;
    }

    /**
     * @return DashButtonRule[]
     */
    public function getRules(): array
    {
        return $this->rules->toArray();
    }

    /**
     * @param DashButtonRule[] $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param DashButtonRule $rule
     */
    public function addRule(DashButtonRule $rule)
    {
        $this->rules->add($rule);
    }

    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }

}