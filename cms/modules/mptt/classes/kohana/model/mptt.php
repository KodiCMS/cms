<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Modified Preorder Tree Traversal Class.
 *
 * @author     Kiall Mac Innes
 * @author     Mathew Davies
 * @author     Mike Parkin
 * @copyright  (c) 2008-2010
 * @package    Model_MPTT
 */
abstract class Kohana_Model_MPTT extends ORM
{
	/**
	 * @access public
	 * @var string left column name.
	 */
	public $left_column = 'lft';

	/**
	 * @access public
	 * @var string right column name.
	 */
	public $right_column = 'rgt';

	/**
	 * @access public
	 * @var string level column name.
	 */
	public $level_column = 'lvl';

	/**
	 * @access public
	 * @var string scope column name.
	 **/
	public $scope_column = 'scope';
	
	/**
	 * @access public
	 * @var string parent column name.
	 **/
	public $parent_column = 'pid';


	/**
	 * Enable/Disable path calculation
	 *
	 */
	protected $path_calculation_enabled = FALSE;

	/**
	 * Full pre-calculated path
	 *
	 */
	public $path_column = 'path';

	/**
	 * Single path element
	 */
	public $path_part_column = 'path_part';

	/**
	 * Path separator
	 */
	public $path_separator = '/';

	/**
	* Returns a full hierarchical tree, with or without scope checking.
	* 
	* @param   boolean  $scope  only retrieve nodes with specified scope [Optional]
	* @return  object
	*/
	public function full_tree($scope = NULL)
	{
		 $result = Model_MPTT::factory($this->_object_name);

		 if ( $scope !== NULL )
		 {
			 $result->where($this->scope_column, '=', $scope);
		 }
		 else
		 {
			 $result->order_by($this->scope_column, 'ASC')->order_by($this->left_column, 'ASC');
		 }

		 return $result->find_all();
	}

	public function html_select()
	{
		
	}


	/**
	 * New scope
	 * This also double as a new_root method allowing
	 * us to store multiple trees in the same table.
	 *
	 * @param integer $scope New scope to create.
	 * @return boolean
	 **/
	public function new_scope($scope = NULL, array $additional_fields = array())
	{
		if($scope === NULL)
		{
			$last_scope = (int) DB::select(array(DB::expr('MAX('.$this->scope_column.')'), 'max'))
				->from($this->_table_name)
				->execute()
				->get('max');

			$scope = $last_scope + 1;
		}

		// Make sure the specified scope doesn't already exist.
		$search = Model_MPTT::factory($this->_object_name)->where($this->scope_column, '=', $scope)->find_all();

		if ($search->count() > 0 )
			return FALSE;

		// Create a new root node in the new scope.
		$this->{$this->left_column} = 1;
		$this->{$this->right_column} = 2;
		$this->{$this->level_column} = 0;
		$this->{$this->parent_column} = 0;
		$this->{$this->scope_column} = $scope;

		// Other fields may be required.
		if ( ! empty($additional_fields))
		{
			foreach ($additional_fields as $column => $value)
			{
				$this->{$column} = $value;
			}
		}

		parent::save();

		return $this;
	}

	/**
	 * Locks table.
	 *
	 * @access private
	 */
	protected function lock()
	{
		$lock = $this->_db->query(Database::SELECT, 'SELECT GET_LOCK("' . Kohana::$environment . '-' . $this->_table_name . '", 30) AS l', TRUE)->get('l');

		if ($lock == 0)
		{
			return $this->lock(); // Unable to obtain lock, retry.
		}
		else if ($lock == 1)
		{
			return $this; // Success
		}
		else
			throw new Exception('Unable to obtain MPTT lock'); // Unknown Error handle this.. better
	}

	/**
	 * Unlock table.
	 *
	 * @access private
	 */
	protected function unlock()
	{
		$this->_db->query(Database::SELECT, 'SELECT RELEASE_LOCK("' . Kohana::$environment . '-' . $this->_table_name . '") AS l', TRUE);

		return $this;
	}

	/**
	 * Does the current node have children?
	 *
	 * @access public
	 * @return bool
	 */
	public function has_children()
	{
		return (($this->{$this->right_column} - $this->{$this->left_column}) > 1);
	}

	/**
	 * Is the current node a leaf node?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_leaf()
	{
		return ! $this->has_children();
	}

	/**
	 * Is the current node a descendant of the supplied node.
	 *
	 * @access public
	 * @param Model_MPTT $target Target
	 * @return bool
	 */
	public function is_descendant($target)
	{
		return ($this->{$this->left_column} > $target->{$this->left_column} AND $this->{$this->right_column} < $target->{$this->right_column} AND $this->{$this->scope_column} = $target->{$this->scope_column});
	}

	/**
	 * Is the current node a direct child of the supplied node?
	 *
	 * @access public
	 * @param Model_MPTT $target Target
	 * @return bool
	 */
	public function is_child($target)
	{
		return ($this->parent->{$this->_primary_key} === $target->{$this->_primary_key});
	}

	/**
	 * Is the current node the direct parent of the supplied node?
	 *
	 * @access public
	 * @param Model_MPTT $target Target
	 * @return bool
	 */
	public function is_parent($target)
	{
		return ($this->{$this->_primary_key} === $target->parent->{$this->_primary_key});
	}

	/**
	 * Is the current node a sibling of the supplied node
	 *
	 * @access public
	 * @param Model_MPTT $target Target
	 * @return bool
	 */
	public function is_sibling($target)
	{
		if ($this->{$this->_primary_key} === $target->{$this->_primary_key})
			return FALSE;

		return ($this->parent->{$this->_primary_key} === $target->parent->{$this->_primary_key});
	}

	/**
	 * Is the current node a root node?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_root()
	{
		return ((int) $this->{$this->left_column} === 1);
	}

	/**
	 * Returns the root node.
	 *
	 * @access protected
	 * @return Model_MPTT
	 */
	public function root($scope = NULL)
	{
		if ($scope === NULL && $this->loaded())
		{
			$scope = $this->{$this->scope_column};
		}
		elseif ($scope === NULL && ! $this->loaded())
		{
			return FALSE;
		}

		return Model_MPTT::factory($this->_object_name)->where($this->left_column, '=', 1)->where($this->scope_column, '=', $scope);
	}

	/**
	 * Returns the parent of the current node.
	 *
	 * @access public
	 * @return Model_MPTT
	 */
	public function parent()
	{
		return $this->parents()->where($this->level_column, '=', $this->{$this->level_column} - 1);
	}

	/**
	 * Returns the parents of the current node.
	 *
	 * @access public
	 * @param bool $root include the root node?
	 * @param string $direction direction to order the left column by.
	 * @return Model_MPTT
	 */
	public function parents($root = TRUE, $direction = 'ASC')
	{
		$parents =  Model_MPTT::factory($this->_object_name)
			->where($this->left_column, '<=', $this->{$this->left_column})
			->where($this->right_column, '>=', $this->{$this->right_column})
			->where($this->_primary_key, '<>', $this->{$this->_primary_key})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->order_by($this->left_column, $direction);

		if ( ! $root)
		{
			$parents->where($this->left_column, '!=', 1);
		}

		return $parents;
	}

	/**
	 * Returns the children of the current node.
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return Model_MPTT
	 */
	public function children($self = FALSE, $direction = 'ASC')
	{
		if ($self)
		{
			return $this->descendants($self, $direction)->where($this->level_column, '<=', $this->{$this->level_column} + 1)->where($this->level_column, '>=', $this->{$this->level_column});
		}

		return $this->descendants($self, $direction)->where($this->level_column, '=', $this->{$this->level_column} + 1);
	}

	/**
	 * Returns the descendants of the current node.
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return Model_MPTT
	 */
	public function descendants($self = FALSE, $direction = 'ASC')
	{
		$left_operator = $self ? '>=' : '>';
		$right_operator = $self ? '<=' : '<';

		return Model_MPTT::factory($this->_object_name)
			->where($this->left_column, $left_operator, $this->{$this->left_column})
			->where($this->right_column, $right_operator, $this->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->order_by($this->left_column, $direction);
	}

	/**
	 * Returns the siblings of the current node
	 *
	 * @access public
	 * @param bool $self include the current loaded node?
	 * @param string $direction direction to order the left column by.
	 * @return Model_MPTT
	 */
	public function siblings($self = FALSE, $direction = 'ASC')
	{
		$siblings = Model_MPTT::factory($this->_object_name)
			->where($this->left_column, '>', $this->parent->find()->{$this->left_column})
			->where($this->right_column, '<', $this->parent->find()->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->where($this->level_column, '=', $this->{$this->level_column})
			->order_by($this->left_column, $direction);

		if ( ! $self)
		{
			$siblings->where($this->_primary_key, '<>', $this->{$this->_primary_key});
		}

		return $siblings;
	}

	/**
	 * Returns leaves under the current node.
	 *
	 * @access public
	 * @return Model_MPTT
	 */
	public function leaves()
	{
		return Model_MPTT::factory($this->_object_name)
			->where($this->left_column, '=', new Database_Expression('(`'.$this->right_column.'` - 1)'))
			->where($this->left_column, '>=', $this->{$this->left_column})
			->where($this->right_column, '<=', $this->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->order_by($this->left_column, 'ASC');
	}

	/**
	 * Get Size
	 *
	 * @access protected
	 * @return integer
	 */
	protected function get_size()
	{
		return ($this->{$this->right_column} - $this->{$this->left_column}) + 1;
	}

	/**
	 * Create a gap in the tree to make room for a new node
	 *
	 * @access private
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function create_space($start, $size = 2)
	{
		// Update the right values, then the left.
		$this->_db->query(Database::UPDATE, 'UPDATE '.$this->_table_name.' SET `'.$this->right_column.'` = `'.$this->right_column.'` + '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column}, FALSE);
		$this->_db->query(Database::UPDATE, 'UPDATE '.$this->_table_name.' SET `'.$this->left_column.'` = `'.$this->left_column.'` + '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column}, FALSE);
	}

	/**
	 * Closes a gap in a tree. Mainly used after a node has
	 * been removed.
	 *
	 * @access private
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function delete_space($start, $size = 2)
	{
		// Update the left values, then the right.
		$this->_db->query(Database::UPDATE, 'UPDATE '.$this->_table_name.' SET `'.$this->left_column.'` = `'.$this->left_column.'` - '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column}, FALSE);
		$this->_db->query(Database::UPDATE, 'UPDATE '.$this->_table_name.' SET `'.$this->right_column.'` = `'.$this->right_column.'` - '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$this->{$this->scope_column}, FALSE);
	}

	/**
	 * Insert a node
	 *
	 * @access private
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	protected function insert($target, $copy_left_from, $left_offset, $level_offset)
	{
		// Insert should only work on new nodes.. if its already it the tree it needs to be moved!
		if ($this->loaded())
			return FALSE;

		$this->lock();

		if ( ! $target instanceof $this)
		{
			$target = Model_MPTT::factory($this->_object_name, $target);
		}
		else
		{
			$target->reload(); // Ensure we're using the latest version of $target
		}

		$this->{$this->left_column}  = $target->{$copy_left_from} + $left_offset;
		$this->{$this->right_column} = $this->{$this->left_column} + 1;
		$this->{$this->level_column} = $target->{$this->level_column} + $level_offset;
		$this->{$this->scope_column} = $target->{$this->scope_column};
		$this->{$this->parent_column} = $target->{$this->_primary_key};

		$this->create_space($this->{$this->left_column});

		parent::save();

		if ($this->path_calculation_enabled)
		{
			$this->update_path();
			parent::save();
		}

		$this->unlock();

		return $this;
	}

	/**
	 * Inserts a new node to the left of the target node.
	 *
	 * @access public
	 * @param Model_MPTT $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function insert_as_first_child($target)
	{
		return $this->insert($target, $this->left_column, 1, 1);
	}

	/**
	 * Inserts a new node to the right of the target node.
	 *
	 * @access public
	 * @param Model_MPTT $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function insert_as_last_child($target)
	{
		return $this->insert($target, $this->right_column, 0, 1);
	}

	/**
	 * Inserts a new node as a previous sibling of the target node.
	 *
	 * @access public
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function insert_as_prev_sibling($target)
	{
		return $this->insert($target, $this->left_column, 0, 0);
	}

	/**
	 * Inserts a new node as the next sibling of the target node.
	 *
	 * @access public
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function insert_as_next_sibling($target)
	{
		return $this->insert($target, $this->right_column, 1, 0);
	}

	/**
	 * Overloaded save method
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function save(Validation $validation = NULL)
	{
		if ($this->loaded() === TRUE)
		{
			return parent::save($validation);
		}

		return FALSE;
	}

	/**
	 * Removes a node and it's descendants.
	 *
	 * $usless_param prevents a strict error that breaks PHPUnit like hell!
	 * @access public
	 * @param bool $descendants remove the descendants?
	 */
	public function delete($usless_param = NULL)
	{
		$this->lock()->reload();

		$result = DB::delete($this->_table_name)
			->where($this->left_column, '>=', $this->{$this->left_column})
			->where($this->right_column, '<=', $this->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->execute($this->_db);

		if ($result > 0)
		{
			$this->delete_space($this->{$this->left_column}, $this->get_size());
		}

		$this->unlock();
	}

	/**
	 * Move to First Child
	 *
	 * Moves the current node to the first child of the target node.
	 *
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function move_to_first_child($target)
	{
		return $this->move($target, TRUE, 1, 1, TRUE);
	}

	/**
	 * Move to Last Child
	 *
	 * Moves the current node to the last child of the target node.
	 *
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function move_to_last_child($target)
	{
		return $this->move($target, FALSE, 0, 1, TRUE);
	}

	/**
	 * Move to Previous Sibling.
	 *
	 * Moves the current node to the previous sibling of the target node.
	 *
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function move_to_prev_sibling($target)
	{
		return $this->move($target, TRUE, 0, 0, FALSE);
	}

	/**
	 * Move to Next Sibling.
	 *
	 * Moves the current node to the next sibling of the target node.
	 *
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @return Model_MPTT
	 */
	public function move_to_next_sibling($target)
	{
		return $this->move($target, FALSE, 1, 0, FALSE);
	}

	/**
	 * Move
	 *
	 * @param Model_MPTT|integer $target target node id or Model_MPTT object.
	 * @param bool $left_column use the left column or right column from target
	 * @param integer $left_offset left value for the new node position.
	 * @param integer $level_offset level
	 * @param bool allow this movement to be allowed on the root node
	 */
	protected function move($target, $left_column, $left_offset, $level_offset, $allow_root_target)
	{
		if (!$this->loaded())
			return FALSE;

		// Make sure we have the most upto date version of this AFTER we lock
		$this->lock()->reload();

		if ( ! $target instanceof $this)
		{
			$target = Model_MPTT::factory($this->_object_name, $target);

			if ( ! $target->loaded())
			{
				$this->unlock();
				return FALSE;
			}
		}
		else
		{
			$target->reload();
		}

		// Stop $this being moved into a descendant or disallow if target is root
		if ($target->is_descendant($this) OR ($allow_root_target === FALSE AND $target->is_root()))
		{
			$this->unlock();
			return FALSE;
		}
		
		$parent_id = $level_offset > 0 ? $target->{$this->_primary_key} : $target->{$this->parent_column};

		$left_offset = ($left_column === TRUE ? $target->{$this->left_column} : $target->{$this->right_column}) + $left_offset;
		$level_offset = $target->{$this->level_column} - $this->{$this->level_column} + $level_offset;

		$size = $this->get_size();

		$this->create_space($left_offset, $size);

		// if node is moved to a position in the tree "above" its current placement
		// then its lft/rgt may have been altered by create_space
		$this->reload();

		$offset = ($left_offset - $this->{$this->left_column});

		$update = DB::update($this->_table_name)
			->set(array(
				$this->left_column => DB::expr($this->left_column . ' + ' . $offset), 
				$this->right_column => DB::expr($this->right_column . ' + ' . $offset), 
				$this->level_column => DB::expr($this->level_column.' + '.$level_offset),
				$this->scope_column => DB::expr($target->{$this->scope_column}),
				$this->parent_column => $parent_id
			))
			->where($this->left_column, '>=', $this->{$this->left_column})
			->where($this->right_column, '<=', $this->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column});
			
		echo $update;
			
			
		$update->execute();

		$this->delete_space($this->{$this->left_column}, $size);


		if ($this->path_calculation_enabled)
		{
			$this->update_path();
			parent::save();
		}

		$this->unlock();

		return $this;
	}

	/**
	 *
	 * @access public
	 * @param $column - Which field to get.
	 * @return mixed
	 */
	public function __get($column)
	{
		switch ($column)
		{
			case 'parent':
				return $this->parent();
			case 'parents':
				return $this->parents();
			case 'children':
				return $this->children();
			case 'siblings':
				return $this->siblings();
			case 'root':
				return $this->root();
			case 'leaves':
				return $this->leaves();
			case 'descendants':
				return $this->descendants();
			default:
				return parent::__get($column);
		}
	}

	/**
	 * Verify the tree is in good order
	 *
	 * This functions speed is irrelevant - its really only for debugging and unit tests
	 *
	 * @todo Look for any nodes no longer contained by the root node.
	 * @todo Ensure every node has a path to the root via ->parents();
	 * @access public
	 * @return boolean
	 */
	public function verify_tree()
	{
		foreach ($this->get_scopes() as $scope)
		{
			if ( ! $this->verify_scope($scope->{$this->scope_column}))
				return FALSE;
		}
		return TRUE;
	}

	private function get_scopes()
	{
		// TODO... redo this so its proper :P and open it public
		// used by verify_tree()
		return $this->_db->query(Database::SELECT, 'SELECT DISTINCT(`'.$this->scope_column.'`) from `'.$this->_table_name.'`', TRUE);
	}


	public function verify_scope($scope)
	{
		$root = $this->root($scope);

		$end = $root->{$this->right_column};

		// Find nodes that have slipped out of bounds.
		$result = $this->_db->query(Database::SELECT, 'SELECT count(*) as count FROM `'.$this->_table_name.'` WHERE `'.$this->scope_column.'` = '.$root->{$this->scope_column}.' AND (`'.$this->left_column.'` > '.$end.' OR `'.$this->right_column.'` > '.$end.')', FALSE);
		if ($result[0]->count > 0)
			return FALSE;

		// Find nodes that have the same left and right value
		$result = $this->_db->query(Database::SELECT, 'SELECT count(*) as count FROM `'.$this->_table_name.'` WHERE `'.$this->scope_column.'` = '.$root->{$this->scope_column}.' AND `'.$this->left_column.'` = `'.$this->right_column.'`', FALSE);
		if ($result[0]->count > 0)
			return FALSE;

		// Find nodes that right value is less than the left value
		$result = $this->_db->query(Database::SELECT, 'SELECT count(*) as count FROM `'.$this->_table_name.'` WHERE `'.$this->scope_column.'` = '.$root->{$this->scope_column}.' AND `'.$this->left_column.'` > `'.$this->right_column.'`', FALSE);
		if ($result[0]->count > 0)
			return FALSE;

		// Make sure no 2 nodes share a left/right value
		$i = 1;
		while ($i <= $end)
		{
			$result = $this->_db->query(Database::SELECT, 'SELECT count(*) as count FROM `'.$this->_table_name.'` WHERE `'.$this->scope_column.'` = '.$root->{$this->scope_column}.' AND (`'.$this->left_column.'` = '.$i.' OR `'.$this->right_column.'` = '.$i.')', FALSE);

			if ($result[0]->count > 1)
				return FALSE;

			$i++;
		}

		// Check to ensure that all nodes have a "correct" level
		//TODO

		return TRUE;
	}

	public function update_path()
	{
		$path = "";

		$parents = $this->parents(FALSE)
			->find_all();

		foreach ($parents as $parent)
		{
			$path .= $this->path_separator . trim($parent->{$this->path_part_column});
		}

		$path .= $this->path_separator . trim($this->{$this->path_part_column});

		$path = trim($path, $this->path_separator);

		$this->{$this->path_column} = $path;

		return $this;
	}
}
