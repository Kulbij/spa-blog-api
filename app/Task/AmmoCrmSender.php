<?php

namespace App\Task;

use App\Task\CrmOrderSender;
use App\Task\CrmConnectorInterface;

class AmmoCrmSender extends CrmOrderSender
{
	private $login, $password;

	public function __construct($login, $password)
	{
		$this->login = $login;
		$this->password = $password;
	}

    public function getCrm(): CrmConnectorInterface
    {
        return new AmmoCrmConnector($this->login, $this->password);
    }
}
