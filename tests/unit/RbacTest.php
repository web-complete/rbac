<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\exception\RbacException;
use WebComplete\rbac\Rbac;
use WebComplete\rbac\resource\AbstractResource;

class RbacTest extends RbacTestCase
{

    public function testInstance()
    {
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('load', 1)
        ]);
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
        $adminPerm = new Permission('adminPerm', '');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getPermission', 1, ['adminPerm'])->returns($adminPerm),
            Mocker::method('deletePermission', 1, ['adminPerm'])
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deletePermission('adminPerm');
    }

    public function testDeleteNotExistPermission()
    {
        $this->expectException(RbacException::class);
        $this->expectExceptionMessage('Permission not found');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getPermission')->returns(null)
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deletePermission('moderatorPerm');
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
            Mocker::method('getRole', 1, 'moderator')->returns($role),
            Mocker::method('getRoles', 1)->returns([$otherRole]),
            Mocker::method('deleteRole', 1)->with('moderator')
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deleteRole('moderator');
    }

    public function testDeleteNotExistRole()
    {
        $this->expectException(RbacException::class);
        $this->expectExceptionMessage('Role not found');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getRole')->returns(null)
        ]);
        /** @var AbstractResource $resource */
        $rbac = new Rbac($resource);
        $rbac->deleteRole('moderator');
    }

    public function testUserAssignRole()
    {
        $role = new Role('moderator');
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getRole', 1, 'moderator')->returns($role),
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
            Mocker::method('getRole', 1, 'moderator')->returns($role),
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
            Mocker::method('getRoles', 1)->returns([$role]),
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
            Mocker::method('getRole', 1, 'moderator')->returns($role),
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
            Mocker::method('getRole')->returnsMap([
                ['admin', $adminRole],
                ['moderator', $moderatorRole],
                ['user', $userRole],
            ]),
            Mocker::method('getPermission')->returnsMap([
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
        $adminRole = new Role('adminRole');
        /** @var Permission $adminPerm */
        $adminPerm = Mocker::create(Permission::class, [
            Mocker::method('checkRule', 1, [10, [1,2,3]])->returns(true)
        ], ['adminPerm', '']);
        $adminSubPerm = new Permission('adminSubPerm', '');
        $adminPerm->addChild($adminSubPerm);
        $adminRole->addPermission($adminPerm);

        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('userHasRole', 1, [10, 'adminRole'])->returns(true),
            Mocker::method('getPermission', 1, 'adminPerm')->returns($adminPerm),
        ]);

        $rbac = new Rbac($resource);
        $this->assertTrue($rbac->userCheckPermission(10, 'adminPerm', [1,2,3]));
    }

    public function testUserCheckPermissionFalse()
    {
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class);
        $rbac = new Rbac($resource);
        $this->assertFalse($rbac->userCheckPermission(10, 'adminPerm', [1,2,3]));
    }

    public function testGetRole()
    {
        $adminRole = new Role('adminRole');
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getRole', 1, 'adminRole')->returns($adminRole),
        ]);
        $rbac = new Rbac($resource);
        $this->assertEquals($adminRole, $rbac->getRole('adminRole'));
    }

    public function testGetPermission()
    {
        $adminPerm = new Permission('adminPerm', '');
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('getPermission', 1, 'adminPerm')->returns($adminPerm),
        ]);
        $rbac = new Rbac($resource);
        $this->assertEquals($adminPerm, $rbac->getPermission('adminPerm'));
    }

    public function testSave()
    {
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('persist', 1),
        ]);
        $rbac = new Rbac($resource);
        $rbac->save();
    }

    public function testClear()
    {
        /** @var AbstractResource $resource */
        $resource = Mocker::create(AbstractResource::class, [
            Mocker::method('clear', 1),
        ]);
        $rbac = new Rbac($resource);
        $rbac->clear();
    }

}