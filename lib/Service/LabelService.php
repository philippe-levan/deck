<?php
/**
 * @copyright Copyright (c) 2016 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
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

namespace OCA\Deck\Service;

use OCA\Deck\Db\Label;
use OCA\Deck\Db\Acl;
use OCA\Deck\Db\LabelMapper;
use OCA\Deck\StatusException;
use OCA\Deck\BadRequestException;


class LabelService {

	/** @var LabelMapper */
	private $labelMapper;
	/** @var PermissionService */
	private $permissionService;
	/** @var BoardService */
	private $boardService;

	public function __construct(LabelMapper $labelMapper, PermissionService $permissionService, BoardService $boardService) {
		$this->labelMapper = $labelMapper;
		$this->permissionService = $permissionService;
		$this->boardService = $boardService;
	}

	/**
	 * @param $labelId
	 * @return \OCP\AppFramework\Db\Entity
	 * @throws \OCA\Deck\NoPermissionException
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws BadRequestException
	 */
	public function find($labelId) {
		if (is_numeric($labelId) === false) {
			throw new BadRequestException('label id must be a number');
		}
		$this->permissionService->checkPermission($this->labelMapper, $labelId, Acl::PERMISSION_READ);
		return $this->labelMapper->find($labelId);
	}

	/**
	 * @param $title
	 * @param $color
	 * @param $boardId
	 * @return \OCP\AppFramework\Db\Entity
	 * @throws StatusException
	 * @throws \OCA\Deck\NoPermissionException
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function create($title, $color, $boardId) {

		if ($title === false || $title === null) {
			throw new BadRequestException('title must be provided');
		}

		if ($color === false || $color === null) {
			throw new BadRequestException('color must be provided');
		}

		if (is_numeric($boardId) === false) {
			throw new BadRequestException('board id must be a number');
		}

		$this->permissionService->checkPermission(null, $boardId, Acl::PERMISSION_MANAGE);
		if ($this->boardService->isArchived(null, $boardId)) {
			throw new StatusException('Operation not allowed. This board is archived.');
		}
		$label = new Label();
		$label->setTitle($title);
		$label->setColor($color);
		$label->setBoardId($boardId);
		return $this->labelMapper->insert($label);
	}

	/**
	 * @param $id
	 * @return \OCP\AppFramework\Db\Entity
	 * @throws StatusException
	 * @throws \OCA\Deck\NoPermissionException
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function delete($id) {

		if (is_numeric($id) === false) {
			throw new BadRequestException('label id must be a number');
		}

		$this->permissionService->checkPermission($this->labelMapper, $id, Acl::PERMISSION_MANAGE);
		if ($this->boardService->isArchived($this->labelMapper, $id)) {
			throw new StatusException('Operation not allowed. This board is archived.');
		}
		return $this->labelMapper->delete($this->find($id));
	}

	/**
	 * @param $id
	 * @param $title
	 * @param $color
	 * @return \OCP\AppFramework\Db\Entity
	 * @throws StatusException
	 * @throws \OCA\Deck\NoPermissionException
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function update($id, $title, $color) {

		if (is_numeric($id) === false) {
			throw new BadRequestException('label id must be a number');
		}

		if ($title === false || $title === null) {
			throw new BadRequestException('title must be provided');
		}

		if ($color === false || $color === null) {
			throw new BadRequestException('color must be provided');
		}

		$this->permissionService->checkPermission($this->labelMapper, $id, Acl::PERMISSION_MANAGE);
		if ($this->boardService->isArchived($this->labelMapper, $id)) {
			throw new StatusException('Operation not allowed. This board is archived.');
		}
		$label = $this->find($id);
		$label->setTitle($title);
		$label->setColor($color);
		return $this->labelMapper->update($label);
	}

}