<?php
/**
 * Deleted Records View
 *
 * @author Erik Frister <erik_frister@otq-solutions.com>
 * @author Julian Kleinhans <typo3@kj187.de>
 * @package TYPO3
 * @subpackage tx_recycler
 */
class tx_recycler_view_deletedRecords {

	/**
	 * Transforms the rows for the deleted Records into the Array View necessary for ExtJS Ext.data.ArrayReader
	 *
	 * @param array     $rows   Array with table as key and array with all deleted rows
	 * @param integer	$totalDeleted: Number of deleted records in total, for PagingToolbar
	 * @return string   JSON Array
	 */
	public function transform($deletedRowsArray, $totalDeleted) {
		$total = 0;
		$jsonArray = array(
			'rows' => array()
		);
		// iterate
		if (is_array($deletedRowsArray) && count($deletedRowsArray) > 0) {
			foreach ($deletedRowsArray as $table => $rows) {
				$total += count($deletedRowsArray[$table]);
				foreach ($rows as $row) {
					$backendUser = t3lib_BEfunc::getRecord('be_users', $row[$GLOBALS['TCA'][$table]['ctrl']['cruser_id']], 'username', '', FALSE);
					$jsonArray['rows'][] = array(
						'uid' => $row['uid'],
						'pid' => $row['pid'],
						'table' => $table,
						'crdate' => t3lib_BEfunc::datetime($row[$GLOBALS['TCA'][$table]['ctrl']['crdate']]),
						'tstamp' => t3lib_BEfunc::datetime($row[$GLOBALS['TCA'][$table]['ctrl']['tstamp']]),
						'owner' => htmlspecialchars($backendUser['username']),
						'owner_uid' => $row[$GLOBALS['TCA'][$table]['ctrl']['cruser_id']],
						'tableTitle' => tx_recycler_helper::getUtf8String($GLOBALS['LANG']->sL($GLOBALS['TCA'][$table]['ctrl']['title'])),
						'title' => htmlspecialchars(tx_recycler_helper::getUtf8String(t3lib_BEfunc::getRecordTitle($table, $row))),
						'path' => tx_recycler_helper::getRecordPath($row['pid'])
					);
				}
			}
		}
		$jsonArray['total'] = $totalDeleted;
		return json_encode($jsonArray);
	}

}

?>