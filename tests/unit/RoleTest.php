<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\RbacInterface;

class RoleTest extends RbacTestCase
{

    public function testInstance()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        new Role('role1', $rbac);
        $this->assertTrue(true);
    }

    public function testGetName()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $role1 = new Role('role1', $rbac);
        $this->assertEquals('role1', $role1->getName());
    }

    public function testGetChildren()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $role2 = new Role('role2', $rbac);
        $role3 = new Role('role3', $rbac);

        $rbac = Mocker::create(RbacInterface::class, [
            Mocker::method('getRole', 3)->returnsMap([
                ['role2', $role2],
                ['role3', $role3],
            ])
        ]);
        $role1 = new Role('role1', $rbac);
        $role1->addChild($role2);
        $role1->addChild($role3);

        $this->assertEquals([
            'role2' => $role2,
            'role3' => $role3
        ], $role1->getChildren());

        $role1->removeChild('role2');
        $this->assertEquals([
            'role3' => $role3
        ], $role1->getChildren());
    }

    public function testGetPermissions()
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
        $role1 = new Role('role1', $rbac);
        $role1->addPermission($perm2);
        $role1->addPermission($perm3);

        $this->assertEquals([
            'perm2' => $perm2,
            'perm3' => $perm3
        ], $role1->getPermissions());

        $role1->removePermission('perm2');
        $this->assertEquals([
            'perm3' => $perm3
        ], $role1->getPermissions());
    }

    public function testGetUserIds()
    {
        /** @var RbacInterface $rbac */
        $rbac = Mocker::create(RbacInterface::class);
        $role1 = new Role('role1', $rbac);
        $role1->assignUserId(1);
        $role1->assignUserId(2);
        $this->assertEquals([1,2], $role1->getUserIds());
        $this->assertTrue($role1->hasUserId(2));
        $role1->removeUserId(2);
        $this->assertEquals([1], $role1->getUserIds());
        $this->assertFalse($role1->hasUserId(2));
    }

}