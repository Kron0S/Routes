<?php

require_once 'z:/usr/local/php5/PEAR/PHPUnit/Framework.php';
require_once '../Hiqus.php';

class HiqusTest extends PHPUnit_Framework_TestCase
{
	public function testDecodeSimple() {
		$string = Hiqus::decode(array('a', 's', 'd'));
		$this->assertEquals($string, 'a/s/d');
	}
	public function testDecodeAssociation() {
		$string = Hiqus::decode(array('a', 'b'=>'s', 'd'));
		$this->assertEquals($string, 'a/b=s/d');
	}
	public function testDecodeHierarchy() {
		$string = Hiqus::decode(array('a', 's'=>'a', 's'=>array('b', 'c'=>'e'), array('b', 'c'=>'e'), 'd', 'd'));
		$this->assertEquals($string, 'a/s=b/s_c=e/b/c=e/d/d');
	}
	public function testDecodeHierarchy2() {
		$string = Hiqus::decode(array('a', 's'=>array('b', 'c'=>array('e', 'd'=>'d')), 'd'));
		$this->assertEquals($string, 'a/s=b/s_c=e/s_c_d=d/d');
	}
	public function testEncodeSimple() {
		$data = Hiqus::encode('/a/s/d');
		$this->assertType('array', $data);
		$this->assertEquals($data, array('a', 's', 'd'));
	}
	public function testEncodeAssociation() {
		$data = Hiqus::encode('a/b=dd/b=s/d/d//');
		$this->assertType('array', $data);
		$this->assertEquals($data, array('a', 'b'=>'s', 'd', 'd'));
	}
	public function testEncodeHierarchy() {
		$data = Hiqus::encode('a/s=b/s_c=e/d');
		$this->assertType('array', $data);
		$this->assertEquals($data, array('a', 's'=>array('b', 'c'=>'e'), 'd'));
	}
	public function testEncodeHierarchy2() {
		$data = Hiqus::encode('a/s=b/s_c=e/s_c_d=d/d');
		$this->assertType('array', $data);
		$this->assertEquals($data, array('a', 's'=>array('b', 'c'=>array('e', 'd'=>'d')), 'd'));
	}
	public function testEncodeSimpleDelimiter() {
		Hiqus::setObjectDelimiter(' ');
		$data = Hiqus::encode('a s d');
		$this->assertType('array', $data);
		$this->assertEquals($data, array('a', 's', 'd'));
	}
}
