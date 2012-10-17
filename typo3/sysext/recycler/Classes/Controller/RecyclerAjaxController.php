<?php
/**
 * Controller class for the 'recycler' extension. Handles the AJAX Requests
 *
 * @author 		Julian Kleinhans <typo3@kj187.de>
 * @author 	Erik Frister <erik_frister@otq-solutions.com>
 * @package 		TYPO3
 * @subpackage 	tx_recycler
 */
class tx_recycler_controller_ajax {

	/**
	 * Stores the content for the ajax output
	 *
	 * @var 	string
	 */
	protected $content;

	/**
	 * Command to be processed
	 *
	 * @var 	string
	 */
	protected $command;

	/**
	 * Stores relevant data from extJS
	 * Example: Json format
	 * [ ["pages",1],["pages",2],["tt_content",34] ]
	 *
	 * @var 	string
	 */
	protected $data;

	/**
	 * Initialize method
	 *
	 * @return void
	 */
	public function init() {
		$this->mapCommand();
		$this->getContent();
	}

	/**
	 * Maps the command to the correct Model and View
	 *
	 * @return void
	 */
	public function mapCommand() {
		$this->command = t3lib_div::_GP('cmd');
		$this->data = t3lib_div::_GP('data');
		// check params
		if (!is_string($this->command)) {
			// @TODO make devlog output
			return FALSE;
		}
		// Create content
		$this->createContent();
	}

	/**
	 * Creates the content
	 *
	 * @return void
	 */
	protected function createContent() {
		$str = '';
		switch ($this->command) {
		case 'getDeletedRecords':
			$table = t3lib_div::_GP('table') ? t3lib_div::_GP('table') : t3lib_div::_GP('tableDefault');
			$limit = t3lib_div::_GP('limit') ? (int) t3lib_div::_GP('limit') : (int) t3lib_div::_GP('pagingSizeDefault');
			$start = t3lib_div::_GP('start') ? (int) t3lib_div::_GP('start') : 0;
			$filter = t3lib_div::_GP('filterTxt') ? t3lib_div::_GP('filterTxt') : '';
			$startUid = t3lib_div::_GP('startUid') ? t3lib_div::_GP('startUid') : '';
			$depth = t3lib_div::_GP('depth') ? t3lib_div::_GP('depth') : '';
			$this->setDataInSession('tableSelection', $table);
			$model = t3lib_div::makeInstance('tx_recycler_model_deletedRecords');
			$model->loadData($startUid, $table, $depth, ($start . ',') . $limit, $filter);
			$deletedRowsArray = $model->getDeletedRows();
			$model = t3lib_div::makeInstance('tx_recycler_model_deletedRecords');
			$totalDeleted = $model->getTotalCount($startUid, $table, $depth, $filter);
			// load view
			$view = t3lib_div::makeInstance('tx_recycler_view_deletedRecords');
			$str = $view->transform($deletedRowsArray, $totalDeleted);
			break;
		case 'doDelete':
			$str = FALSE;
			$model = t3lib_div::makeInstance('tx_recycler_model_deletedRecords');
			if ($model->deleteData($this->data)) {
				$str = TRUE;
			}
			break;
		case 'doUndelete':
			$str = FALSE;
			$recursive = t3lib_div::_GP('recursive');
			$model = t3lib_div::makeInstance('tx_recycler_model_deletedRecords');
			if ($model->undeleteData($this->data, $recursive)) {
				$str = TRUE;
			}
			break;
		case 'getTables':
			$depth = t3lib_div::_GP('depth') ? t3lib_div::_GP('depth') : 0;
			$startUid = t3lib_div::_GP('startUid') ? t3lib_div::_GP('startUid') : '';
			$this->setDataInSession('depthSelection', $depth);
			$model = t3lib_div::makeInstance('tx_recycler_model_tables');
			$str = $model->getTables('json', 1, $startUid, $depth);
			break;
		default:
			$str = 'No command was recognized.';
			break;
		}
		$this->content = $str;
	}

	/**
	 * Returns the content that was created in the mapCommand method
	 *
	 * @return string
	 */
	public function getContent() {
		echo $this->content;
	}

	/**
	 * Sets data in the session of the current backend user.
	 *
	 * @param 	string		$identifier: The identifier to be used to set the data
	 * @param 	string		$data: The data to be stored in the session
	 * @return 	void
	 */
	protected function setDataInSession($identifier, $data) {
		$GLOBALS['BE_USER']->uc['tx_recycler'][$identifier] = $data;
		$GLOBALS['BE_USER']->writeUC();
	}

}

?>