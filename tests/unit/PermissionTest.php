<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\resource\ResourceInterface;

class PermissionTest extends RbacTestCase
{

    public function testInstance()
    {
        /** @var ResourceInterface $res */
        $res = Mocker::create(ResourceInterface::class);
        new Permission('perm1', 'desc1', $res);
        $this->assertTrue(true);
    }

    public function testGetName()
    {
        /** @var ResourceInterface $res */
        $res = Mocker::create(ResourceInterface::class);
        $perm = new Permission('perm1', 'desc1', $res);
        $this->assertEquals('perm1', $perm->getName());
    }

    public function testGetDescription()
    {
        /** @var ResourceInterface $res */
        $res = Mocker::create(ResourceInterface::class);
        $perm = new Permission('perm1', 'desc1', $res);
        $this->assertEquals('desc1', $perm->getDescription());
    }

    public function testGetChildren()
    {
        /** @var ResourceInterface $res */
        $res = Mocker::create(ResourceInterface::class);
        $perm2 = new Permission('perm2', 'desc2', $res);
        $perm3 = new Permission('perm3', 'desc3', $res);

        $res = Mocker::create(ResourceInterface::class, [
            Mocker::method('getPermission', 3)->returnsMap([
                ['perm2', $perm2],
                ['perm3', $perm3],
            ])
        ]);
        $perm1 = new Permission('perm1', 'desc1', $res);
        $perm1->addChild($perm2);
        $perm1->addChild($perm3);

        $this->assertEquals([
            'perm2' => $perm2,
            'perm3' => $perm3
        ], $perm1->getChildren());

        $perm1->removeChild('perm2');
        $this->assertEquals([
            'perm3' => $perm3
        ], $perm1->getChildren());
    }

    public function testSetGetRule()
    {
        /** @var ResourceInterface $res */
        $res = Mocker::create(ResourceInterface::class);
        $perm = new Permission('perm1', 'desc1', $res);
        $perm->setRuleClass('SomeClass');
        $this->assertEquals('SomeClass', $perm->getRuleClass());
    }
}
