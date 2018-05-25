<?php

namespace MojDashButton\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashButtonConfig
 * @package MojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_dash_button_config")
 */
class DashButtonConfig extends ModelEntity
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
     * @var DashButton
     *
     * @ORM\OneToOne(targetEntity="\MojDashButton\Models\DashButton")
     * @ORM\JoinColumn(name="button_id", referencedColumnName="id")
     */
    private $button;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    }

}