<?php

class RbacIntegrationTest extends \PHPUnit\Framework\TestCase
{

    protected function createResource()
    {
        $file = __DIR__ . '/../runtime/rbac.data';
        file_put_contents($file, '');
        return new \WebComplete\rbac\resource\FileResource($file);
    }

}