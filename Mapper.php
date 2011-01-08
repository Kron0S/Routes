<?php
/**
 * Routes package
 *
 * @author  http://kron0s.com
 * @author  Kron0S <al@kron0s.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * @package Routes
 */

/**
 * Класс для роутинга
 *
 * @package Routes
 */
require_once 'Exception.php';
require_once 'Hiqus.php';

class Routes_Mapper
{
	/**
	 * Массив правил роутинга
	 * @var array
	 */
	public $routes = array();

	/**
	 * Добавляем новое правило
	 *
	 * @param  array $rule Правило роутинга
	 * @return void
	 */
	public function addRule($rule)
	{
		$this->routes[] = $rule;
	}

	/**
	 * Находит правило соотвествующее переданному URL
	 * если правила нет - возвращает false
	 *
	 * Использование:
	 *   $match = $route->match('/article/foo/bar/smth');
	 *
	 * @param  string		$url  URL для поиска
	 * @param  array|false	Array если нашли, иначе false
	 */
	public function match($url)
	{
		foreach($this->routes as $rule)
		{
			$result = $this->matchRule($rule, $url);
			if($result)
				return $result;
		}
	}
	/**
	 * Проверяет подходит ли для этого URL это правило
	 * если не подходит - возвращает false
	 *
	 * @param  Array		$rule правило
	 * @param  string		$url  URL
	 * @param  array|false	Array если подходит, иначе false
	 */
	public function matchRule($rule, $url)
	{
		$result = $rule;
		$path = $rule['path'];
		$path = str_replace('/', '\/', $path);
		$path = '/^\/'.$path.'\/?$/i';
		if(preg_match($path, $url, $matches))
		{
			$result['matches'] = $matches;
			foreach($matches as $key=>$match)
			{
				$result['controller'] = str_replace('$'.$key, $match, $result['controller']);
				$result['method'] = str_replace('$'.$key, $match, $result['method']);
				$result['args'] = str_replace('$'.$key, $match, $result['args']);
			}
			if($result['as_array'])
				$result['args'] = Hiqus::encode($result['args']);
			return $result;
		}
		else
			return false;
	}

	/**
	 * Генерирует URL для правила
	 *
	 * Использование:
	 *   $url = $route->generate('pages', array('args'=>$args));
	 *
	 * @param   string			$name Название правила
	 * @param   array			$name Параметры правила
	 * @return  false|string	URL или false
	 */
	public function generate($name, $kargs = array())
	{
		$url = false;
		if(isset($kargs['args']) && is_array($kargs['args']))
			$kargs['args'] = Hiqus::decode($kargs['args']);
		foreach($this->routes as $rule)
		{
			if($rule['name']===$name)
			{
				$url = '/'.$rule['path'];
				foreach($kargs as $key=>$value)
				{
					$pos_begin = strpos($url, '(?P<'.$key.'>');
					$pos_end = strpos($url, ')', $pos_begin);
					$url = substr($url, 0, $pos_begin).$value.substr($url, $pos_end+1);
				}
				$url = str_replace('?', '', $url);
			}
		}
		return $url;
	}
}
