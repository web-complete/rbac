<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\resource\AbstractResource;

class RbacResourceTest extends RbacTestCase
{

    public function testCreateRole()
    {
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getRole', 2)->returnsMap([
                ['asd', null],
                ['qwe', new Role('qwe')],
            ])
        ]);
        $role = $resource->createRole('asd');
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('asd', $role->getName());
        $this->assertEquals(['asd' => $role], Mocker::getProperty($resource, 'roles'));
        $this->expectException(\WebComplete\rbac\exception\RbacException::class);
        $this->expectExceptionMessage('Role already exists');
        $resource->createRole('qwe');
    }

    public function testDeleteRole()
    {
        $role1 = new Role('role1');
        $role2 = new Role('role2');
        $role3 = new Role('role3');
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class);
        Mocker::setProperty($resource, 'roles', ['role1' => $role1, 'role2' => $role2, 'role3' => $role3]);
        $resource->deleteRole('role1');
        $resource->deleteRole('role3');
        $this->assertEquals(['role2' => $role2], Mocker::getProperty($resource, 'roles'));
        $resource->createRole('role3');
    }

    public function testCreatePermission()
    {
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getPermission', 2)->returnsMap([
                ['asd', null],
                ['qwe', new Permission('qwe', 'rty')],
            ])
        ]);
        $perm = $resource->createPermission('asd', 'fgh');
        $this->assertInstanceOf(Permission::class, $perm);
        $this->assertEquals('asd', $perm->getName());
        $this->assertEquals('fgh', $perm->getDescription());
        $this->assertEquals(['asd' => $perm], Mocker::getProperty($resource, 'permissions'));
        $this->expectException(\WebComplete\rbac\exception\RbacException::class);
        $this->expectExceptionMessage('Permission already exists');
        $resource->createPermission('qwe', 'rty');
    }

    public function testDeletePermission()
    {
        $perm1 = new Role('perm1');
        $perm2 = new Role('perm2');
        $perm3 = new Role('perm3');
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class);
        Mocker::setProperty($resource, 'permissions', ['perm1' => $perm1, 'perm2' => $perm2, 'perm3' => $perm3]);
        $resource->deletePermission('perm1');
        $resource->deletePermission('perm3');
        $this->assertEquals(['perm2' => $perm2], Mocker::getProperty($resource, 'permissions'));
        $resource->createPermission('perm3', '');
    }

    public function testGetRoles()
    {

    }

    public function testGetPermissions()
    {

    }

    public function testClear()
    {
        $role1 = new Role('role1');
        $perm1 = new Role('perm1');
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class);
        Mocker::setProperty($resource, 'roles', ['role1' => $role1]);
        Mocker::setProperty($resource, 'permissions', ['perm1' => $perm1]);
        $resource->clear();
        $this->assertEquals([], Mocker::getProperty($resource, 'roles'));
        $this->assertEquals([], Mocker::getProperty($resource, 'permissions'));
    }

}