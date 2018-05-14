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
    public function install(InstallContext $context)
    {
        parent::install($context);

        $this->createModels();

        /** @var CrudService $attributeCrudService */
        $attributeCrudService = $this->container->get('shopware_attribute.crud_service');
        $attributeCrudService->update(
            's_user_attributes',
            'moj_dash_buttons',
            TypeMapping::TYPE_MULTI_SELECTION,
            [
                'entity' => DashButton::class,
                'displayInBackend' => true,
                'custom' => true,
                'label' => 'Dash-Buttons'
            ]
        );

        return true;
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