<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\Rbac;
use WebComplete\rbac\resource\AbstractResource;

class RbacTest extends RbacTestCase
{

    public function testInstance()
    {
        $resource = Mocker::create(AbstractResource::class);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $this->assertInstanceOf(Rbac::class, $rbac);
    }

    public function testCreatePermission()
    {
        $permission = new Permission('a2', 'b2');
        $parentPermission = Mocker::create(Permission::class, [
            Mocker::method('addChild', 1, $permission)
        ]);
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('createPermission', 1, ['a', 'b'])->returns($permission)
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->createPermission('a', 'b', $parentPermission);
    }

    public function testDeletePermission()
    {
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('deletePermission', 1, ['a'])
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deletePermission('a');
    }

    public function testCreateRole()
    {
        $role = new Role('a');
        $parentRole = Mocker::create(Role::class, [
            Mocker::method('addChild', 1, [$role])
        ]);
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('createRole', 1, ['a'])->returns($role)
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->createRole('a', $parentRole);
    }

    public function testDeleteRole()
    {
        $role = new Role('moderator');
        $otherRole = Mocker::create(Role::class, [
            Mocker::method('hasChild', 1, [$role])->returns(true),
            Mocker::method('removeChild', 1, [$role])->returns(true),
        ]);
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('fetchRole', 1, 'moderator')->returns($role),
            Mocker::method('fetchRoles', 1)->returns([$otherRole]),
            Mocker::method('deleteRole', 1)->with('moderator')
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deleteRole('moderator');
    }

    public function testUserAssignRole()
    {
        $role = new Role('moderator');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('fetchRole', 1, 'moderator')->returns($role),
            Mocker::method('userAssignRole', 1, [10, 'moderator']),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->userAssignRole(10, 'moderator');
    }

    public function testUserResetRole()
    {
        $role = new Role('moderator');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('fetchRole', 1, 'moderator')->returns($role),
            Mocker::method('userRemoveRole', 1, [10, 'moderator']),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->userResetRole(10, 'moderator');
    }

    public function testUserResetRoleAll()
    {
        $role = new Role('moderator');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('fetchRoles', 1)->returns([$role]),
            Mocker::method('userRemoveRole', 1, [10, 'moderator']),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->userResetRole(10);
    }

    public function testUserHasRole()
    {
        $role = new Role('moderator');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('fetchRole', 1, 'moderator')->returns($role),
            Mocker::method('userHasRole', 1, [10, 'moderator'])->returns(true),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $this->assertEquals(true, $rbac->userHasRole(10, 'moderator'));
    }

    public function testUserGetRoles()
    {
        $role1 = new Role('admin');
        $role2 = new Role('moderator');

        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('userFetchRoles', 1, 10)->returns([$role1, $role2]),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $this->assertEquals(['admin', 'moderator'], $rbac->userGetRoles(10));
    }

    public function testUserGetPermissions()
    {
        $adminRole = new Role('admin');
        $moderatorRole = new Role('moderator');
        $userRole = new Role('user');
        $adminRole->addChild($moderatorRole);
        $moderatorRole->addChild($userRole);

        $adminPerm = new Permission('adminPerm', '');
        $adminSubPerm = new Permission('adminSubPerm', '');
        $moderatorPerm = new Permission('moderatorPerm', '');
        $moderatorSubPerm = new Permission('moderatorSubPerm', '');
        $userPerm = new Permission('userPerm', '');
        $userSubPerm = new Permission('userSubPerm', '');
        $adminPerm->addChild($adminSubPerm);
        $moderatorPerm->addChild($moderatorSubPerm);
        $userPerm->addChild($userSubPerm);

        $adminRole->addPermission($adminPerm);
        $moderatorRole->addPermission($moderatorPerm);
        $userRole->addPermission($userPerm);

        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('userFetchRoles', 1, 10)->returns([$adminRole]),
            Mocker::method('fetchRole')->returnsMap([
                ['admin', $adminRole],
                ['moderator', $moderatorRole],
                ['user', $userRole],
            ]),
            Mocker::method('fetchPermission')->returnsMap([
                ['adminPerm', $adminPerm],
                ['adminSubPerm', $adminSubPerm],
                ['moderatorPerm', $moderatorPerm],
                ['moderatorSubPerm', $moderatorSubPerm],
                ['userPerm', $userPerm],
                ['userSubPerm', $userSubPerm],
            ]),
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $this->assertEquals([
            'adminPerm',
            'adminSubPerm',
            'moderatorPerm',
            'moderatorSubPerm',
            'userPerm',
            'userSubPerm',
            ], $rbac->userGetPermissions(10));
    }

    public function testUserCheckPermission()
    {

    }

    public function testGetRole()
    {

    }

    public function testGetPermission()
    {

    }

    public function testSave()
    {

    }

    public function testClear()
    {

    }

}