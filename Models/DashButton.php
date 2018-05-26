<?php

namespace MojDashButton\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Customer\Customer;

/**
 * Class DashButton
 * @package MojDashButton\Models
 *
 * @ORM\Entity(repositoryClass="\MojDashButton\Models\Repository\DashButton")
 * @ORM\Table(name="moj_dash_button")
 */
class DashButton extends ModelEntity
{

    CONST SINGLEPRODUCTMODE = 1;
    CONST MULTIPRODUCTMODE = 2;

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
     * @ORM\Column(name="product_mode", type="integer", options={"default": 1})
     */
    private $productMode;

    /**
     * @var DashButtonProduct[]
     *
     * @ORM\OneToMany(targetEntity="\MojDashButton\Models\DashButtonProduct", mappedBy="button")
     */
    private $products;

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
     * DashButton constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productMode = self::SINGLEPRODUCTMODE;
    }

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
    public function getButtonCode(): string
    {
        return $this->buttonCode;
    }

    /**
     * @param string $buttonCode
     */
    public function setButtonCode(string $buttonCode)
    {
        $this->buttonCode = $buttonCode;
    }

    /**
     * @return int
     */
    public function getProductMode(): int
    {
        return $this->productMode;
    }

    /**
     * @param int $productMode
     */
    public function setProductMode(int $productMode)
    {
        $this->productMode = $productMode;
    }

    /**
     * @return DashButtonProduct[]
     */
    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    /**
     * @param DashButtonProduct[] $products
     */
    public function setProducts(array $products)
    {
        $this->products = $products;
    }

    /**
     * @param DashButtonProduct $product
     */
    public function addProduct(DashButtonProduct $product)
    {
        $this->products->add($product);
    }

    public function removeProduct(DashButtonProduct $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * @return Customer
     */
    public function getUser(): Customer
    {
        return $this->user;
    }

    /**
     * @param Customer $user
     */
    public function setUser(Customer $user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

}