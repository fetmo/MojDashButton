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

    public function collectLog(DashButton $button)
    {
        $selectSQL = 'SELECT * FROM moj_dash_log 
                        WHERE button_id = :buttonid OR message like :buttoncode 
                        ORDER BY log_Date DESC, id DESC';

        $selectSQL = str_replace(':buttoncode', '"%' . $button->getButtonCode() . '%"', $selectSQL);

        return
            $this->db->fetchAll($selectSQL,
                [
                    'buttonid' => $button->getId()
                ]
            );
    }

}