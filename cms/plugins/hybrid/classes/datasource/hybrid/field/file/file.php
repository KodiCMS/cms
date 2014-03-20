<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */
class DataSource_Hybrid_Field_File_File extends DataSource_Hybrid_Field {

	/**
	 *
	 * @var array 
	 */
	protected $_props = array(
		'types' => array(),
		'max_size' => 1048576
	);

	/**
	 *
	 * @var string 
	 */
	public $folder = NULL;

	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		parent::__construct($data);

		$this->family = DataSource_Hybrid_Field::FAMILY_FILE;
	}

	/**
	 * 
	 * @return boolean
	 */
	public function create()
	{
		if (parent::create())
		{
			if ($this->create_folder())
			{
				$this->update();
				return $this->id;
			}

			$this->remove_folder();
		}

		return FALSE;
	}

	/**
	 * 
	 * @return boolean
	 */
	public function remove()
	{
		$this->remove_folder();
		return parent::remove();
	}

	/**
	 * 
	 * @return string
	 */
	public function get_type()
	{
		return 'VARCHAR(255)';
	}

	public function set(array $data)
	{
		$data['types'] = !empty($data['types']) ? $data['types'] : array();

		parent::set($data);
	}
	
	/**
	 * 
	 * @param integer $size
	 */
	public function set_max_size( $size )
	{
		if(empty($size))
		{
			$size = Num::bytes('1MiB');
		}
		
		$this->max_size = (int) $size;
	}

	/**
	 * 
	 * @param integer $ds_id
	 */
	public function set_ds($ds_id)
	{
		parent::set_ds($ds_id);

		if ($this->ds_id)
		{
			$this->folder = 'hybrid' . DIRECTORY_SEPARATOR . $this->ds_id . DIRECTORY_SEPARATOR . substr($this->name, 2) . DIRECTORY_SEPARATOR;
		}
		
		return $this;
	}

	/**
	 * 
	 * @param array $types
	 * @return \DataSource_Hybrid_Field_File
	 */
	public function set_types($types)
	{
		$this->types = array();

		if (!is_array($types))
		{
			$types = explode(',', $types);
		}

		foreach ($types as $i => $type)
		{
			$type = trim($type);
			if (
					empty($type) OR
					!preg_match('~^[A-Za-z0-9_\\-]+$~', $type) OR
					!$this->check_disallowed($type)
			)
			{
				unset($types[$i]);
			}
		}

		$this->types = $types;

		return $this;
	}

	/**
	 * 
	 * @param string $file_type
	 * @return boolean
	 */
	protected function check_disallowed($file_type)
	{
		$disallowed = explode(',', '/^php/,/^phtm/,py,pl,/^asp/,htaccess,cgi,_wc,/^shtm/,/^jsp/');
		foreach ($disallowed as $type)
		{
			if (
					(
					(strpos($type, '/') !== FALSE) AND
					preg_match($type, $file_type)
					) OR $type == $file_type
			)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * 
	 * @return boolean
	 */
	public function create_folder()
	{
		if (!empty($this->folder) AND $this->ds_id AND !file_exists(PUBLICPATH . $this->folder))
		{
			if (mkdir(PUBLICPATH . $this->folder, 0777, TRUE))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * 
	 * @return boolean
	 */
	public function remove_folder()
	{
		$folder = $this->folder;
		if (!empty($this->folder) AND is_dir(PUBLICPATH . $this->folder))
		{
			FileSystem::factory(PUBLICPATH . $this->folder)->delete();
			return TRUE;
		}

		return !is_dir(PUBLICPATH . $this->folder);
	}

	/**
	 * 
	 * @param string $path
	 * @return boolean
	 */
	public function is_image($path)
	{
		if (!file_exists($path) OR is_dir($path))
			return FALSE;

		$a = getimagesize($path);
		$image_type = $a[2];

		if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)))
		{
			return TRUE;
		}

		return FALSE;
	}

	protected function _upload_file(array $file)
	{
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$filename = uniqid() . '.' . $ext;
		$filepath = Upload::save($file, $filename, $this->folder());

		return $filepath;
	}

	public function copy_file($filepath)
	{
		$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		$filename = uniqid() . '.' . $ext;
		$new_filepath = $this->folder() . $filename;
		copy($filepath, $new_filepath);

		return $new_filepath;
	}

	/**
	 * 
	 * @param DataSource_Hybrid_Document $doc
	 */
	public function onCreateDocument( DataSource_Hybrid_Document $doc )
	{
		$this->onUpdateDocument( $doc, $doc );
	}

	/**
	 * 
	 * @param DataSource_Hybrid_Document $doc
	 * @return boolean
	 */
	public function onUpdateDocument( DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new )
	{
		$file = $new->get($this->name);

		if (is_array($file))
		{
			$file = $this->_upload_file($file);
		}
		elseif ($file == -1)
		{
			$this->onRemoveDocument( $old );

			$new->set($this->name, '');
			return FALSE;
		}
		elseif ($old !== NULL AND $file == $old->get($this->name))
		{
			return FALSE;
		}

		$filepath = NULL;

		if ( ! empty($file) AND strpos($file, $this->folder()) !== FALSE)
		{
			$filepath = $file;
			$filename = pathinfo($filepath, PATHINFO_BASENAME);
		}

		if (empty($filepath))
		{
			$this->set_old_value($new);
			return FALSE;
		}

		$this->onRemoveDocument($old);

		$new->set($this->name, $this->folder . $filename);

		return TRUE;
	}

	/**
	 * 
	 * @param DataSource_Hybrid_Document $doc
	 */
	public function onRemoveDocument(DataSource_Hybrid_Document $doc)
	{
		$value = $doc->get($this->name);
		
		if ( ! empty($value) )
		{
			@unlink(PUBLICPATH . $value);
			$doc->set($this->name, '') ;
		}
	}

	/**
	 * 
	 * @param Validation $validation
	 * @param DataSource_Hybrid_Document $doc
	 * @return Validation
	 */
	public function document_validation_rules(Validation $validation, DataSource_Hybrid_Document $doc)
	{
		$file = NULL;
		
		$types = $this->types;

		if ($validation->offsetExists($this->name))
		{
			$file = $validation->offsetGet($this->name);
		}

		if ($this->isreq === TRUE AND !empty($file))
		{
			$validation->rules($this->name, array(
				array('Upload::not_empty')
			));
		}

		if (is_array($file))
		{
			$validation
					->rule($this->name, 'Upload::valid')
					->rule($this->name, 'Upload::size', array(':value', $this->max_size));

			if ( ! empty($types) )
			{
				$validation
						->rule($this->name, 'Upload::type', array(':value', $this->types));
			}
		}

		return parent::document_validation_rules($validation, $doc);
	}

	/**
	 * 
	 * @return string
	 */
	public function folder()
	{
		return PUBLICPATH . $this->folder;
	}

	/**
	 * @param Model_Widget_Hybrid
	 * @param array $field
	 * @param array $row
	 * @param string $fid
	 * @return mixed
	 */
	public static function fetch_widget_field($widget, $field, $row, $fid)
	{
		return !empty($row[$fid]) ? str_replace(array('/', '\\'), '/', $row[$fid]) : NULL;
	}

	public function fetch_headline_value($value)
	{
		if ($this->is_image(PUBLICPATH . $value))
		{
			return HTML::anchor(PUBLIC_URL . $value, __('File'), array('class' => 'popup fancybox'));
		}
		else if (!empty($value))
		{
			return HTML::anchor(PUBLIC_URL . $value, __('File'), array('target' => 'blank'));
		}

		return parent::fetch_headline_value($value);
	}

}
