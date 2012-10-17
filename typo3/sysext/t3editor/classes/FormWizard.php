<?php
/**
 * Wizard for tceforms
 *
 * @author Tobias Liebig <mail_typo3@etobi.de>
 */
class tx_t3editor_tceforms_wizard {

	/**
	 * Main function
	 *
	 * @param array $parameters
	 * @param object $pObj
	 * @return string|NULL
	 */
	public function main($parameters, $pObj) {
		$t3editor = t3lib_div::makeInstance('tx_t3editor');
		if (!$t3editor->isEnabled()) {
			return;
		}
		if ($parameters['params']['format'] !== '') {
			$t3editor->setModeByType($parameters['params']['format']);
		} else {
			$t3editor->setMode(tx_t3editor::MODE_MIXED);
		}
		$config = $GLOBALS['TCA'][$parameters['table']]['columns'][$parameters['field']]['config'];
		$doc = $GLOBALS['SOBE']->doc;
		$attributes = ((((((((((('rows="' . $config['rows']) . '" ') . 'cols="') . $config['cols']) . '" ') . 'wrap="off" ') . 'style="') . $config['wizards']['t3editor']['params']['style']) . '" ') . 'onchange="') . $parameters['fieldChangeFunc']['TBE_EDITOR_fieldChanged']) . '" ';
		$parameters['item'] = '';
		$parameters['item'] .= $t3editor->getCodeEditor($parameters['itemName'], 'fixed-font enable-tab', $parameters['row'][$parameters['field']], $attributes, ($parameters['table'] . ' > ') . $parameters['field'], array(
			'target' => intval($pObj->target)
		));
		$parameters['item'] .= $t3editor->getJavascriptCode($doc);
		return '';
	}

}

?>