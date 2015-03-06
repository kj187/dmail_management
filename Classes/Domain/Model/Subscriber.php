<?php
namespace Aijko\DmailManagement\Domain\Model;

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
 * Class Subscriber
 * @package Aijko\DmailManagement\Domain\Model
 */
class Subscriber extends \TYPO3\TtAddress\Domain\Model\Address {

	/**
	 * @var bool
	 */
	protected $moduleSysDmailHtml = TRUE;

	/**
	 * @var bool
	 */
	protected $hidden = TRUE;

	/**
	 * @var string
	 */
	protected $clientip = '';

	/**
	 * @var string
	 */
	protected $timeSubscription;

	/**
	 * @var string
	 */
	protected $timeSendsubscriptionmail;

	/**
	 * @var string
	 */
	protected $timeApprovesubscription;

	/**
	 * @return boolean
	 */
	public function isModuleSysDmailHtml() {
		return $this->moduleSysDmailHtml;
	}

	/**
	 * @param boolean $moduleSysDmailHtml
	 */
	public function setModuleSysDmailHtml($moduleSysDmailHtml) {
		$this->moduleSysDmailHtml = $moduleSysDmailHtml;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @return string
	 */
	public function getName() {
		$name[] = $this->getFirstName();
		$name[] = $this->getLastName();

		$fullname = implode(' '. $name);
		return $fullname;
	}

	/**
	 * @return string
	 */
	public function getClientip() {
		return $this->clientip;
	}

	/**
	 * @param string $clientip
	 */
	public function setClientip($clientip) {
		$this->clientip = $clientip;
	}

	/**
	 * @return string
	 */
	public function getTimeSubscription() {
		return $this->timeSubscription;
	}

	/**
	 *
	 */
	public function setTimeSubscription() {
		$this->timeSubscription = time();
	}

	/**
	 * @return string
	 */
	public function getTimeSendsubscriptionmail() {
		return $this->timeSendsubscriptionmail;
	}

	/**
	 *
	 */
	public function setTimeSendsubscriptionmail() {
		$this->timeSendsubscriptionmail = time();
	}

	/**
	 * @return string
	 */
	public function getTimeApprovesubscription() {
		return $this->timeApprovesubscription;
	}

	/**
	 *
	 */
	public function setTimeApprovesubscription() {
		$this->timeApprovesubscription = time();
	}
}