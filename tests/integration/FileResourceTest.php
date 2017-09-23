<?php

use PHPUnit\Framework\TestCase;
use WebComplete\rbac\resource\FileResource;

class FileResourceTest extends TestCase
{
    protected $file = __DIR__ . '/../runtime/rbac.data';

    public function testSaveLoad()
    {
        @unlink($this->file);
        $res = new FileResource($this->file);
        $res->load();
        $this->assertCount(0, $res->getRoles());
        $this->assertCount(0, $res->getPermissions());

        $perm1 = $res->createPermission('perm1', 'desc1');
        $perm2 = $res->createPermission('perm2', 'desc2');
        $perm2->setRuleClass('SomeRuleClass');
        $perm1->addChild($perm2);
        $role1 = $res->createRole('role1');
        $role2 = $res->createRole('role2');
        $role2->setUserIds([4,5]);
        $role1->addChild($role2);
        $role1->setUserIds([1,2,3]);
        $role1->addPermission($perm1);

        $res->save();
        $res->clear();
        $this->assertCount(0, $res->getRoles());
        $this->assertCount(0, $res->getPermissions());
        $res->load();
        $this->assertCount(2, $res->getRoles());
        $this->assertCount(2, $res->getPermissions());

        $role1 = $res->getRole('role1');
        $this->assertEquals([1,2,3], $role1->getUserIds());
        $roleChildren = $role1->getChildren();
        $this->assertCount(1, $roleChildren);
        $role2 = reset($roleChildren);
        $this->assertEquals('role2', $role2->getName());
        $this->assertEquals([4,5], $role2->getUserIds());
        $permissions = $role1->getPermissions();
        $this->assertCount(1, $permissions);
        $perm1 = reset($permissions);
        $this->assertEquals('perm1', $perm1->getName());
        $this->assertEquals('desc1', $perm1->getDescription());
        $permChildren = $perm1->getChildren();
        $this->assertCount(1, $permChildren);
        $perm2 = reset($permChildren);
        $this->assertEquals('perm2', $perm2->getName());
        $this->assertEquals('desc2', $perm2->getDescription());
        $this->assertEquals('SomeRuleClass', $perm2->getRuleClass());
    }
}
