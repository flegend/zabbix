<?php
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


class CControllerIconMapCreate extends CController {

	protected function checkInput() {
		$fields = [
			'iconmap'   => 'required | array'
		];

		$ret = $this->validateInput($fields);

		if (!$ret) {
			$this->setResponse(new CControllerResponseFatal());
		}

		return $ret;
	}

	protected function checkPermissions() {
		return ($this->getUserType() == USER_TYPE_SUPER_ADMIN);
	}

	protected function doAction() {
		$result = (bool) API::IconMap()->create((array) $this->getInput('iconmap'));

		$url = new CUrl('zabbix.php');
		if ($result) {
			$response = new CControllerResponseRedirect($url->setArgument('action', 'iconmap.list'));
			$response->setMessageOk(_('Icon map created'));
		}
		else {
			$response = new CControllerResponseRedirect($url->setArgument('action', 'iconmap.edit'));
			$response->setFormData($this->getInputAll());
			$response->setMessageError(_('Cannot create icon map'));
		}

		$this->setResponse($response);
	}
}