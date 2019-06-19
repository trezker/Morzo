<?php

function Log_message($message)
{
	$bt = debug_backtrace();
	
	$line = $bt[0]['line'];
	$file = $bt[0]['file'];

	$GLOBALS['logger']->Write($message, $file, $line);
}

class Logger {
    private $path = '../logs/';
    private $filename = 'log.log';
    private $fp = null;

    public function Write($message, $file, $line) {
		if (!$this->fp) {
			$this->Open();
        }
		// define script name
		$script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		// define current time
		$time = date('H:i:s');
		// write current time, script name and message to the log file
		// in case of using on Windows, instead of "\n" use "\r\n"
		fwrite($this->fp, "$time $file:$line\n$message\n\n");
    }

    public function Close() {
        fclose($this->fp);
    }

    private function Open() {
		$path = $this->path;
        $file = $this->filename;
        // define the current date (it will be appended to the log file name)
        $today = date('Y-m-d');
        // open log file for writing only; place the file pointer at the end of the file
        // if the file does not exist, attempt to create it
        $this->fp = fopen($path.$today.'_'.$file, 'a') or exit("Can't open $file!");
    }
}

$GLOBALS['logger'] = new Logger;
