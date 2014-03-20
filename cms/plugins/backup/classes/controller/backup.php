<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Backup
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Backup extends Controller_System_Backend {
	
	public function action_index() 
	{		
		$this->template->title = __('Backup');
		
		$this->template->content = View::factory('backup/index', array(
			'files' => Api::get('backup.list')->as_object()->response
		));
	}
	
	public function action_view()
	{
		Assets::package('ace');

		$file = $this->request->param('file');
		$backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . $file);
		$this->template->title = __( 'Backup view :file', array(':file' => $file));
		
		$this->template->content = View::factory('backup/view', array(
			'model' => $backup,
			'filename' => $file
		));
	}
	
	public function action_database()
	{
		$this->auto_render = FALSE;

		$backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . 'db-'.date('YmdHis').'.sql')
			->create()
			->save();
		
		Kohana::$log->add(Log::INFO, ':user create database backup')->write();
		
		Messages::success(__('Database backup created succefully'));
		
		$this->go_back();
	}
	
	public function action_filesystem()
	{
		if($backup = Model_Backup::factory(BACKUP_PLUGIN_FOLDER . 'filesystem-'.date('YmdHis').'.zip')
			->create())
		{
			Kohana::$log->add(Log::INFO, ':user create filesystem backup')->write();
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
		
		Kohana::$log->add(Log::INFO, ':user restore backup')->write();
		Messages::success(__('Backup restored succefully'));
		
		$this->go_back();
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$file = $this->request->param('file');

		if(!file_exists(BACKUP_PLUGIN_FOLDER.$file))
		{
			throw new HTTP_Exception_404('File :file not exist', array(':file' => $file));
		}
		
		unlink(BACKUP_PLUGIN_FOLDER.$file);
		
		Kohana::$log->add(Log::ALERT, ':user delete backup file')->write();
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
		
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);

		# Имя файла
		$filename = 'uploaded-' . date('YmdHis') . '-' . $file['name'];

		Upload::$default_directory = BACKUP_PLUGIN_FOLDER;
			
		# Cохраняем оригинал и продолжаем работать, если ок: 
		if ($file = Upload::save($file, $filename, NULL, 0777))
		{
			Messages::success(__('File :filename uploaded succefully', array(
				':filename' => $filename
			)));
			
			Kohana::$log->add(Log::ALERT, 'Backup file :filename uploaded by :user', array(
				':filename' => $filename
			))->write();

			$this->go_back();
		}
	}
}