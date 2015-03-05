<?php
namespace Aijko\DmailManagement\Controller;

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

use \Aijko\DmailManagement\Domain\Repository\SubscriberRepository;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \Aijko\DmailManagement\Domain\Model\Subscriber;

/**
 * Class AccountManagementController
 * @package Aijko\DmailManagement\Controller
 */
class AccountManagementController extends ActionController {

	/**
	 * @var PersistenceManager
	 */
	protected $persistenceManager = NULL;

	/**
	 * @var SubscriberRepository
	 */
	protected $subscriberRepository = NULL;

	/**
	 * @var Typo3QuerySettings
	 */
	protected $querySettings = NULL;

	/**
	 * SignalSlot Dispatcher
	 *
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * Initialize before every action
	 */
	protected function initializeAction() {
		$this->persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$this->querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$this->subscriberRepository = $this->objectManager->get('Aijko\\DmailManagement\\Domain\\Repository\\SubscriberRepository');
		$this->querySettings->setStoragePageIds(array($this->settings['persistence']['storagePid']));
		$this->subscriberRepository->setDefaultQuerySettings($this->querySettings);
	}

	/**
	 * @param Subscriber $subscriber
	 * @param array $error
	 * @return void
	 */
	public function subscriptionAction(Subscriber $subscriber = NULL, array $error = array()) {
		if (NULL === $subscriber) {
			$subscriber = $this->objectManager->get('Aijko\\DmailManagement\\Domain\\Model\\Subscriber');
		}
		if ($this->request->hasArgument('prefillEmail') && $this->request->getArgument('prefillEmail')) {
			$subscriber->setEmail($this->request->getArgument('prefillEmail'));
		}
		$this->view->assign('subscriber', $subscriber);
		$this->view->assign('error', $error);
	}

	/**
	 * @param Subscriber $subscriber
	 */
	public function createAction(Subscriber $subscriber) {
		$error = array();
		if (!$subscriber->getEmail() || !GeneralUtility::validEmail($subscriber->getEmail())) {
			$error['email_invalid'] = TRUE;
		} else {
			$this->querySettings->setIgnoreEnableFields(TRUE);
			$this->subscriberRepository->setDefaultQuerySettings($this->querySettings);
			$emailExistingCount = $this->subscriberRepository->countByEmail($subscriber->getEmail());
			if ($emailExistingCount > 0) {
				$error['email_stillavailable'] = TRUE;
			}
		}
		if (count($error) > 0) {
			$this->forward('subscription', NULL, NULL, array('subscriber' => $subscriber, 'error' => $error));
		}
		$subscriber->setHidden(TRUE);
		$this->subscriberRepository->add($subscriber);
		$this->persistenceManager->persistAll();
		$authCode = $this->getSubscriberAuthCode($subscriber);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__ . 'AfterPersistence', array($subscriber, $authCode, $this->settings));
		$this->redirect('', NULL, NULL, NULL, $this->settings['subscription']['confirmation']['redirectPage']);
	}

	/**
	 * @param string $authCode
	 * @param int $subscriber
	 */
	public function activateAction($authCode, $subscriber) {
		try {
			$subscriber = $this->getSubscriberFromUid($subscriber);
			if (!$subscriber->isHidden()) {
				throw new \Exception('Account is already activated! #1425395866');
			}
			$this->checkSubscriberAuthCode($authCode, $subscriber);
			$subscriber->setHidden(FALSE);
			$this->subscriberRepository->update($subscriber);
		} catch(\Exception $exception) {
			$this->errorHandler($exception);
		}

		$this->redirect('', NULL, NULL, NULL, $this->settings['subscription']['activation']['redirectPage']);
	}

	/**
	 * @param string $authCode
	 * @param int $subscriber
	 */
	public function unsubscribeAction($authCode = '', $subscriber = 0) {
		try {
			$subscriber = $this->getSubscriberFromUid($subscriber);
			$this->checkSubscriberAuthCode($authCode, $subscriber);
			$this->subscriberRepository->remove($subscriber);
		} catch(\Exception $exception) {
			$this->errorHandler($exception);
		}
		$this->redirect('', NULL, NULL, NULL, $this->settings['unsubscription']['confirmation']['redirectPage']);
	}

	/**
	 * @param string $uid
	 * @return object
	 * @throws \Exception
	 */
	protected function getSubscriberFromUid($uid) {
		$this->querySettings->setIgnoreEnableFields(TRUE);
		$this->subscriberRepository->setDefaultQuerySettings($this->querySettings);
		$subscriber = $this->subscriberRepository->findByUid($uid);
		if (!$subscriber) {
			throw new \Exception('Subscriber could not be initialize, please contact the admin! #1425395864');
		}
		return $subscriber;
	}

	/**
	 * @param Subscriber $subscriber
	 * @return string
	 */
	protected function getSubscriberAuthCode(Subscriber $subscriber) {
		$authCode = GeneralUtility::stdAuthCode($subscriber->getUid(), 'uid');
		return $authCode;
	}

	/**
	 * @param string $authCode
	 * @param Subscriber $subscriber
	 * @return void
	 * @throws \Exception
	 */
	protected function checkSubscriberAuthCode($authCode, Subscriber $subscriber) {
		$originalAuthCode = $this->getSubscriberAuthCode($subscriber);
		if ($originalAuthCode !== $authCode) {
			throw new \Exception('Please do not manipulate the URL! #1425395865');
		}
	}

	/**
	 * @param \Exception $exception
	 */
	protected function errorHandler(\Exception $exception) {
		die($exception->getMessage());
	}
}