<?php

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

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Configure plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Aijko.' . $_EXTKEY,
	'Accountmanagement',
	array(
		'AccountManagement' => 'subscription, create, activate, unsubscribe',
		
	),
	// non-cacheable actions
	array(
		'AccountManagement' => 'subscription, create, activate, unsubscribe',
		
	)
);

// Register signal slot
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect('Aijko\\DmailManagement\\Controller\\AccountManagementController', 'createActionAfterPersistence', 'Aijko\\DmailManagement\\Service\\NotificationService', 'subscriptionSuccess');
unset($signalSlotDispatcher);