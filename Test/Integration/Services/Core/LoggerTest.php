<?php

class LoggerTest extends PHPUnit_Framework_TestCase
{

    public function testLog()
    {
        $db = Shopware()->Db();
        $type = 'phpunit';
        $whereType = 'type = "' . $type . '"';

        $logger = new \MojDashButton\Services\Core\Logger($db);

        $logger->log($type, null, 'First Log');

        $logs = $db->fetchAll('SELECT * FROM moj_dash_log WHERE ' . $whereType);
        $this->assertCount(1, $logs);

        $logger->log($type, null, 'Another Log');
        $logger->log($type, new \MojDashButton\Models\DashButton(), 'Another Log');
        $logger->log($type, null, 'Another Log');
        $logger->log($type, new \MojDashButton\Models\DashButton(), 'Another Log');

        $logs = $db->fetchAll('SELECT * FROM moj_dash_log WHERE ' . $whereType);
        $this->assertCount(5, $logs);

        $db->delete('moj_dash_log', $whereType);
    }

}
