<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field_File_Image extends DataSource_Hybrid_Field_File_File {
	
	/**
	 *
	 * @var array 
	 */
	protected $_props = array(
		'width' => 100,
		'height' => 100, 
		'crop' => FALSE,
		'master' => Image::AUTO,
		'quality' => 95,
		'types' => 'bmp,gif,jpg,png,tif',
		'max_size' => 1048576
	);

	/**
	 * 
	 * @param array $data
	 * @return DataSource_Hybrid_Field
	 */
	public function set( array $data )
	{		
		$data['crop'] = !empty($data['crop']) ? TRUE : FALSE;
		
		if(empty($data['linked_fields']))
		{
			$data['linked_fields'] = array();
		}
		
		return parent::set( $data );
	}
	
	/**
	 * 
	 * @return \DataSource_Hybrid_Field_File
	 */
	public function set_types($types)
	{
		$types = 'bmp,gif,jpg,png,tif';
		parent::set_types($types);

		return $this;
	}
	
	/**
	 * 
	 * @param integer $width
	 */
	public function set_width( $width )
	{
		$this->width = (int) $width;
	}
	
	/**
	 * 
	 * @param integer $height
	 */
	public function set_height( $height )
	{
		$this->height = (int) $height;
	}
	
	/**
	 * 
	 * @param integer $quality
	 */
	public function set_quality( $quality )
	{
		$this->quality = (int) $quality;
	}
	
	/**
	 * 
	 * @param array $linked_fields
	 */
	public function set_linked_fields( $linked_fields )
	{
		$this->linked_fields = (array) $linked_fields;
	}	
	
	/**
	 * 
	 * @return array
	 */
	public function linked_fields()
	{
		return (array) $this->linked_fields;
	}
	
	/**
	 * @todo Удалить этот метод, т.к. не работает валидация данных
	 * @param array $data
	 * @return DataSource_Hybrid_Field
	 */
	public function set_document_value(array $data, DataSource_Hybrid_Document $document)
	{
		$file = Arr::get($data, $this->name, array());
		
		if(is_array($file) AND Upload::valid($file) AND Upload::not_empty($file))
		{
			$data[$this->name] = $this->_upload_file($file);

			$related_fields = $this->linked_fields();
			

			if( ! empty($related_fields) )
			{
				foreach($related_fields as $id)
				{
					$related_field = DataSource_Hybrid_Field_Factory::get_field($id);
					if($related_field === NULL) continue;
					
					$doc->fields[$related_field->name] = $related_field->copy_file($data[$this->name]);
				}
			}
		}
		else if(Valid::url( Arr::get($data, $this->name . '_url') )  )
		{
			$data[$this->name] = $data[$this->name . '_url'];
		}
		
		return parent::set_document_value($data, $document);
	}
	
	/**
	 * 
	 * @param DataSource_Hybrid_Document $old
	 * @param DataSource_Hybrid_Document $new
	 * @return boolean
	 */
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new)
	{
		$new_file = $new->get($this->name);
		
		if(empty($new_file)) 
		{
			$this->set_old_value($new);
			return FALSE;
		}
		elseif( $new_file == -1)
		{
			$this->onRemoveDocument($old);
			
			$new->set($this->name, '');
			return FALSE;
		}
		elseif($old !== NULL AND $new_file == $old->get($this->name))
		{
			return FALSE;
		}

		$filepath = NULL;
		
		if( ! empty($new_file) AND strpos($new_file, $this->folder()) !== FALSE)
		{
			$filepath = $new_file;
			$filename = pathinfo($filepath, PATHINFO_BASENAME);
		}
		if( is_string($new_file) AND Valid::url($new_file) )
		{
			list($status, $filename) = Upload::from_url( $new_file, $this->types, $this->folder());

			if($status)
			{
				if(rename(TMPPATH . $filename, $this->folder() . $filename))
				{
					$filepath = $this->folder() . $filename;
				}
				else
				{
					unlink(TMPPATH . $filename);
					return FALSE;
				}
			}
		}

		if( empty($filepath) ) 
		{
			$this->set_old_value($new);
			return FALSE;
		}

		$this->onRemoveDocument($old);
		
		if($this->is_image( $filepath ))
		{
			$image = Image::factory( $filepath );
			
			if(!empty($this->width) OR !empty($this->height))
			{
				$image->resize( $this->width, $this->height, $this->master );

				if(!empty($this->crop ) AND $this->width > 0 AND $this->height > 0)
				{
					$image->crop( $this->width, $this->height );
				}
			}

			$image->save(NULL, $this->quality);
		}
		
		$new->set($this->name, $this->folder . $filename);

		return TRUE;
	}
	
	/**
	 * 
	 * @param Validation $validation
	 * @param DataSource_Hybrid_Document $doc
	 * @return Validation
	 */
	public function document_validation_rules( Validation $validation, DataSource_Hybrid_Document $doc )
	{
		$image_url = NULL;
		$image = NULL;
		
		if($validation->offsetExists($this->name))
		{
			$image = $validation->offsetGet($this->name );
		}
		
		if($validation->offsetExists($this->name . '_url'))
		{
			$image_url = $validation->offsetGet($this->name . '_url');
		}
		
		if($this->isreq === TRUE AND ! empty($image))
		{
			if(is_array($image))
			{
				$validation->rules( $this->name, array(
					array('Upload::not_empty')
				) );
			}
			else
			{
				$validation->rules( $this->name, array(
					array('Valid::not_empty')
				) );
			}
			
		}
		elseif($this->isreq === TRUE AND !empty($image_url))
		{
			$validation->rules( $this->name . '_url', array(
				array('Valid::not_empty')
			) );
		}

		if(empty($image_url) AND is_array( $image ))
		{
			$validation->rules( $this->name, array(
				array('Upload::valid'),
				array('Upload::type', array(':value', $this->types)),
				array('Upload::size', array(':value', $this->max_size))
			) );
		}
		else
		{
			$ext = strtolower( pathinfo( $image_url, PATHINFO_EXTENSION ));
			
			$validation->rules( $this->name . '_url', array(
				array('Valid::url'),
				array('in_array', array($ext, $this->types))
			) );
		}

		return $validation
				->label($this->name . '_url', $this->header)
				->label($this->name, $this->header);
	}

	public function get_similar_fields()
	{
		$fields = DataSource_Hybrid_Field_Factory::get_section_fields($this->ds_id, array('file_image'));

		unset($fields[$this->id]);
		$options = array();

		foreach ($fields as $field)
		{
			$options[$field->id] = $field->name;
		}

		return $options;
	}
}