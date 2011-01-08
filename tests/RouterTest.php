<?php

require_once 'z:/usr/local/php5/PEAR/PHPUnit/Framework.php';
require_once '../Mapper.php';

class Mapper_RouterTest extends PHPUnit_Framework_TestCase
{
	public function testDefault() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'default',
			'controller'=>'pages',
			'method'=>'index',
			'path'=>'',
			'args'=>'',
			'as_array'=>false,
		));
		$match = $route->match('/foo');
		$this->assertEquals($match, false);
		
		$match = $route->match('/');
		$this->assertArrayHasKey('matches', $match);
		$this->assertArrayHasKey('controller', $match);
		$this->assertEquals($match['controller'], 'pages');
		$this->assertArrayHasKey('method', $match);
		$this->assertEquals($match['method'], 'index');
	}
	public function testRegexp() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'langs',
			'controller'=>'langs',
			'method'=>'$method',
			'path'=>'langs/(?P<method>.*)',
			'args'=>'',
			'as_array'=>false,
		));
		$match = $route->match('/foo/bar');
		$this->assertEquals($match, false);
		
		$match = $route->match('/langs/index');
		$this->assertArrayHasKey('matches', $match);
		$this->assertArrayHasKey('controller', $match);
		$this->assertEquals($match['controller'], 'langs');
		$this->assertArrayHasKey('method', $match);
		$this->assertEquals($match['method'], 'index');
	}
	public function testAdminUrl() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'admin',
			'controller'=>'$controller',
			'method'=>'admin_$method',
			'path'=>'admin/(?P<controller>[^/]*)/?(?P<method>[^/]*)/?(?P<args>.*)',
			'args'=>'$args',
			'as_array'=>true,
		));
		$match = $route->match('/foo/bar');
		$this->assertEquals($match, false);
		
		$match = $route->match('/admin/langs/edit/1');
		$this->assertArrayHasKey('matches', $match);
		$this->assertArrayHasKey('controller', $match);
		$this->assertEquals($match['controller'], 'langs');
		$this->assertArrayHasKey('method', $match);
		$this->assertEquals($match['method'], 'admin_edit');
		$this->assertArrayHasKey('args', $match);
		$this->assertType('array', $match['args']);
	}
	public function testAllToHiqus() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'pages',
			'controller'=>'pages',
			'method'=>'display',
			'path'=>'(?P<args>.*)',
			'args'=>'$args',
			'as_array'=>true,
		));
		$match = $route->match('/article/foo/bar/smth');
		$this->assertArrayHasKey('matches', $match);
		$this->assertArrayHasKey('controller', $match);
		$this->assertEquals($match['controller'], 'pages');
		$this->assertArrayHasKey('method', $match);
		$this->assertEquals($match['method'], 'display');
		$this->assertArrayHasKey('args', $match);
		$this->assertType('array', $match['args']);
	}
	public function testGenerateDefault() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'default',
			'controller'=>'pages',
			'method'=>'index',
			'path'=>'',
			'args'=>'',
			'as_array'=>false,
		));
		
		$url = $route->generate('default');
		$this->assertEquals($url, '/');
	}
	public function testGenerateWithHiqus() {
		$route = new Routes_Mapper();
		$route->addRule(array(
			'name'=>'pages',
			'controller'=>'pages',
			'method'=>'display',
			'path'=>'(?P<args>.*)',
			'args'=>'$args',
			'as_array'=>true,
		));
		
		$args = Hiqus::encode('a/s/d');
		$url = $route->generate('pages', array('args'=>$args));
		$this->assertEquals($url, '/a/s/d');
	}
}
