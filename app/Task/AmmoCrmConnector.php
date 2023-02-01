<?php

namespace App\Task;

use App\Task\Crm;
use App\Task\CrmConnectorInterface;

class AmmoCrmConnector implements CrmConnectorInterface
{
    private $login, $password;

    public $data;

    public function __construct($login, $password)
	{
		$this->login = $login;
		$this->password = $password;
	}

	public function logIn(): void
	{
		echo "connection";
	}

	public function logout(): void
	{
		echo "</br>disconnection";
	}

    public function sendOrder($order): void
    {
    	echo "</br>data is posted";

    	$this->data[] = 123;
    }

    public function getResult(): array
    {
    	return $this->data;
    }
}
