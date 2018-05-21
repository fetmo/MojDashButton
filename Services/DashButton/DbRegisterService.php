<?php
/**
 * Created by PhpStorm.
 * User: doit-jung
 * Date: 15.05.2018
 * Time: 20:13
 */

namespace MojDashButton\Services\DashButton;


use MojDashButton\Models\DashButton;
use MojDashButton\Services\Core\ButtonCollector;
use MojDashButton\Services\Core\Logger;
use MojDashButton\Services\Core\RegisterService;
use Shopware\Components\Model\ModelManager;

class DbRegisterService implements RegisterService
{

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ButtonCollector
     */
    private $buttonCollector;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * DbRegisterService constructor.
     * @param ButtonCollector $buttonCollector
     * @param ModelManager $modelManager
     * @param Logger $logger
     */
    public function __construct(ButtonCollector $buttonCollector, ModelManager $modelManager, Logger $logger)
    {
        $this->modelManager = $modelManager;
        $this->buttonCollector = $buttonCollector;
        $this->logger = $logger;
    }

    /**
     * @param $buttonCode
     * @return DashButton
     * @throws \Exception
     */
    public function registerButton($buttonCode)
    {
        try{
            $this->buttonCollector->collectButton($buttonCode);
        }catch (\Exception $exception){
            $button = new DashButton();
            $button->setButtonCode($buttonCode);
            $button->setQuantity(0);

            $this->modelManager->persist($button);
            $this->modelManager->flush($button);

            $message = sprintf('Button successfully registered (%s)', $buttonCode);
            $this->logger->log('buttonRegister', $button, $message);

            return $button;
        }

        throw new \Exception('Button already registered');
    }

}