<?php
/**
* @file		backup.php
*
* @brief 	Performs backup to local / remotebox
*
* Use the following crontab entry to execute:
* 01  03 *  *  * root    /usr/bin/php/ /pathto/backup.php >> /tmp/backup.log
*
* @version	ver-2.0 (Re-implemented using OOP/class)
*
* @package	Scripts
* @author  	Salil Kothadia <salil.kothadia@ymail.com>
* @license 	GNU/GPL
*/

# output buffering
ob_start();
echo "===============================================================\n";

# FILES / DIR TO BACKUP
$dirArray['crontab']			= '/etc/crontab';
$dirArray['apache_conf'] 		= '/etc/httpd/conf/httpd.conf';
$dirArray['virtualhost_conf']	= '/mnt/data-store/apache.d/';
$dirArray['php_ini'] 			= '/etc/php.ini';
$dirArray['mysql_conf'] 		= '/etc/my.cnf';

# DATABASES TO BACKUP
$databaseArray					= array('compassites');

# Backup class instance
$instance = new BackUp('/mnt/data-store/backup/');

# Trigger Backup
$instance->initProcess($dirArray, $databaseArray);


# SUPPORTING CLASS
class BackUp
{
	protected $_execArray	= array();
	protected $_localBackupPath= '';

	private $_lockFile 		= '/tmp/backup.lock';
    private $_isRunning		= false;

	/**
	*	@brief	Constructs the backup process
	*
	*	@param 	string	The name of the DB as defined in Config/Data.php
	*	@return	array  	an array of DB info
	*/
	public function __construct($localBackupPath='/mnt/data-store/backup/')
	{
		# Check if process is already running
		if( file_exists($this->_lockFile) )
		{
			$this->_isRunning = true;
		}
		else
        {
			$file = fopen($this->_lockFile, 'w');
			fclose($file);
        }

		# Local backup path
		$this->_localBackupPath = $localBackupPath . date('Y-m-d') . '/';
		$this->_databaseDumpPath= '/tmp/mysqlDump/';

		# check for if directory exsist else create them
		$this->checkDirectory(array($this->_localBackupPath, $this->_databaseDumpPath));

		/*
		// Remote paths
		$remoteServer	= 'xyz.in';
		$remotePath		= "/home/{$data['Name']}";
		*/
	}

	/**
	*	@brief	Starts the backup process
	*
	*	@param 	array	Array of shell commands to be executed
	*	@return	void
	*/
	public function initProcess(array $dirs, array $databases )
	{
		# build tar commands
		$this->tar($dirs);

		# build sql dump commands
		$this->databaseDump($databases);

		# Loop through list of commands and execute each one
		foreach ($this->_execArray as $name => $command)
		{
			echo date('Y-m-d H:i:s') . " - Executing: $name :: \n$command\n\n";
			passthru($command);

			# flush output
			ob_flush();
			flush();
		}
	}

	# Copy or Tar
	protected function tar($dirArray)
	{
		foreach($dirArray as $key => $file)
		{
			if( is_dir($file) )
			{
				$this->_execArray[$key] = "/bin/tar cjvf {$this->_localBackupPath}{$key}.tar.bz2 {$file}";
			}
			elseif( is_file($file) )
			{
			    $this->_execArray[$key] = "/bin/cp {$file} {$this->_localBackupPath}";
			}
		}
	}

	# MySQL dump
	protected function databaseDump($databases)
	{
		foreach($databases as $database)
		{
			$this->_execArray['mysqldump_'.$database] = "mysqldump -u export -pexportsa {$database} > {$this->_databaseDumpPath}{$database}.sql";
		}
		$this->_execArray['mysqldump_tar']	= "/bin/tar cjvf {$this->_localBackupPath}database.tar.bz2 {$this->_databaseDumpPath}";
		$this->_execArray['mysqldump_rmdir']= "/bin/rm -rf {$this->_databaseDumpPath}";
	}

	# Checks for dirs
	protected function checkDirectory(array $dirArray)
	{
		foreach($dirArray as $dir)
		{
			if( !empty($dir) )
			{
				if (!file_exists($dir))
				{
					mkdir($dir, 0775, true);
				}
			}
		}
	}

    protected function isRunning()
    {
    	return $this->_isRunning;
    }

    public function __destruct()
    {
    	if( file_exists($this->_lockFile) && !$this->_isRunning )
    	{
    		unlink($this->_lockFile);
    	}
    }
};