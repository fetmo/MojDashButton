<?php

namespace MojDashButton\Services\Core;

use MojDashButton\Models\DashButton;

class Logger
{

    private $db;

    public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db)
    {
        $this->db = $db;
    }

    public function log($type, DashButton $button = null, $message)
    {
        return (bool)$this->db->insert(
            'moj_dash_log',
            [
                'type' => $type,
                'button_id' => ($button != null) ? $button->getId() : null,
                'message' => $message,
                'log_date' => date("Y-m-d H:i:s")
            ]
        );
    }

}