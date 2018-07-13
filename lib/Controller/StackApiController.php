<?php
/**
 * @copyright Copyright (c) 2017 Steven R. Baker <steven@stevenrbaker.com>
 *
 * @author Steven R. Baker <steven@stevenrbaker.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Deck\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Deck\StatusException;
use OCA\Deck\Service\StackService;

/**
 * Class StackApiController
 *
 * @package OCA\Deck\Controller
 */
class StackApiController extends ApiController {

	private $service;
	private $userInfo;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param StackService $service
	 */
	public function __construct($appName, IRequest $request, StackService $service) {
		parent::__construct($appName, $request);
		$this->service = $service;
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * Return all of the stacks in the specified board.
	 */
	public function index($boardId) {
		$stacks = $this->service->findAll($boardId);
		
		if ($stacks === false || $stacks === null) {
			return new DataResponse("No Stacks Found", HTTP::STATUS_NOT_FOUND);
		}

		return new DataResponse($stacks, HTTP::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * @params $title
	 * @params $order
	 *
	 * Create a stack with the specified title and order.
	 */
	public function create($boardId, $title, $order) {		

		try {						
			$stack = $this->service->create($title, $boardId, $order);			
		} catch (StatusException $e) {
			$errorMessage['error'] = $e->getMessage();
			return new DataResponse($errorMessage, HTTP::STATUS_INTERNAL_SERVER_ERROR);
		}
		
		return new DataResponse($stack, HTTP::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * @params $stackId
	 * @params $title
	 * @params $boardId
	 * @params $order
	 *
	 * Create a stack with the specified title and order.
	 */
	public function update($stackId, $title, $boardId, $order) {
		
		try {
			$stack = $this->service->update($stackId, $title, $boardId, $order);
		} catch (StatusException $e) {
			$errorMessage['error'] = $e->getMessage();
			return new DataResponse($errorMessage, HTTP::STATUS_INTERNAL_SERVER_ERROR);
		}
		
		return new DataResponse($stack, HTTP::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * @params $id
	 *
	 * Delete the stack specified by $id.  Return the board that was deleted.
	 */
	public function delete($boardId, $stackId) {
		$stack = $this->service->delete($stackId);

		if ($stack == false || $stack == null) {
			return new DataResponse("Stack Not Found", HTTP::STATUS_NOT_FOUND);
		}

		return new DataResponse($stack, HTTP::STATUS_OK);
	}

}
