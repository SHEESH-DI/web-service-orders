<?php

namespace App\Service;

class OrderService {
	private string $orderId;
	private array $items;
	private bool $done;

	public const int STATUS_ORDER_DONE = 1;

	public function __construct(string $orderId, array $items, bool $done = false) {
		$this->orderId = $orderId;
		$this->items = $items;
		$this->done = $done;
	}

	public function getOrderId(): string
	{
		return $this->orderId;
	}

	public function getItems(): array
	{
		return $this->items;
	}

	public function isDone(): bool
	{
		return $this->done;
	}
}
