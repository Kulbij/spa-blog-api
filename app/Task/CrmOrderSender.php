<?php

namespace App\Task;

use App\Task\Crm;
use App\Task\CrmConnectorInterface;

abstract class CrmOrderSender
{
    abstract public function getCrm(): CrmConnectorInterface;

    public $data;

    public function sendOrder($order): array
    {
        $crm = $this->getCrm();

        $crm->login();
        $crm->sendOrder($order);
        $crm->logout();

        return $crm->getResult();
    }
}
