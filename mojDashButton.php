<?php


namespace mojDashButton;

use Doctrine\ORM\Tools\SchemaTool;

use mojDashButton\Models\DashBasketEntry;
use mojDashButton\Models\DashButton;
use mojDashButton\Models\DashLogEntry;

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;

class mojDashButton extends Plugin
{

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'addTemplate',
        ];
    }

    public function install(InstallContext $context)
    {
        parent::install($context);

        $this->createModels();

        return true;
    }

    public function addTemplate(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $subject */
        $subject = $args->get('subject');
        $view = $subject->View();

        $view->addTemplateDir($this->getPath() . '/Resources/views/');
    }

    private function createModels()
    {
        /** @var ModelManager $models */
        $models = $this->container->get('models');

        $metaData = [
            $models->getClassMetadata(DashButton::class),
            $models->getClassMetadata(DashBasketEntry::class),
            $models->getClassMetadata(DashLogEntry::class)
        ];

        $schemaTool = new SchemaTool($models);
        $schemaTool->updateSchema($metaData, true);
    }

}