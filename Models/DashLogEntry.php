<?php
/**
 * Created by PhpStorm.
 * User: doit-jung
 * Date: 14.05.2018
 * Time: 19:00
 */

namespace mojDashButton\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Class DashLogEntry
 * @package mojDashButton\Models
 *
 * @ORM\Entity()
 * @ORM\Table(name="moj_dash_log")
 */
class DashLogEntry extends ModelEntity
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
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="log_date", type="datetime")
     */
    private $date;

    /**
     * @var DashButton
     *
     * @ORM\ManyToOne(targetEntity="\mojDashButton\Models\DashButton", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="button_id", referencedColumnName="id")
     */
    private $button;

    /**
     * @var integer
     *
     * @ORM\Column(name="button_id", type="integer", nullable=true)
     */
    private $buttonId;

    /**
     * DashLogEntry constructor.
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
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

}