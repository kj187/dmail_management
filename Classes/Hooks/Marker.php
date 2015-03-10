<?php
namespace Aijko\DmailManagement\Hooks;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Julian Kleinhans <julian.kleinhans@aijko.com>, AIJKO GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class Marker
 * @package Aijko\DmailManagement\Hooks
 */
class Marker {

	/**
	 * Replace markers inside the templates
	 *
	 * @param array $params
	 * @param \DirectMailTeam\DirectMail\Dmailer $dmailer
	 * @return array
	 */
	function replaceMarker(array &$params, \DirectMailTeam\DirectMail\Dmailer $dmailer) {
		$fullname = $this->getName($params['row']);
		$params['markers']['Guten Tag,'] = ($fullname ? 'Guten Tag ' . $fullname . ',' : 'Guten Tag,');
	}

	/**
	 * @return string
	 */
	public function getName($row) {
		$name = array();

		if ($row['first_name']) {
			$name[] = $row['first_name'];
		}
		if ($row['last_name']) {
			$name[] = $row['last_name'];
		}

		if (!count($name)) {
			return '';
		}

		$fullname = implode(' ', $name);
		return $fullname;
	}
}