<?php
/*
** Zabbix
** Copyright (C) 2001-2015 Zabbix SIA
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


/**
 * Validate import data from Zabbix 1.8.
 */
class C10XmlValidator {

	/**
	 * Base validation function.
	 *
	 * @param array  $data	import data
	 * @param string $path	XML path (for error reporting)
	 *
	 * @return array		Validator does some manipulation for the incoming data. For example, converts empty tags to
	 *						an array, if desired. Converted array is returned.
	 */
	public function validate(array $data, $path) {
		$rules = ['type' => XML_ARRAY, 'rules' => [
			'version' =>				['type' => XML_STRING | XML_REQUIRED],
			'date' =>					['type' => XML_STRING, 'ex_validate' => [$this, 'validateDate']],
			'time' =>					['type' => XML_STRING, 'ex_validate' => [$this, 'validateTime']],
			'hosts' =>					['type' => XML_INDEXED_ARRAY, 'prefix' => 'host', 'rules' => [
				'host' =>					['type' => XML_ARRAY, 'rules' => [
					'name' =>					['type' => XML_STRING | XML_REQUIRED],
					'proxy_hostid' =>			['type' => XML_STRING | XML_REQUIRED],
					'useip' =>					['type' => XML_STRING | XML_REQUIRED],
					'dns' =>					['type' => XML_STRING | XML_REQUIRED],
					'ip' =>						['type' => XML_STRING | XML_REQUIRED],
					'port' =>					['type' => XML_STRING | XML_REQUIRED],
					'status' =>					['type' => XML_STRING | XML_REQUIRED],
					'useipmi' =>				['type' => XML_STRING],
					'ipmi_ip' =>				['type' => XML_STRING],
					'ipmi_port' =>				['type' => XML_STRING],
					'ipmi_authtype' =>			['type' => XML_STRING],
					'ipmi_privilege' =>			['type' => XML_STRING],
					'ipmi_username' =>			['type' => XML_STRING],
					'ipmi_password' =>			['type' => XML_STRING],
					'groups' =>					['type' => XML_INDEXED_ARRAY | XML_REQUIRED, 'prefix' => 'group', 'rules' => [
						'group' =>					['type' => XML_STRING]
					]],
					'items' =>					['type' => XML_INDEXED_ARRAY, 'prefix' => 'item', 'rules' => [
						'item' =>					['type' => XML_ARRAY, 'rules' => [
							'type' =>					['type' => XML_STRING | XML_REQUIRED],
							'key' =>					['type' => XML_STRING | XML_REQUIRED],
							'value_type' =>				['type' => XML_STRING | XML_REQUIRED],
							'description' =>			['type' => XML_STRING | XML_REQUIRED],
							'ipmi_sensor' =>			['type' => XML_STRING | XML_REQUIRED],
							'delay' =>					['type' => XML_STRING | XML_REQUIRED],
							'history' =>				['type' => XML_STRING | XML_REQUIRED],
							'trends' =>					['type' => XML_STRING | XML_REQUIRED],
							'status' =>					['type' => XML_STRING | XML_REQUIRED],
							'data_type' =>				['type' => XML_STRING | XML_REQUIRED],
							'units' =>					['type' => XML_STRING | XML_REQUIRED],
							'multiplier' =>				['type' => XML_STRING | XML_REQUIRED],
							'delta' =>					['type' => XML_STRING | XML_REQUIRED],
							'formula' =>				['type' => XML_STRING | XML_REQUIRED],
							'lastlogsize' =>			['type' => XML_STRING | XML_REQUIRED],
							'logtimefmt' =>				['type' => XML_STRING | XML_REQUIRED],
							'delay_flex' =>				['type' => XML_STRING | XML_REQUIRED],
							'authtype' =>				['type' => XML_STRING | XML_REQUIRED],
							'username' =>				['type' => XML_STRING | XML_REQUIRED],
							'password' =>				['type' => XML_STRING | XML_REQUIRED],
							'publickey' =>				['type' => XML_STRING | XML_REQUIRED],
							'privatekey' =>				['type' => XML_STRING | XML_REQUIRED],
							'params' =>					['type' => XML_STRING | XML_REQUIRED],
							'trapper_hosts' =>			['type' => XML_STRING | XML_REQUIRED],
							'snmp_community' =>			['type' => XML_STRING | XML_REQUIRED],
							'snmp_oid' =>				['type' => XML_STRING | XML_REQUIRED],
							'snmp_port' =>				['type' => XML_STRING | XML_REQUIRED],
							'snmpv3_securityname' =>	['type' => XML_STRING | XML_REQUIRED],
							'snmpv3_securitylevel' =>	['type' => XML_STRING | XML_REQUIRED],
							'snmpv3_authpassphrase' =>	['type' => XML_STRING | XML_REQUIRED],
							'snmpv3_privpassphrase' =>	['type' => XML_STRING | XML_REQUIRED],
							'valuemapid' =>				['type' => XML_STRING],
							'applications' =>			['type' => XML_INDEXED_ARRAY, 'prefix' => 'application', 'rules' => [
								'application' =>			['type' => XML_STRING]
							]]
						]]
					]],
					'triggers' =>				['type' => XML_INDEXED_ARRAY, 'prefix' => 'trigger', 'rules' => [
						'trigger' =>				['type' => XML_ARRAY, 'rules' => [
							'description' =>			['type' => XML_STRING | XML_REQUIRED],
							'type' =>					['type' => XML_STRING | XML_REQUIRED],
							'expression' =>				['type' => XML_STRING | XML_REQUIRED],
							'url' =>					['type' => XML_STRING | XML_REQUIRED],
							'status' =>					['type' => XML_STRING | XML_REQUIRED],
							'priority' =>				['type' => XML_STRING | XML_REQUIRED],
							'comments' =>				['type' => XML_STRING | XML_REQUIRED]
						]]
					]],
					'templates' =>				['type' => XML_INDEXED_ARRAY, 'prefix' => 'template', 'rules' => [
						'template' =>				['type' => XML_STRING]
					]],
					'graphs' =>					['type' => XML_INDEXED_ARRAY, 'prefix' => 'graph', 'rules' => [
						'graph' =>					['type' => XML_ARRAY, 'rules' => [
							'name' =>					['type' => XML_STRING | XML_REQUIRED],
							'width' =>					['type' => XML_STRING | XML_REQUIRED],
							'height' =>					['type' => XML_STRING | XML_REQUIRED],
							'ymin_type' =>				['type' => XML_STRING | XML_REQUIRED],
							'ymax_type' =>				['type' => XML_STRING | XML_REQUIRED],
							'ymin_item_key' =>			['type' => XML_STRING | XML_REQUIRED],
							'ymax_item_key' =>			['type' => XML_STRING | XML_REQUIRED],
							'show_work_period' =>		['type' => XML_STRING | XML_REQUIRED],
							'show_triggers' =>			['type' => XML_STRING | XML_REQUIRED],
							'graphtype' =>				['type' => XML_STRING | XML_REQUIRED],
							'yaxismin' =>				['type' => XML_STRING | XML_REQUIRED],
							'yaxismax' =>				['type' => XML_STRING | XML_REQUIRED],
							'show_legend' =>			['type' => XML_STRING | XML_REQUIRED],
							'show_3d' =>				['type' => XML_STRING | XML_REQUIRED],
							'percent_left' =>			['type' => XML_STRING | XML_REQUIRED],
							'percent_right' =>			['type' => XML_STRING | XML_REQUIRED],
							'graph_elements' =>			['type' => XML_INDEXED_ARRAY | XML_REQUIRED, 'prefix' => 'graph_element', 'rules' => [
								'graph_element' =>			['type' => XML_ARRAY, 'rules' => [
									'item' =>					['type' => XML_STRING | XML_REQUIRED],
									'drawtype' =>				['type' => XML_STRING | XML_REQUIRED],
									'sortorder' =>				['type' => XML_STRING | XML_REQUIRED],
									'color' =>					['type' => XML_STRING | XML_REQUIRED],
									'yaxisside' =>				['type' => XML_STRING | XML_REQUIRED],
									'calc_fnc' =>				['type' => XML_STRING | XML_REQUIRED],
									'type' =>					['type' => XML_STRING | XML_REQUIRED],
									'periods_cnt' =>			['type' => XML_STRING | XML_REQUIRED]
								]]
							]]
						]]
					]],
					'macros' =>					['type' => XML_INDEXED_ARRAY, 'prefix' => 'macro', 'rules' => [
						'macro' =>					['type' => XML_ARRAY, 'rules' => [
							'value' =>					['type' => XML_STRING | XML_REQUIRED],
							'name' =>					['type' => XML_STRING | XML_REQUIRED]
						]]
					]]
				]]
			]],
			'dependencies' =>			['type' => XML_INDEXED_ARRAY, 'prefix' => 'dependency', 'rules' => [
				'dependency' =>				['type' => XML_INDEXED_ARRAY, 'prefix' => 'depends', 'extra' => 'description', 'rules' => [
					'depends' =>				['type' => XML_STRING],
					'description' =>			['type' => XML_STRING | XML_REQUIRED]
				]]
			]],
			'sysmaps' =>				['type' => XML_INDEXED_ARRAY, 'prefix' => 'sysmap', 'rules' => [
				'sysmap' =>					['type' => XML_ARRAY, 'rules' => [
					'selements' =>				['type' => XML_INDEXED_ARRAY | XML_REQUIRED, 'prefix' => 'selement', 'rules' => [
						'selement' =>				['type' => XML_ARRAY, 'rules' => [
							'selementid' =>				['type' => XML_STRING | XML_REQUIRED],
							'elementid' =>				['type' => XML_ARRAY, 'rules' => [
								'name' =>					['type' => XML_STRING],
								'host' =>					['type' => XML_STRING],
								'description' =>			['type' => XML_STRING],
								'expression' =>				['type' => XML_STRING]
							]],
							'elementtype' =>			['type' => XML_STRING | XML_REQUIRED],
							'iconid_on' =>				['type' => XML_ARRAY, 'rules' => [
								'name' =>					['type' => XML_STRING | XML_REQUIRED]
							]],
							'iconid_off' =>				['type' => XML_ARRAY | XML_REQUIRED, 'rules' => [
								'name' =>					['type' => XML_STRING | XML_REQUIRED]
							]],
							'iconid_unknown' =>			['type' => XML_ARRAY, 'rules' => [
								'name' =>					['type' => XML_STRING | XML_REQUIRED]
							]],
							'iconid_disabled' =>		['type' => XML_ARRAY, 'rules' => [
								'name' =>					['type' => XML_STRING | XML_REQUIRED]
							]],
							'iconid_maintenance' =>		['type' => XML_ARRAY, 'rules' => [
								'name' =>					['type' => XML_STRING | XML_REQUIRED]
							]],
							'label' =>					['type' => XML_STRING | XML_REQUIRED],
							'label_location' =>			['type' => XML_STRING | XML_REQUIRED],
							'x' =>						['type' => XML_STRING | XML_REQUIRED],
							'y' =>						['type' => XML_STRING | XML_REQUIRED],
							'url' =>					['type' => XML_STRING]
						]]
					]],
					'links' =>					['type' => XML_INDEXED_ARRAY | XML_REQUIRED, 'prefix' => 'link', 'rules' => [
						'link' =>					['type' => XML_ARRAY, 'rules' => [
							'selementid1' =>			['type' => XML_STRING | XML_REQUIRED],
							'selementid2' =>			['type' => XML_STRING | XML_REQUIRED],
							'drawtype' =>				['type' => XML_STRING | XML_REQUIRED],
							'color' =>					['type' => XML_STRING | XML_REQUIRED],
							'label' =>					['type' => XML_STRING],
							'linktriggers' =>			['type' => XML_INDEXED_ARRAY, 'prefix' => 'linktrigger', 'rules' => [
								'linktrigger' =>			['type' => XML_ARRAY, 'rules' => [
									'drawtype' =>				['type' => XML_STRING | XML_REQUIRED],
									'color' =>					['type' => XML_STRING | XML_REQUIRED],
									'triggerid' =>				['type' => XML_ARRAY | XML_REQUIRED, 'rules' => [
										'host' =>					['type' => XML_STRING],
										'description' =>			['type' => XML_STRING | XML_REQUIRED],
										'expression' =>				['type' => XML_STRING | XML_REQUIRED],
									]]
								]]
							]]
						]]
					]],
					'name' =>					['type' => XML_STRING | XML_REQUIRED],
					'width' =>					['type' => XML_STRING | XML_REQUIRED],
					'height' =>					['type' => XML_STRING | XML_REQUIRED],
					'backgroundid' =>			['type' => XML_ARRAY, 'rules' => [
						'name' =>					['type' => XML_STRING | XML_REQUIRED]
					]],
					'label_type' =>				['type' => XML_STRING | XML_REQUIRED],
					'label_location' =>			['type' => XML_STRING | XML_REQUIRED],
					'highlight' =>				['type' => XML_STRING | XML_REQUIRED],
					'expandproblem' =>			['type' => XML_STRING | XML_REQUIRED],
					'markelements' =>			['type' => XML_STRING | XML_REQUIRED],
					'show_unack' =>				['type' => XML_STRING | XML_REQUIRED]
				]]
			]],
			'screens' =>				['type' => XML_INDEXED_ARRAY, 'prefix' => 'screen', 'rules' => [
				'screen' =>					['type' => XML_ARRAY, 'rules' => [
					'name' =>					['type' => XML_STRING | XML_REQUIRED],
					'hsize' =>					['type' => XML_STRING | XML_REQUIRED],
					'vsize' =>					['type' => XML_STRING | XML_REQUIRED],
					'screenitems' =>			['type' => XML_INDEXED_ARRAY | XML_REQUIRED, 'prefix' => 'screenitem', 'rules' => [
						'screenitem' =>				['type' => XML_ARRAY, 'rules' => [
							'resourcetype' =>			['type' => XML_STRING | XML_REQUIRED],
							'resourceid' =>				['type' => XML_REQUIRED],
							'width' =>					['type' => XML_STRING | XML_REQUIRED],
							'height' =>					['type' => XML_STRING | XML_REQUIRED],
							'x' =>						['type' => XML_STRING | XML_REQUIRED],
							'y' =>						['type' => XML_STRING | XML_REQUIRED],
							'colspan' =>				['type' => XML_STRING | XML_REQUIRED],
							'rowspan' =>				['type' => XML_STRING | XML_REQUIRED],
							'elements' =>				['type' => XML_STRING | XML_REQUIRED],
							'valign' =>					['type' => XML_STRING | XML_REQUIRED],
							'halign' =>					['type' => XML_STRING | XML_REQUIRED],
							'style' =>					['type' => XML_STRING | XML_REQUIRED],
							'dynamic' =>				['type' => XML_STRING | XML_REQUIRED],
							'url' =>					['type' => XML_STRING]
						]]
					]]
				]]
			]],
			'images' =>					['type' => XML_INDEXED_ARRAY, 'prefix' => 'image', 'rules' => [
				'image' =>					['type' => XML_ARRAY, 'rules' => [
					'name' =>					['type' => XML_STRING | XML_REQUIRED],
					'imagetype' =>				['type' => XML_STRING | XML_REQUIRED],
					'encodedImage' =>			['type' => XML_STRING | XML_REQUIRED]
				]]
			]]
		]];

		return (new CXmlValidatorGeneral($rules))->validate($data, $path);
	}

	/**
	 * Validate date format.
	 *
	 * @param string $date	export date
	 * @param string $path	XML path
	 *
	 * @throws Exception	if the date is invalid
	 */
	public function validateDate($date, $path) {
		if (!preg_match('/^(0[1-9]|[1-2][0-9]|3[01])\.(0[1-9]|1[0-2])\.[0-9]{2}$/', $date)) {
			throw new Exception(_s('Invalid XML tag "%1$s": %2$s.', $path, _s('"%1$s" is expected', _x('DD.MM.YY', 'XML date format'))));
		}

		return $date;
	}

	/**
	 * Validate time format.
	 *
	 * @param string $time	export time
	 * @param string $path	XML path
	 *
	 * @throws Exception	if the time is invalid
	 */
	public function validateTime($time, $path) {
		if (!preg_match('/^(2[0-3]|[01][0-9])\.[0-5][0-9]$/', $time)) {
			throw new Exception(_s('Invalid XML tag "%1$s": %2$s.', $path, _s('"%1$s" is expected', _x('hh.mm', 'XML time format'))));
		}

		return $time;
	}
}
