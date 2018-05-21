<?php

namespace MojDashButton\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashButton
 * @package MojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_dash_button")
 */
class DashButton extends ModelEntity
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
     * @ORM\Column(name="button_code", type="string", nullable=false, unique=true)
     */
    private $buttonCode;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", options={"default": 1} )
     */
    private $quantity;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getButtonCode()
    {
        return $this->buttonCode;
    }

    /**
     * @param string $buttonCode
     */
    public function setButtonCode($buttonCode)
    {
        $this->buttonCode = $buttonCode;
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
     * @return string
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * @param string $ordernumber
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;
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

}