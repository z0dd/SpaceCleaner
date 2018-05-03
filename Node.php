<?php
/**
* 
*/
class Node extends SpaceCleaner
{
	private $priority	= NULL;
	private $path		= NULL;
	private $mask		= NULL; 
	private $date		= NULL;
	private $recurcive	= NULL;
	private $comment	= NULL;
	
	function __construct(array $config)
	{
		if (isset($config['path']) || !file_exists($config['path'])) 
			throw new Exception("Не верно указана переменная 'path'", 1);

		$this->path = $config['path'];

		if (!isset($config['mask']) || (!is_null($config['mask']) && preg_match($config['mask'], NULL) === FALSE) )
			throw new Exception("Не верно указана переменная 'mask'", 1);

		$this->mask = $config['mask'];

		if (!isset($config['date']) || (!is_null($config['date']) && strtotime($config['date']) === FALSE) )
			throw new Exception("Не верно указана переменная 'date'", 1);

		$this->date = $config['date'];

		$this->recurcive = isset($config['recurcive']) && $config['recurcive'] === TRUE;

		$this->comment = isset($config['comment']) ? $config['comment'] : "";
	}

	public function setPriority(integer $priority)
	{
		$this->priority = $priority;
	}

	public function clearing()
	{
		if ($handle = opendir($this->path)) {

		    while (($entry = readdir($handle)) !== FALSE) {
		        if ($entry == "." || $entry == "..") continue;

				$filedate = date("Y-m-d H:i:s", filemtime($this->path.$entry));

		        if (!is_null($this->date) && $filedate > $this->date) 
		        	continue;

		        if (!is_null($this->mask) && !preg_match($this->mask, $entry)) 
					continue;

				if (!isProdaction()) {
					parent::message(ENVIROMENT. " DELETED: {$entry}");
				}else{
					if (unlink($this->path.$entry)) {
						parent::message("DELETED: {$entry}");
					}else{
						parent::message("NOT DELETED: {$entry}", "ERROR");
					}
				}
		    }
		    closedir($handle);
		}
	}
}