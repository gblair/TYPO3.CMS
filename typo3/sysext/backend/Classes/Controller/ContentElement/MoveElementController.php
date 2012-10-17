<?php
/**
 * Script Class for rendering the move-element wizard display
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class SC_move_el {

	// Internal, static (eg. from GPvars):
	/**
	 * @todo Define visibility
	 */
	public $sys_language = 0;

	/**
	 * @todo Define visibility
	 */
	public $page_id;

	/**
	 * @todo Define visibility
	 */
	public $table;

	/**
	 * @todo Define visibility
	 */
	public $R_URI;

	/**
	 * @todo Define visibility
	 */
	public $input_moveUid;

	/**
	 * @todo Define visibility
	 */
	public $moveUid;

	/**
	 * @todo Define visibility
	 */
	public $makeCopy;

	/**
	 * Document template object
	 *
	 * @var mediumDoc
	 * @todo Define visibility
	 */
	public $doc;

	// Pages-select clause
	/**
	 * @todo Define visibility
	 */
	public $perms_clause;

	// Internal, dynamic:
	// Content for module accumulated here.
	/**
	 * @todo Define visibility
	 */
	public $content;

	/**
	 * Constructor, initializing internal variables.
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function init() {
		// Setting internal vars:
		$this->sys_language = intval(t3lib_div::_GP('sys_language'));
		$this->page_id = intval(t3lib_div::_GP('uid'));
		$this->table = t3lib_div::_GP('table');
		$this->R_URI = t3lib_div::sanitizeLocalUrl(t3lib_div::_GP('returnUrl'));
		$this->input_moveUid = t3lib_div::_GP('moveUid');
		$this->moveUid = $this->input_moveUid ? $this->input_moveUid : $this->page_id;
		$this->makeCopy = t3lib_div::_GP('makeCopy');
		// Select-pages where clause for read-access:
		$this->perms_clause = $GLOBALS['BE_USER']->getPagePermsClause(1);
		// Starting the document template object:
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->setModuleTemplate('templates/move_el.html');
		$this->doc->JScode = '';
		// Starting document content (header):
		$this->content = '';
		$this->content .= $this->doc->header($GLOBALS['LANG']->getLL('movingElement'));
	}

	/**
	 * Creating the module output.
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function main() {
		if ($this->page_id) {
			// Get record for element:
			$elRow = t3lib_BEfunc::getRecordWSOL($this->table, $this->moveUid);
			// Headerline: Icon, record title:
			$hline = t3lib_iconWorks::getSpriteIconForRecord($this->table, $elRow, array('id' => 'c-recIcon', 'title' => htmlspecialchars(t3lib_BEfunc::getRecordIconAltText($elRow, $this->table))));
			$hline .= t3lib_BEfunc::getRecordTitle($this->table, $elRow, TRUE);
			// Make-copy checkbox (clicking this will reload the page with the GET var makeCopy set differently):
			$hline .= $this->doc->spacer(5);
			$onClick = ('window.location.href=\'' . t3lib_div::linkThisScript(array('makeCopy' => !$this->makeCopy))) . '\';';
			$hline .= $this->doc->spacer(5);
			$hline .= (((((('<input type="hidden" name="makeCopy" value="0" />' . '<input type="checkbox" name="makeCopy" id="makeCopy" value="1"') . ($this->makeCopy ? ' checked="checked"' : '')) . ' onclick="') . htmlspecialchars($onClick)) . '" /> <label for="makeCopy" class="t3-label-valign-top">') . $GLOBALS['LANG']->getLL('makeCopy', 1)) . '</label>';
			// Add the header-content to the module content:
			$this->content .= $this->doc->section('', $hline, FALSE, TRUE);
			$this->content .= $this->doc->spacer(20);
			// Reset variable to pick up the module content in:
			$code = '';
			// IF the table is "pages":
			if ((string) $this->table == 'pages') {
				// Get page record (if accessible):
				$pageinfo = t3lib_BEfunc::readPageAccess($this->page_id, $this->perms_clause);
				if (is_array($pageinfo) && $GLOBALS['BE_USER']->isInWebMount($pageinfo['pid'], $this->perms_clause)) {
					// Initialize the position map:
					$posMap = t3lib_div::makeInstance('ext_posMap_pages');
					$posMap->moveOrCopy = $this->makeCopy ? 'copy' : 'move';
					// Print a "go-up" link IF there is a real parent page (and if the user has read-access to that page).
					if ($pageinfo['pid']) {
						$pidPageInfo = t3lib_BEfunc::readPageAccess($pageinfo['pid'], $this->perms_clause);
						if (is_array($pidPageInfo)) {
							if ($GLOBALS['BE_USER']->isInWebMount($pidPageInfo['pid'], $this->perms_clause)) {
								$code .= (((('<a href="' . htmlspecialchars(t3lib_div::linkThisScript(array('uid' => intval($pageinfo['pid']), 'moveUid' => $this->moveUid)))) . '">') . t3lib_iconWorks::getSpriteIcon('actions-view-go-up')) . t3lib_BEfunc::getRecordTitle('pages', $pidPageInfo, TRUE)) . '</a><br />';
							} else {
								$code .= (t3lib_iconWorks::getSpriteIconForRecord('pages', $pidPageInfo) . t3lib_BEfunc::getRecordTitle('pages', $pidPageInfo, TRUE)) . '<br />';
							}
						}
					}
					// Create the position tree:
					$code .= $posMap->positionTree($this->page_id, $pageinfo, $this->perms_clause, $this->R_URI);
				}
			}
			// IF the table is "tt_content":
			if ((string) $this->table == 'tt_content') {
				// First, get the record:
				$tt_content_rec = t3lib_BEfunc::getRecord('tt_content', $this->moveUid);
				// ?
				if (!$this->input_moveUid) {
					$this->page_id = $tt_content_rec['pid'];
				}
				// Checking if the parent page is readable:
				$pageinfo = t3lib_BEfunc::readPageAccess($this->page_id, $this->perms_clause);
				if (is_array($pageinfo) && $GLOBALS['BE_USER']->isInWebMount($pageinfo['pid'], $this->perms_clause)) {
					// Initialize the position map:
					$posMap = t3lib_div::makeInstance('ext_posMap_tt_content');
					$posMap->moveOrCopy = $this->makeCopy ? 'copy' : 'move';
					$posMap->cur_sys_language = $this->sys_language;
					// Headerline for the parent page: Icon, record title:
					$hline = t3lib_iconWorks::getSpriteIconForRecord('pages', $pageinfo, array('title' => htmlspecialchars(t3lib_BEfunc::getRecordIconAltText($pageinfo, 'pages'))));
					$hline .= t3lib_BEfunc::getRecordTitle('pages', $pageinfo, TRUE);
					// Load SHARED page-TSconfig settings and retrieve column list from there, if applicable:
					// SHARED page-TSconfig settings.
					$modTSconfig_SHARED = t3lib_BEfunc::getModTSconfig($this->page_id, 'mod.SHARED');
					$colPosArray = t3lib_div::callUserFunction('EXT:cms/classes/class.tx_cms_backendlayout.php:tx_cms_BackendLayout->getColPosListItemsParsed', $this->page_id, $this);
					foreach ($colPosArray as $colPos) {
						$colPosList .= $colPosList != '' ? ',' . $colPos[1] : $colPos[1];
					}
					// Removing duplicates, if any
					$colPosList = implode(',', array_unique(t3lib_div::intExplode(',', $colPosList)));
					// Adding parent page-header and the content element columns from position-map:
					$code = $hline . '<br />';
					$code .= $posMap->printContentElementColumns($this->page_id, $this->moveUid, $colPosList, 1, $this->R_URI);
					// Print a "go-up" link IF there is a real parent page (and if the user has read-access to that page).
					$code .= '<br />';
					$code .= '<br />';
					if ($pageinfo['pid']) {
						$pidPageInfo = t3lib_BEfunc::readPageAccess($pageinfo['pid'], $this->perms_clause);
						if (is_array($pidPageInfo)) {
							if ($GLOBALS['BE_USER']->isInWebMount($pidPageInfo['pid'], $this->perms_clause)) {
								$code .= (((('<a href="' . htmlspecialchars(t3lib_div::linkThisScript(array(
									'uid' => intval($pageinfo['pid']),
									'moveUid' => $this->moveUid
								)))) . '">') . t3lib_iconWorks::getSpriteIcon('actions-view-go-up')) . t3lib_BEfunc::getRecordTitle('pages', $pidPageInfo, TRUE)) . '</a><br />';
							} else {
								$code .= (t3lib_iconWorks::getSpriteIconForRecord('pages', $pidPageInfo) . t3lib_BEfunc::getRecordTitle('pages', $pidPageInfo, TRUE)) . '<br />';
							}
						}
					}
					// Create the position tree (for pages):
					$code .= $posMap->positionTree($this->page_id, $pageinfo, $this->perms_clause, $this->R_URI);
				}
			}
			// Add the $code content as a new section to the module:
			$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('selectPositionOfElement'), $code, FALSE, TRUE);
		}
		// Setting up the buttons and markers for docheader
		$docHeaderButtons = $this->getButtons();
		$markers['CSH'] = $docHeaderButtons['csh'];
		$markers['CONTENT'] = $this->content;
		// Build the <body> for the module
		$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('movingElement'));
		$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		$this->content .= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
	}

	/**
	 * Print out the accumulated content:
	 *
	 * @return void
	 * @todo Define visibility
	 */
	public function printContent() {
		echo $this->content;
	}

	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return array All available buttons as an assoc. array
	 */
	protected function getButtons() {
		$buttons = array(
			'csh' => '',
			'back' => ''
		);
		if ($this->page_id) {
			if ((string) $this->table == 'pages') {
				// CSH
				$buttons['csh'] = t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'move_el_pages', $GLOBALS['BACK_PATH'], '', TRUE);
			} elseif ((string) $this->table == 'tt_content') {
				// CSH
				$buttons['csh'] = t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'move_el_cs', $GLOBALS['BACK_PATH'], '', TRUE);
			}
			if ($this->R_URI) {
				// Back
				$buttons['back'] = ((((('<a href="' . htmlspecialchars($this->R_URI)) . '" class="typo3-goBack" title="') . $GLOBALS['LANG']->getLL('goBack', TRUE)) . '">') . t3lib_iconWorks::getSpriteIcon('actions-view-go-back')) . '</a>';
			}
		}
		return $buttons;
	}

}

?>