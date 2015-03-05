<?php
namespace Aijko\DmailManagement\Service;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Object\ObjectManager;
use \Aijko\DmailManagement\Domain\Model\Subscriber;

/**
 * Class NotificationService
 * @package Aijko\DmailManagement\Service
 */
class NotificationService {

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
	}

	/**
	 * @param Subscriber $subscriber
	 * @param string $authCode
	 * @param array $settings
	 */
	public function subscriptionSuccess(Subscriber $subscriber, $authCode, array $settings) {
		$this->settings = $settings;
		$body = $this->getStandaloneView(array('subscriber' => $subscriber, 'authCode' => $authCode), 'Notification/SubscriptionSuccess.txt')->render();
		$this->send(
			$this->settings['subscription']['confirmation']['mail']['subject'],
			$body,
			array($this->settings['subscription']['confirmation']['mail']['fromEmail'] => $this->settings['subscription']['confirmation']['mail']['fromName']),
			array($subscriber->getEmail() => $subscriber->getName())
		);
	}

	/**
	 * @param array $variables
	 * @return string
	 */
	protected function getStandaloneView(array $variables, $template) {
		$templateRootPaths  = array_reverse($this->settings['view']['templateRootPaths']);
		foreach ($templateRootPaths as $templateRootPath) {
			$templateRootPath = GeneralUtility::getFileAbsFileName($templateRootPath);
			if (file_exists($templateRootPath)) {
				$templatePathAndFilename = $templateRootPath . $template;
				break;
			}
		}

		$viewObject = $this->objectManager->create('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$viewObject->setFormat('plain');
		$viewObject->setTemplatePathAndFilename($templatePathAndFilename);
		$viewObject->assignMultiple($variables);
		return $viewObject;
	}

	/**
	 * @param string $subject
	 * @param string $body
	 * @param array $from
	 * @param array $to
	 */
	protected function send($subject, $body, array $from, array $to) {
		$mail = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$mail->setFrom($from)
			->setTo($to)
			->setSubject($subject)
			->setBody($body)
			->send();
	}
}