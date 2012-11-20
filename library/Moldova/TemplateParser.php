<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 6/29/11
 * Time: 10:28 AM
 */

class Moldova_TemplateParser {
	public $tplsPath;
	public $tplsExt;
	public $tplsMain;
	public $lastEvalCode;
	private $tplsVars = array();
	private $initEvalString = '';

	function __construct($tplsPath = NULL, $tplsExt = NULL) {
		if ($tplsPath) {
			$this->tplsPath = $tplsPath;
		}
		if ($tplsExt) {
			$this->tplsExt = $tplsExt;
		}
	}

	public function assign($tplVarName = '', &$tplVar) {
		if ($tplVarName) {
			$this->tplsVars[$tplVarName] = &$tplVar;
			return true;
		} else {
			return false;
		}
	}

	public function appendJsFile($filename = '') {
		if (!empty($filename)) {
			$this->tplsVars['OPTIONS']['jsArray'][] = $filename;
			return true;
		} else {
			return false;
		}
	}

	public function setBodyTemplate($bodyFileName) {
		if (!empty($bodyFileName)) {
			$bodyFilePath = $this->tplsPath.$bodyFileName.$this->tplsExt;
			$this->assign('bodyTpl', $bodyFilePath);
		} else {
			trigger_error('Empty body template file', E_USER_ERROR);
			exit;
		}
	}

	private function prepareVars() {
		$this->initEvalString = '';
		if (count($this->tplsVars) > 0) {
			foreach ($this->tplsVars as $varName => $varValue) {
				$this->initEvalString .= '$'.$varName.' = &$this->tplsVars[\''.$varName.'\'];'."\n";
			}
		}
		$this->initEvalString .= '?>';
	}

	public function display($tplName = '', $returnContent = false) {
		if (!$this->tplsPath || !$this->tplsExt) {
			trigger_error('Templates path and extension was not defined', E_USER_ERROR);
			exit;
		}
		if (empty($tplName)) {
			if (!$this->tplsMain) {
				trigger_error('Main template file not defined', E_USER_ERROR);
				exit;
			} else {
				$tplName = $this->tplsMain;
			}
		}
		if (!file_exists($this->tplsPath.$tplName.$this->tplsExt)) {
			trigger_error('Template file not found: '.$tplName.$this->tplsExt, E_USER_ERROR);
			exit;
		}
		if ($tplName) {
			$this->prepareVars();
			$this->lastEvalCode = $this->initEvalString.file_get_contents($this->tplsPath.$tplName.$this->tplsExt);
			if ($returnContent) {
				ob_start();
				eval($this->lastEvalCode);
				$content = ob_get_contents();
				ob_end_clean();
				return $content;
			} else {
				eval($this->lastEvalCode);
			}
		}
	}
}
