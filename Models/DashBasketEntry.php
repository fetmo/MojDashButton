<?php

namespace MojDashButton\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashBasketEntry
 * @package MojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_basket_details")
 */
class DashBasketEntry extends ModelEntity
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="basket_date", type="datetime")
     */
    private $date;

    /**
     * @var DashButton
     *
     * @ORM\ManyToOne(targetEntity="\MojDashButton\Models\DashButton")
     * @ORM\JoinColumn(name="button_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $button;

    /**
     * @var integer
     *
     * @ORM\Column(name="button_id", type="integer", nullable=true)
     */
    private $buttonId;

    /**
     * @var \Shopware\Models\Customer\Customer
     *
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Customer\Customer")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var \Shopware\Models\Article\Detail
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Article\Detail")
     * @ORM\JoinColumn(name="ordernumber", referencedColumnName="ordernumber")
     */
    private $variant;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    private $ordernumber;

    /**
     * DashBasketEntry constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return DashButton
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * @param DashButton $button
     */
    public function setButton($button)
    {
        $this->button = $button;
    }

    /**
     * @return int
     */
    public function getButtonId()
    {
        return $this->buttonId;
    }

    /**
     * @param int $buttonId
     */
    public function setButtonId($buttonId)
    {
        $this->buttonId = $buttonId;
    }

    /**
     * @return \Shopware\Models\Customer\Customer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Shopware\Models\Customer\Customer $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return \Shopware\Models\Article\Detail
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * @param \Shopware\Models\Article\Detail $variant
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;
    }

    /**
     * @return int
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * @param int $ordernumber
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;
    }

}