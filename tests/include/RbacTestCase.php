<?php

use Mvkasatkin\mocker\Mocker;

class RbacTestCase extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        parent::setUp();
        Mocker::init($this);
    }

}