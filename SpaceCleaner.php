<?php
/**
* 
*/
class SpaceCleaner
{
	const LOG_FILE = "./log.txt";

	private $nodes = NULL;
	private $log = NULL;
	public $minimumFreeSpace = NULL;

	function __construct($minimumFreeSpace)
	{
		$this->minimumFreeSpace = $minimumFreeSpace;
	}

	public function loadNodes(array $nodes)
	{
		$this->nodes = $nodes;
		return $this;
	}

	public function start()
	{
		$freeSpace = $this->getFreeSpace();
		if ($freeSpace >= $this->minimumFreeSpace) {
			// Очистка не требуется
			return NULL;
		}

		$this->message("Свободного места на сервере: ".round($freeSpace/1024/1024/1024)." GB");
		$this->message("Начинаем очистку");

		ksort($this->nodes);

		foreach ($this->nodes as $priority => $config) {
			if ($this->getFreeSpace() >= $this->minimumFreeSpace) break;

			$this->message("Подготавливаем к очистке приоритет № {$priority}");	
			$node  = new Node($config);
			$node->clearing();
		}

		$this->message("Свободного места на сервере: ".round($this->getFreeSpace()/1024/1024/1024)." GB");

		$this->_clear();
		$this->message("Закончили");
	}

	public function getFreeSpace()
	{
		return disk_free_space("/");
	}
	
	public function message($text, $status="INFO")
	{
		echo "\033[1;34m". date("Y-m-d H:i:s")." - ".($status == 'ERROR'?"\033[0;31m":"\033[0;32m").$status.': '."\033[0;36m".$text."\033[0m".PHP_EOL;

		if (defined('LOG_TYPE') && trim(strtoupper(LOG_TYPE)) !== 'ALL' && trim(strtoupper(LOG_TYPE)) === strtoupper($status)) {
			$this->log(date("Y-m-d H:i:s") . " - " . $status . ': ' . $text . PHP_EOL);
		}else{
			$this->log(date("Y-m-d H:i:s") . " - " . $status . ': ' . $text . PHP_EOL);
		}
	}

	private function log($text)
	{
		if (is_null($this->log))
			$this->log = fopen(self::LOG_FILE, "a+");	
		
		fwrite($this->log, $text);
	}

	protected function _clear()
	{
		if (!is_null($this->log)) {
			fclose($this->log);
			$this->log = NULL;
		}
	}
}