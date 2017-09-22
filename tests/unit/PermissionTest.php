<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\RbacInterface;

class PermissionTest extends RbacTestCase
{

    public function testInstance()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        new Permission('perm1', 'desc1', $rbac);
        $this->assertTrue(true);
    }

    public function testGetName()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $perm = new Permission('perm1', 'desc1', $rbac);
        $this->assertEquals('perm1', $perm->getName());
    }

    public function testGetDescription()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $perm = new Permission('perm1', 'desc1', $rbac);
        $this->assertEquals('desc1', $perm->getDescription());
    }

    public function testGetChildren()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $perm2 = new Permission('perm2', 'desc2', $rbac);
        $perm3 = new Permission('perm3', 'desc3', $rbac);

        $rbac = Mocker::create(RbacInterface::class, [
            Mocker::method('getPermission', 3)->returnsMap([
                ['perm2', $perm2],
                ['perm3', $perm3],
            ])
        ]);
        $perm1 = new Permission('perm1', 'desc1', $rbac);
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
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $perm = new Permission('perm1', 'desc1', $rbac);
        $perm->setRuleClass('SomeClass');
        $this->assertEquals('SomeClass', $perm->getRuleClass());
    }

}
