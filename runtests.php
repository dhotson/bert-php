#!/usr/bin/env php
<?php

define('BASEDIR',dirname(__FILE__));

require_once(BASEDIR.'/lib/simpletest/autorun.php');

$suite = new TestSuite('Tests');

class AllTests extends TestSuite
{
	function AllTests()
	{
		$this->TestSuite('All tests');

		$iterator = new RecursiveDirectoryIterator(BASEDIR.'/tests');
		foreach ($iterator as $file)
		{
			if(preg_match('/Test.php$/',$file->getFileName()))
			{
				$this->addFile($file->getPathname());
			}
		}
	}
}



