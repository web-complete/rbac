<?php

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;

class RoleTest extends \PHPUnit\Framework\TestCase
{

    public function testInstance()
    {
        $role = new Role('adminRole', '');
        $this->assertInstanceOf(Role::class, $role);
    }

    public function testGetName()
    {
        $role = new Role('adminRole', '');
        $this->assertEquals('adminRole', $role->getName());
    }

    public function testAddHasGetRemoveChild()
    {
        $role = new Role('adminRole');
        $role2 = new Role('adminRole2');
        $role3 = new Role('adminRole3');
        $role31 = new Role('adminRole3');
        $role3->addChild($role31);
        $role->addChild($role2);
        $role->addChild($role3);
        $this->assertTrue($role->hasChild($role2));
        $this->assertEquals(['adminRole2', 'adminRole3'], $role->getChildrenNames());
        $role->removeChild($role2);
        $this->assertFalse($role->hasChild($role2));
    }

    public function testAddHasGetRemovePermission()
    {
        $role = new Role('adminRole');
        $perm1 = new Permission('perm1', '');
        $perm2 = new Permission('perm2', '');
        $perm21 = new Permission('perm21', '');
        $perm2->addChild($perm21);
        $role->addPermission($perm1);
        $role->addPermission($perm2);
        $this->assertTrue($role->hasPermission($perm2));
        $this->assertEquals(['perm1', 'perm2'], $role->getPermissionNames());
        $role->removePermission($perm2);
        $this->assertFalse($role->hasPermission($perm2));
    }

}