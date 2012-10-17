<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * No-document script
 * This is used by eg. the Doc module if no documents is registered as "open"
 * (a concept which is better known from the "classic backend"...)
 *
 * Revised for TYPO3 3.6 November/2003 by Kasper Skårhøj
 * XHTML compliant
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 */
require 'init.php';
$LANG->includeLLFile('EXT:lang/locallang_alt_doc.xml');
require_once t3lib_extMgm::extPath('opendocs') . 'class.tx_opendocs.php';
/*
 * @deprecated since 6.0, the classname SC_alt_doc_nodoc and this file is obsolete
 * and will be removed by 7.0. The class was renamed and is now located at:
 * typo3/sysext/backend/Classes/Controller/NoDocumentsOpenController.php
 */
require_once t3lib_extMgm::extPath('backend') . 'Classes/Controller/NoDocumentsOpenController.php';
// Make instance:
$SOBE = t3lib_div::makeInstance('SC_alt_doc_nodoc');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>