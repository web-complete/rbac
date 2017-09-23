<?php

use PHPUnit\Framework\TestCase;
use WebComplete\rbac\exception\RbacException;
use WebComplete\rbac\Rbac;
use WebComplete\rbac\resource\RuntimeResource;

class RbacUsageTest extends TestCase
{

    public function testDeletePermission()
    {
        $rbac = $this->configureRbac();
        $rbac->deletePermission('perm3');
        $role3 = $rbac->getRole('role3');
        $this->assertCount(0, $role3->getPermissions());
        $perm2 = $rbac->getPermission('perm2');
        $this->assertCount(0, $perm2->getChildren());
    }

    public function testDeleteRole()
    {
        $rbac = $this->configureRbac();
        $rbac->deleteRole('role3');
        $role2 = $rbac->getRole('role2');
        $this->assertCount(1, $role2->getChildren());
    }

    public function testCheckAccess()
    {
        $rbac = $this->configureRbac();
        $this->assertTrue($rbac->checkAccess(3, 'perm1'));
        $this->assertTrue($rbac->checkAccess(3, 'perm2'));
        $this->assertTrue($rbac->checkAccess(3, 'perm3'));
        $this->assertFalse($rbac->checkAccess(3, 'perm4'));
        $this->assertFalse($rbac->checkAccess(3, 'perm4', ['qwe']));

        $this->assertTrue($rbac->checkAccess(7, 'perm3'));
        $this->assertFalse($rbac->checkAccess(7, 'perm4', ['qwe']));

        $this->assertFalse($rbac->checkAccess(8, 'perm3'));
        $this->assertFalse($rbac->checkAccess(8, 'perm4'));
        $this->assertTrue($rbac->checkAccess(8, 'perm4', ['qwe']));

        $this->assertTrue($rbac->checkAccess(5, 'perm3'));
        $this->assertTrue($rbac->checkAccess(5, 'perm4', ['qwe']));
        $this->assertFalse($rbac->checkAccess(5, 'perm4'));
        $this->assertFalse($rbac->checkAccess(5, 'perm2'));
        $this->assertFalse($rbac->checkAccess(5, 'perm1'));
    }

    public function testRuleNotExistsException()
    {
        $this->expectException(RbacException::class);
        $rbac = $this->configureRbac();
        $rbac->getPermission('perm1')->setRuleClass('SomeRuleNotExists');
        $rbac->checkAccess(1, 'perm1');
    }

    public function testRuleException()
    {
        $this->expectException(RbacException::class);
        $rbac = $this->configureRbac();
        $rbac->getPermission('perm1')->setRuleClass(SomeRuleEx::class);
        $rbac->checkAccess(1, 'perm1');
    }

    public function testCreateDuplicateRole()
    {
        $this->expectException(RbacException::class);
        $rbac = $this->configureRbac();
        $rbac->createRole('role1');
    }

    public function testCreateDuplicatePermission()
    {
        $this->expectException(RbacException::class);
        $rbac = $this->configureRbac();
        $rbac->createPermission('perm1', 'descriptionX');
    }

    /**
     * @return Rbac
     */
    protected function configureRbac()
    {
        $res = new RuntimeResource();
        $rbac = new Rbac($res);

        $perm1 = $rbac->createPermission('perm1', 'desc1');
        $perm2 = $rbac->createPermission('perm2', 'desc2');
        $perm3 = $rbac->createPermission('perm3', 'desc3');
        $perm4 = $rbac->createPermission('perm4', 'desc4');
        $perm2->addChild($perm3);
        $perm4->setRuleClass(SomeRule::class);

        $role1 = $rbac->createRole('role1');
        $role2 = $rbac->createRole('role2');
        $role3 = $rbac->createRole('role3');
        $role4 = $rbac->createRole('role4');
        $role1->setUserIds([1,2,3]);
        $role2->setUserIds([4,5]);
        $role3->setUserIds([6,7]);
        $role4->setUserIds([8,9]);
        $role2->addChild($role3);
        $role2->addChild($role4);
        $role1->addPermission($perm1);
        $role1->addPermission($perm2);
        $role3->addPermission($perm3);
        $role4->addPermission($perm4);
        $rbac->save();

        return $rbac;
    }

}