<?php
/**
 * Created by PhpStorm.
 * User: doit-jung
 * Date: 14.05.2018
 * Time: 20:11
 */

namespace mojDashButton\Services\Core;


interface ButtonCollector
{

    /**
     * @param string $buttonCode
     * @return mixed
     */
    public function collectButton($buttonCode);

}