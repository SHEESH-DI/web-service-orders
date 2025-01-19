<?php

namespace App\Validator;

use Exception;

class OrderValidator {

	/**
	 * @throws Exception
	 */
	public function validateItems($items): void
	{
		if (empty($items)) {
			throw new Exception("Items cannot be empty");
		}

	}
}