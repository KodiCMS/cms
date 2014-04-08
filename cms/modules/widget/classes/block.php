<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @author		ButscHSter
 */
class Block {

	/**
	 * Проверка блока на наличие в нем виджетов
	 * 
	 * @param type string|array
	 */
	public static function not_empty( $name )
	{
		if( ! is_array($name) )
		{
			$name = array($name);
		}
		
		$blocks = Context::instance()->get_blocks();

		foreach($name as $block)
		{
			if(in_array($block, $blocks)) return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Метод служит для разметки выводимых блоков на странице
	 * 
	 * @param string $name
	 * @param array $params
	 */
	public static function run( $name, array $params = array() )
	{
		$ctx = & Context::instance();

		$blocks = & $ctx->get_widget_by_block( $name );
		
		if( $blocks !== NULL )
		{
			if(is_array($blocks))
			{
				foreach($blocks as $block)
				{
					if($block instanceof View) 
					{
						echo $block
							->bind('ctx', $ctx)
							->set('params', $params)
							->render();
					}
					else if($block instanceof Model_Widget_Decorator ) 
					{

						$block
							->set_params($params)
							->render();
					}
					else if( $block instanceof Model_Widget_Part ) 
					{
						echo $block;
					}
				}
			}
			else
			{
				if($blocks instanceof View) 
				{
					echo $blocks
						->bind('ctx', $ctx)
						->set('params', $params)
						->render();
				}
				else if($blocks instanceof Model_Widget_Decorator ) 
				{

					$blocks
						->set_params($params)
						->render();
				}
				else if( $blocks instanceof Model_Widget_Part ) 
				{
					echo $blocks;
				}
			}
		}	
	}
	
	/**
	 * Получение виджетов блока без вывода.
	 * 
	 * Для вывода данных блока
	 * 
	 *		$widget = Block::get('block_name', $params);
	 *		if(is_array($widget))
	 *		{
	 *			foreach($widget as $data)
	 *			{
	 *				echo $data;
	 *			}
	 *		}
	 *		else
	 *			echo $widget;
	 * 
	 * @param string $name
	 * @param array $params Дополнительные параметры доступные в виджете
	 */
	public static function get( $name, array $params = array() )
	{
		$ctx = & Context::instance();

		$blocks = & $ctx->get_widget_by_block( $name );
		
		if( $blocks === NULL )
		{
			return NULL;
		}

		if(is_array($blocks))
		{
			foreach($blocks as & $$block)
			{
				if($block instanceof View) 
				{
					$block
						->bind('ctx', $ctx)
						->set('params', $params);
				}
				else if($block instanceof Model_Widget_Decorator ) 
				{

					$block
						->set_params($params);
				}
			}
		}
		else
		{
			if($blocks instanceof View) 
			{
				$blocks
					->bind('ctx', $ctx)
					->set('params', $params);
			}
			else if($blocks instanceof Model_Widget_Decorator ) 
			{

				$blocks
					->set_params($params);
			}
		}
		
		return $blocks;
	}
	
	/**
	 * Блок типа def служит для помещения в него виджетов без вывода.
	 * Необходим в том случае, если необходимо на странице вывести виджет 
	 * внутри другого виджета, но без разметки блока в шаблоне, в него не получится
	 * поместить виджет.
	 * 
	 * Т.е. в основном шаблоне в самом низу мы указываем, например:
	 * 
	 *		Block::def('block_name_def');
	 * 
	 * Теперь в него можно поместить виджет, далее в шаблоне виджета, в котором
	 * мы хотим его вывести пишем:
	 * 
	 *		Block::run('block_name_def');
	 * 
	 * @param string $name
	 */
	public static function def( $name ){}
	
	/**
	 * Метод служит для поиска в переданном шаблоне размеченных блоков
	 * 
	 * @param string $content
	 * @return string
	 */
	public static function parse_content( $content )
	{
		$content = str_replace(' ', '', $content);
		preg_match_all("/Block::([a-z_]{3,5})\(\'(\w+)\'(\,.*)?\)/i", $content, $blocks);
		
		if( !empty($blocks[2]))
		{
			return $blocks[2];
		}

		return NULL;
	}
}