<?php

namespace MojDashButton\Components\Rules;

use MojDashButton\Models\DashButtonProduct;
use Symfony\Component\DependencyInjection\Container;

class IntervalRule implements RuleInterface
{

    CONST TYPE = 'orderinteval';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $time;

    /**
     * @var DashButtonProduct
     */
    private $dashProduct;

    /**
     * IntervalRule constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $config
     */
    public function configure(array $config)
    {
        $this->time = strtoupper(
            str_replace(
                ['hours', 'days', 'minutes', 'seconds'],
                ['hour', 'day', 'minute', 'second'],
                strtolower($config['config'])
            )
        );
        $this->dashProduct = $config['dashproduct'];
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $session = $this->container->get('session');
        $db = $this->container->get('db');

        $userId = $session->offsetGet('sUserId');
        $productOrdernumber = $this->dashProduct->getOrdernumber();

        $selectUserOrders =
            'SELECT so.ordernumber 
              FROM s_order so 
              INNER JOIN s_order_details sod on so.id = sod.orderID
               WHERE so.userID = :user AND sod.articleordernumber = :ordernumber AND
               so.ordertime >= ( NOW() - INTERVAL ' . $this->time . ' )';

        $orders = $db->fetchAll($selectUserOrders, [
            'user' => $userId,
            'ordernumber' => $productOrdernumber
        ]);

        return count($orders) === 0;
    }


}