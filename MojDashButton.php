<?php


namespace MojDashButton;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\SchemaTool;

use MojDashButton\Models\DashBasketEntry;
use MojDashButton\Models\DashButton;
use MojDashButton\Models\DashButtonConfig;
use MojDashButton\Models\DashButtonProduct;
use MojDashButton\Models\DashButtonRule;
use MojDashButton\Models\DashLogEntry;

use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Theme\LessDefinition;

class MojDashButton extends Plugin
{

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'addTemplate',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'collectJS',
            'Theme_Compiler_Collect_Plugin_Less' => 'collectLess',
        ];
    }

    public function install(InstallContext $context)
    {
        parent::install($context);

        $this->createModels();

        return true;
    }

    /**
     * This method can be overridden
     *
     * @param Plugin\Context\ActivateContext $context
     */
    public function activate(Plugin\Context\ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    public function addTemplate(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $subject */
        $subject = $args->get('subject');
        $view = $subject->View();

        $view->addTemplateDir($this->getPath() . '/Resources/views/');
    }

    public function collectJS()
    {
        return new ArrayCollection([
            $this->getPath() . '/Resources/_public/src/js/jquery.product-suggest.js'
        ]);
    }

    public function collectLess()
    {
        $less = new LessDefinition(
            [],
            [
                $this->getPath() . '/Resources/_public/src/less/dashbutton.less'
            ]
        );

        return new ArrayCollection([$less]);
    }

    private function createModels()
    {
        /** @var ModelManager $models */
        $models = $this->container->get('models');

        $metaData = [
            $models->getClassMetadata(DashButton::class),
            $models->getClassMetadata(DashBasketEntry::class),
            $models->getClassMetadata(DashLogEntry::class),
            $models->getClassMetadata(DashButtonConfig::class),
            $models->getClassMetadata(DashButtonProduct::class),
            $models->getClassMetadata(DashButtonRule::class)
        ];

        $schemaTool = new SchemaTool($models);
        $schemaTool->updateSchema($metaData, true);
    }

}