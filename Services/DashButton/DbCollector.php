<?php

namespace MojDashButton\Services\DashButton;

use MojDashButton\Models\DashButton;
use MojDashButton\Services\Core\ButtonCollector;
use Shopware\Components\Model\ModelManager;

class DbCollector implements ButtonCollector
{

    /**
     * @var ModelManager
     */
    private $models;

    /**
     * DbCollector constructor.
     * @param ModelManager $models
     */
    public function __construct(ModelManager $models)
    {
        $this->models = $models;
    }

    /**
     * @param string $buttonCode
     * @return DashButton
     * @throws \Exception
     */
    public function collectButton($buttonCode)
    {
        $repository = $this->models->getRepository(DashButton::class);

        $button = $repository->findOneBy([
            'buttonCode' => $buttonCode
        ]);

        if(null === $button){
            throw new \Exception('Button not found');
        }

        return $button;
    }

    /**
     * @param $userID
     * @return DashButton[]
     */
    public function collectButtonForUser($userID)
    {
        $repository = $this->models->getRepository(DashButton::class);

        $buttons = $repository->findBy([
            'userId' => $userID
        ]);

        return $buttons;
    }

}