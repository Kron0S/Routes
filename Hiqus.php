<?php
/**
 * Hiqus package
 *
 * @author  http://kron0s.com
 * @author  Kron0S <al@kron0s.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * @package Hiqus
 */

/**
 * Класс для преобразования Hiqus в масив PHP и обратно
 *
 * @package Hiqus
 */
class Hiqus {

	/**
	 * разделитель объектов
	 * @var char
	 */
	private static $delim_object = '/';
	/**
	 * разделитель значения и ключа
	 * @var char
	 */
	private static $delim_value = '=';
	/**
	 * разделитель ключей
	 * @var char
	 */
	private static $delim_key = '_';

	/**
	 * Устанавливает новый разделитель объектов
	 *
	 * @param  string		$delimiter новый разделитель
	 */
	public static function setObjectDelimiter($delimiter)
	{
		self::$delim_object = $delimiter;
	}

	/**
	 * Устанавливает новый разделитель значения и ключа
	 *
	 * @param  string		$delimiter новый разделитель
	 */
	public static function setValueDelimiter($delimiter)
	{
		self::$delim_value = $delimiter;
	}

	/**
	 * Устанавливает новый разделитель ключей
	 *
	 * @param  string		$delimiter новый разделитель
	 */
	public static function setKeyDelimiter($delimiter)
	{
		self::$delim_key = $delimiter;
	}

	/**
	 * Функция для разделения массива
	 *
	 * @param array	$data   Результирующий массив
	 * @param array	$keys   Список ключей до значения
	 * @param mixed	$values Новое значение
	 */
	private static function _req(&$data, $keys, $value)
	{
		$key = array_shift($keys);
		if(!isset($data[$key]))
			$data[$key] = array();
		elseif(!is_array($data[$key]))
			$data[$key] = array($data[$key]);
		if($keys)
			self::_req($data[$key], $keys, $value);
		else
			$data[$key] = $value;
	}

	/**
	 * Преобразует строку из формата Hiqus в массив PHP
	 *
	 * Использование:
	 *   $data = Hiqus::encode('/a/s/d');
	 *
	 * @param  string	$string Строка в формате Hiqus
	 * @param  array	Массив PHP
	 */
	public static function encode($string)
	{
		$string = trim($string, " ".self::$delim_object.self::$delim_key.self::$delim_value);
		$values = explode(self::$delim_object, $string);
		$data = array();
		foreach($values as $value)
		{
			list($key, $value) = explode(self::$delim_value, $value);
			if(is_null($value))
			{
				$value = $key;
				$data[] = $value;
			}
			else
			{
				$keys = explode(self::$delim_key, $key);
				self::_req($data, $keys, $value);
			}
		}
		return $data;
	}

	/**
	 * Преобразует массив PHP в строку формата Hiqus
	 *
	 * Использование:
	 *   $string = Hiqus::decode(array('a', 's', 'd'));
	 *
	 * @param  array	$data Массив PHP
	 * @param  string	Строка в формате Hiqus
	 */
	public static function decode($data)
	{
		if(is_string($data))
			return $data;
		$values = array();
		foreach($data as $key=>$value)
		{
			if(is_array($value))
			{
				if(is_numeric($key))
				{
					$a_values = array();
					foreach($value as $a_key=>$a_value)
						if(is_numeric($a_key))
							$a_values[] = $a_value;
						else
							$a_values[$a_key] = $a_value;
					$res = self::decode($a_values);
					$values[] = $res;
				}
				else
				{
					$a_values = array();
					foreach($value as $a_key=>$a_value)
						if(is_numeric($a_key))
							$a_values[$key] = $a_value;
						else
							$a_values[$key.self::$delim_key.$a_key] = $a_value;
					$res = self::decode($a_values);
					$values[] = $res;
				}
			}
			else
			{
				if(is_numeric($key))
					$values[] = $value;
				else
					$values[] = $key.self::$delim_value.$value;
			}
		}
		$string = implode(self::$delim_object, $values);
		return $string;
	}
}
