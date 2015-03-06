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

// Register plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Accountmanagement',
	'DirectMail: Subscription/Unsubscription'
);

// Add flexforms
$pluginSignature = str_replace('_','',$_EXTKEY) . '_accountmanagement';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_accountmanagement.xml');

// Add static typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'DirectMail Management');

// tt_address modified
$tt_address_cols = array(
	'clientip' => array(
		'label' => 'LLL:EXT:dmail_management/Resources/Private/Language/locallang_tca.xml:clientip',
		'exclude' => '1',
		'config' => array(
			'type' => 'input',
			'readOnly' => TRUE,
			'size' => '40',
			'eval' => 'trim',
			'max'  => '255'
		)
	),
	'time_subscription' => array(
		'label' => 'LLL:EXT:dmail_management/Resources/Private/Language/locallang_tca.xml:time_subscription',
		'exclude' => '1',
		'config' => array(
			'type' => 'input',
			'readOnly' => TRUE,
			'size' => 8,
			'max' => 20,
			'eval' => 'datetime',
			'default' => 0,
		)
	),
	'time_sendsubscriptionmail' => array(
		'label' => 'LLL:EXT:dmail_management/Resources/Private/Language/locallang_tca.xml:time_sendsubscriptionmail',
		'exclude' => '1',
		'config' => array(
			'type' => 'input',
			'readOnly' => TRUE,
			'size' => 8,
			'max' => 20,
			'eval' => 'datetime',
			'default' => 0,
		)
	),
	'time_approvesubscription' => array(
		'label' => 'LLL:EXT:dmail_management/Resources/Private/Language/locallang_tca.xml:time_approvesubscription',
		'exclude' => '1',
		'config' => array(
			'type' => 'input',
			'readOnly' => TRUE,
			'size' => 8,
			'max' => 20,
			'eval' => 'datetime',
			'default' => 0,
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address', $tt_address_cols);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCATypes('tt_address', 'clientip, time_subscription, time_sendsubscriptionmail, time_approvesubscription', '', 'after:module_sys_dmail_category');