<?php

namespace App\Controller;

abstract class AbstractController
{

	protected function getData(): array
	{
		return json_decode(file_get_contents('php://input'), true) ?: [];
	}

	protected function jsonResponse(array $data, int $status = 200): void
	{
		header('Content-Type: application/json', true, $status);
		echo json_encode($data);
		exit;
	}
}