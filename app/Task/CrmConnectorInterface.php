<?php

namespace App\Task;

interface CrmConnectorInterface
{
	public function login(): void;

	public function logout(): void;

	public function sendOrder($order): void;

	public function getResult(): array;
}
