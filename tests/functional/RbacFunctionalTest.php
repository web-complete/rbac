<?php

use WebComplete\rbac\entity\Role;

class RbacFunctionalTest extends \PHPUnit\Framework\TestCase
{

    public function testSimple()
    {
        $roleAdmin = new Role('admin');
    }

    protected function createResource()
    {
        $file = __DIR__ . '/../runtime/rbac.data';
        file_put_contents($file, '');
        return new \WebComplete\rbac\resource\FileResource($file);
    }

    /**
     * TODO remove role - will remove it from children of other roles and will remove its children
     * TODO remove perm - will remove it from children of other perms and will remove its children
     * TODO reset user role without roleName will reset user from all roles
     * TODO getUserPermissions - will return all perms + subperms of role + subroles
     * TODO test rules
     * TODO DEV get assignments by role (single and deep)
     * TODO DEV get assignments by permission (single and deep?)
     */
}