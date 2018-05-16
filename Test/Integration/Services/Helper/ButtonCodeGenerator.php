<?php

namespace mojDashButton\Test\Integration\Services\Helper;

trait ButtonCodeGenerator
{

    public function getButtonCode()
    {
        return 'PHPUNIT' . time() . rand(100, 20000);
    }

}