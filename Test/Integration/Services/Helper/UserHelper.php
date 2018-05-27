<?php

namespace MojDashButton\Test\Integration\Services\Helper;


use Symfony\Component\DependencyInjection\Container;

trait UserHelper
{

    use ButtonCodeGenerator;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var int
     */
    private $autoOrderRuntime;

    private function unableAutoOrder()
    {
        $db = $this->container->get('db');

        $this->autoOrderRuntime = (int)$db->fetchOne(
            'SELECT moj_dash_button_directorder FROM s_user_attributes WHERE id = :user',
            ['user' => $this->getUserId()]
        );

        $this->setDirectOrder(0);
    }

    private function revertAutoOrder()
    {
        $this->setDirectOrder($this->autoOrderRuntime);
    }

    private function setDirectOrder($state)
    {
        $db = $this->container->get('db');

        $db->executeUpdate(
            'UPDATE s_user_attributes SET moj_dash_button_directorder = :old WHERE id = :id',
            ['id' => $this->getUserId(), 'old' => $state]
        );
    }

}