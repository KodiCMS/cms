<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Backup extends Controller_System_Plugin {
	
	public function action_index() 
	{
		$files = array();

		$handle = opendir(BACKUP_PLUGIN_FOLDER);
		while (false !== ($file = readdir($handle))) 
		{
			if (!preg_match("/(sql|zip)/", $file, $m)) 
			{
				continue;
			}
			
			$date_create = preg_replace('![^\d]*!', '', $file);
			$date_create = preg_replace('#^([\d]{4})([\d]{2})([\d]{2})([\d]{2})([\d]{2})([\d]{2})$#', '$3/$2/$1 $4:$5:$6', $date_create);

			$files[$file] = array(
				'size' => Text::bytes(filesize(BACKUP_PLUGIN_FOLDER.$file)),
				'path' => BACKUP_PLUGIN_FOLDER.$file,
				'date' => $date_create
			);
		}

		closedir($handle);
		
		$this->template->content = View::factory('backup/index', array(
			'files' => $files
		));
	}
	
	public function action_view()
	{
		$file = $this->request->param('file');
		$backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . $file)
			->view();
		
		$this->template->content = View::factory('backup/view', array(
			'file' => $backup,
			'filename' => $file
		));
	}
	
	public function action_database()
	{
		$this->auto_render = FALSE;

		$backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . 'db-'.date('YmdHis').'.sql')
			->create()
			->save();
		
		Messages::success(__('Database backup created succefully'));
		
		$this->go_back();
	}
	
	public function action_filesystem()
	{
		if($backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . 'filesystem-'.date('YmdHis').'.zip')
			->create())
		{
			Messages::success(__('Filesystem backup created succefully'));
		}
		
		$this->go_back();
	}

	public function action_restore()
	{
		$this->auto_render = FALSE;

		$file = $this->request->param('file');

		$backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . $file)
			->restore();
		
		Messages::success(__('Backup restored succefully'));
		
		$this->go_back();
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$file = $this->request->param('file');

		if(!file_exists(BACKUP_PLUGIN_FOLDER.$file))
		{
			throw new  Kohana_Exception('File :file not exist', array(':file' => $file));
		}
		
		unlink(BACKUP_PLUGIN_FOLDER.$file);
		Messages::success(__('File :filename deleted succefully', array(
			':filename' => $file
		)));
		$this->go_back();
	}
	
	public function action_upload()
	{
		$this->auto_render = FALSE;

		$errors = array();

		# Проверяем файл
		if(!isset($_FILES['file']))
		{
			$this->go_back();
		}
		
		$file = $_FILES['file'];
		
		if (!is_dir(BACKUP_PLUGIN_FOLDER))
		{
			$errors[] = __('Folder (:folder) not exist!', array(
				':folder' => BACKUP_PLUGIN_FOLDER
			));
		}
		
		if(!is_writable(BACKUP_PLUGIN_FOLDER))
		{
			$errors[] = __('Folder (:folder) must be writable!', array(
				':folder' => BACKUP_PLUGIN_FOLDER
			));
		}

		# Проверяем на пустоту
		if(!Upload::not_empty($file))
		{
			$errors[] = __('File is not attached!');
		}

		# Проверяем на расширение
		if(!Upload::type($file, array('sql', 'zip')))
		{
			$errors[] = __('Bad format of file!');
		}
		
		if(!empty($errors))
		{
			Messages::errors($errors);
			$this->go_back();
		}

		# Имя файла
		$filename = 'uploaded-db-'.date('YmdHis').'.sql';

		Upload::$default_directory = BACKUP_PLUGIN_FOLDER;
			
		# Cохраняем оригинал и продолжаем работать, если ок: 
		if ($file = Upload::save($file, $filename, NULL, 0777))
		{
			Messages::success(__('File :filename uploaded succefully', array(
				':filename' => $filename
			)));

			$this->go_back();
		}
	}
}