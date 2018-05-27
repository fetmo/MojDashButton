<?php

namespace MojDashButton\Components\Rules;


interface RuleInterface
{

    public function configure(array $config);

    public function validate(): bool;

}