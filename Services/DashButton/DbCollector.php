<?php

namespace mojDashButton\Services\DashButton;

use mojDashButton\Models\DashButton;
use mojDashButton\Services\Core\ButtonCollector;
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

}