<?php

require_once(BASEDIR.'/classes/Bert.php');
require_once(BASEDIR.'/classes/Ernie.php');

class MyClass
{
	public function a() { }
	public function b() { }
}

class ErnieTest extends UnitTestCase
{
	public function testMod()
	{
		Mock::generate('MyClass', 'MyMock');
		$m = new MyMock();
		Ernie::mod('test', array(
			'a' => array($m, 'a'),
			'b' => array($m, 'b'),
		));

		$m->expectCallCount('a', 1);
		$m->expectCallCount('b', 1);

		Ernie::dispatch('test', 'a', array());
		Ernie::dispatch('test', 'b', array());
	}

	public function testFun()
	{
		Mock::generate('MyClass', 'MyMock');
		$m = new MyMock();
		Ernie::mod('test', array(
			'a' => array($m, 'a'),
		));

		Ernie::fun('b', array($m, 'b'));

		$m->expectCallCount('a', 1);
		$m->expectCallCount('b', 1);

		Ernie::dispatch('test', 'a', array());
		Ernie::dispatch('test', 'b', array());
	}

	public function testExpose()
	{
		Mock::generate('MyClass', 'MyMock');
		$m = new MyMock();

		Ernie::expose('test', $m);

		$m->expectCallCount('a', 1);
		$m->expectCallCount('b', 1);

		Ernie::dispatch('test', 'a', array());
		Ernie::dispatch('test', 'b', array());
	}


}
