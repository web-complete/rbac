<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\RoleInterface;
use WebComplete\rbac\Rbac;
use WebComplete\rbac\resource\ResourceInterface;

class RbacTest extends RbacTestCase
{

    public function testInstance()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class);
        new Rbac($resource);
        $this->assertTrue(true);
    }

    /**
     * Full covering in the integration test
     */
    public function testCheckAccess()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class);
        $rbac = new Rbac($resource);
        $this->assertFalse($rbac->checkAccess(10, 'some', [1,2,3]));
    }

    public function testCreateRole()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('createRole', 1, 'role1')->returns(
                Mocker::create(RoleInterface::class)
            )
        ]);
        $rbac = new Rbac($resource);
        $this->assertInstanceOf(RoleInterface::class, $rbac->createRole('role1'));
    }

    public function testCreatePermission()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('createPermission', 1, ['perm1', 'desc1'])->returns(
                Mocker::create(PermissionInterface::class)
            )
        ]);
        $rbac = new Rbac($resource);
        $this->assertInstanceOf(PermissionInterface::class, $rbac->createPermission('perm1', 'desc1'));
    }

    public function testGetRoles()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('getRoles', 1)->returns([])
        ]);
        $rbac = new Rbac($resource);
        $this->assertEquals([], $rbac->getRoles());
    }

    public function testGetRole()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('getRole', 1, 'role1')->returns(
                Mocker::create(RoleInterface::class)
            )
        ]);
        $rbac = new Rbac($resource);
        $this->assertInstanceOf(RoleInterface::class, $rbac->getRole('role1'));
    }

    public function testDeleteRole()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('deleteRole', 1, 'role1')
        ]);
        $rbac = new Rbac($resource);
        $rbac->deleteRole('role1');
    }

    public function testGetPermissions()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('getPermissions', 1)->returns([])
        ]);
        $rbac = new Rbac($resource);
        $this->assertEquals([], $rbac->getPermissions());
    }

    public function testGetPermission()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('getPermission', 1, 'perm1')->returns(
                Mocker::create(PermissionInterface::class)
            )
        ]);
        $rbac = new Rbac($resource);
        $this->assertInstanceOf(PermissionInterface::class, $rbac->getPermission('perm1'));
    }

    public function testDeletePermission()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('deletePermission', 1, 'perm1')
        ]);
        $rbac = new Rbac($resource);
        $rbac->deletePermission('perm1');
    }

    public function testLoad()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('load', 1)
        ]);
        $rbac = new Rbac($resource);
        $rbac->load();
    }

    public function testSave()
    {
        /** @var ResourceInterface $resource */
        $resource = Mocker::create(ResourceInterface::class, [
            Mocker::method('save', 1)
        ]);
        $rbac = new Rbac($resource);
        $rbac->save();
    }

}
