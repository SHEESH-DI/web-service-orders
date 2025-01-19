<?php

namespace App\Model;

use App\Service\OrderService;
use mysqli;

class OrderModel {

	private mysqli $db;
	public function __construct(mysqli $db)
	{
		$this->db = $db;
	}

	public function save(array $data): OrderService
	{
		$orderId = uniqid();
		$stmt = $this->db->prepare("INSERT INTO orders (id, items, created_at) VALUES (?, ?, ?)");
		$json_encode = json_encode($data);
		$date = date("Y-m-d H:i:s");
		$stmt->bind_param('sss', $orderId, $json_encode, $date);
		$stmt->execute();

		return new OrderService($orderId, $data);
	}

	public function update(string $idOrders, string $items): bool
	{
		$stmt = $this->db->prepare("UPDATE orders SET items = ?, updated_at = ? WHERE id = ?");
		$date = date("Y-m-d H:i:s");
		$stmt->bind_param('sss', $items, $date, $idOrders);
		return $stmt->execute();
	}

	public function getOrder(string $id): ?OrderService
	{
		$stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();

		return $result ? new OrderService($result['id'], json_decode($result['items'], true), (bool)$result['done']) : null;
	}

	public function updateStatusOrders(string $orderId, int $status): bool
	{
		$stmt = $this->db->prepare("UPDATE orders SET done = ?, updated_at = ? WHERE id = ?");
		$date = date("Y-m-d H:i:s");
		$stmt->bind_param('iss', $status, $date, $orderId);
		return $stmt->execute();
	}

	public function getOrdersWithStatus(bool $done): array
	{
		$stmt = $this->db->prepare("SELECT * FROM orders WHERE done = ?");
		$status = (int) $done;
		$stmt->bind_param('i', $status);
		$stmt->execute();
		$result = $stmt->get_result();

		return array_map(
			function($row) {
				return new OrderService($row['id'], json_decode($row['items'], true), (bool)$row['done']);
			},
			$result->fetch_all(MYSQLI_ASSOC)
		);
	}

	public function getAllOrders(): array
	{
		$stmt = $this->db->prepare("SELECT * FROM orders");
		$stmt->execute();
		$result = $stmt->get_result();

		return array_map(
			function($row) {
				return new OrderService($row['id'], json_decode($row['items'], true), (bool)$row['done']);
			},
			$result->fetch_all(MYSQLI_ASSOC)
		);
	}
}