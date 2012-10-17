<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2011 Dmitry Dulepov <dmitry@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
// Fix _GET/_POST values for authentication
if (isset($_GET['login_status'])) {
	$_POST['login_status'] = $_GET['login_status'];
}
define('TYPO3_MOD_PATH', 'sysext/openid/');
require_once '../../init.php';
/*
 * @deprecated since 6.0, the classname tx_openid_return and this file is obsolete
 * and will be removed by 7.0. The class was renamed and is now located at:
 * typo3/sysext/openid/Classes/OpenidReturn.php
 */
require_once t3lib_extMgm::extPath('openid') . 'Classes/OpenidReturn.php';
$module = t3lib_div::makeInstance('tx_openid_return');
/* @var tx_openid_return $module */
$module->main();
?>