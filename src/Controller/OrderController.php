<?php

namespace App\Controller;

use App\Model\OrderModel;
use App\Service\OrderService;
use App\Validator\OrderValidator;
use Exception;

class OrderController extends AbstractController
{
	private OrderModel $OrderModel;
	private OrderValidator $OrderValidator;

	private const string X_AUTH_KEY = 'qwerty123';

	public function __construct(OrderModel $orderModel) {
		$this->OrderModel = $orderModel;
		$this->OrderValidator = new OrderValidator();
	}

	public function createNewOrder(): void
	{
		$data = $this->getData();

		if (!$data) {
			$this->jsonResponse(['message' => 'Error key'], 400);
		}

		try {
			$this->OrderValidator->validateItems($data['items']);
			$order = $this->OrderModel->save($data['items']);
			$this->jsonResponse(['order_id' => $order->getItems(), 'items' => $order->getOrderId(), 'done' => $order->isDone()]);
		} catch (Exception $e) {
			$this->jsonResponse(['message' => $e->getMessage()], 400);
		}
	}

	public function updateOrders($id): void
	{
		$data = $this->getData();

		try {
			$this->OrderValidator->validateItems($data);
			$this->OrderValidator->validateItems($id);

			$order = $this->OrderModel->getOrder($id);

			if ($order && $order->isDone()) {
				$this->OrderModel->update($id, json_encode(array_merge($order->getItems(), $data)));
				$this->jsonResponse(['message' => 'Items added']);
			}

			$this->jsonResponse(['message' => 'Orders not done']);
		} catch (Exception $e) {
			$this->jsonResponse(['message' => $e->getMessage()], 400);
		}
	}

	public function getInfoOrders(string $orderId): void
	{
		try {
			$this->OrderValidator->validateItems($orderId);
			$order = $this->OrderModel->getOrder($orderId);
			if (!$order) throw new Exception('Order not found');

			$this->jsonResponse(['order_id' => $order->getOrderId(), 'items' => $order->getItems(), 'done' => $order->isDone()]);
		} catch (Exception $e) {
			$this->jsonResponse(['message' => $e->getMessage()], 400);
		}


	}

	public function orderUpdateDone(string $orderId): void {
		if (isset($_SERVER['HTTP_X_AUTH_TOKEN']) && $_SERVER['HTTP_X_AUTH_TOKEN'] !== self::X_AUTH_KEY) {
			$this->jsonResponse(['message' => 'Unauthorized'], 403);
		}

		try {
			$order = $this->OrderModel->getOrder($orderId);
			if (!$order) throw new Exception('Order not found');

			if (!$order->isDone()) {
				$this->OrderModel->updateStatusOrders($orderId, OrderService::STATUS_ORDER_DONE);
				$this->jsonResponse(['message' => 'Order is done']);
			}
			$this->jsonResponse(['message' => 'Order already done']);
		} catch (Exception $e) {
			$this->jsonResponse(['message' => $e->getMessage()], 400);
		}
	}

	public function getAllOrders($done = null): void {
		if (isset($_SERVER['HTTP_X_AUTH_TOKEN']) && $_SERVER['HTTP_X_AUTH_TOKEN'] !== self::X_AUTH_KEY) {
			$this->jsonResponse(['message' => 'Unauthorized'], 403);
		}

		if (!isset($done)) {
			$orders = $this->OrderModel->getAllOrders();
			$this->jsonResponse(array_map(function ($o) {
				return ['order_id' => $o->getOrderId(), 'done' => $o->isDone()];
			}, $orders));
		}

		try {
			$orders = $this->OrderModel->getOrdersWithStatus((bool)$_GET['done']);
			$this->jsonResponse(array_map(function ($o) {
				return ['order_id' => $o->getOrderId(), 'done' => $o->isDone()];
			}, $orders));
		} catch (Exception $e) {
			$this->jsonResponse(['message' => $e->getMessage()], 400);
		}
	}
}
