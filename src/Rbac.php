<?php

namespace WebComplete\rbac;

use WebComplete\rbac\exception\RbacException;
use WebComplete\rbac\resource\AbstractResource;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;


class Rbac
{

    /**
     * @var AbstractResource
     */
    protected $resource;

    /**
     * @param AbstractResource $resource
     */
    public function __construct(AbstractResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param $name
     * @param $description
     * @param Permission|null $parentPermission
     *
     * @return Permission
     * @throws RbacException
     */
    public function createPermission($name, $description, Permission $parentPermission = null) : Permission
    {
        $permission = $this->resource->createPermission($name, $description);
        if($parentPermission) {
            $parentPermission->addChild($permission);
        }

        return $permission;
    }

    /**
     * @param $name
     */
    public function deletePermission($name)
    {
        $this->getExistsPermission($name);
        $this->resource->deletePermission($name);
    }

    /**
     * @param $name
     * @param Role|null $parentRole
     *
     * @return Role
     * @throws RbacException
     */
    public function createRole($name, Role $parentRole = null) : Role
    {
        $role = $this->resource->createRole($name);
        if($parentRole) {
            $parentRole->addChild($role);
        }

        return $role;
    }

    /**
     * @param $name
     */
    public function deleteRole($name)
    {
        $role = $this->getExistsRole($name);
        $roles = $this->resource->fetchRoles();
        foreach ($roles as $r) {
            if($r->hasChild($role)) {
                $r->removeChild($role);
            }
        }
        $this->resource->deleteRole($name);
    }

    /**
     * @param $userId
     * @param $roleName
     *
     * @throws RbacException
     */
    public function userAssignRole($userId, $roleName)
    {
        $this->getExistsRole($roleName);
        $this->resource->userAssignRole($userId, $roleName);
    }

    /**
     * @param $userId
     * @param null $roleName
     *
     * @throws RbacException
     */
    public function userResetRole($userId, $roleName = null)
    {
        /** @var Role[] $roles */
        $roles = $roleName
            ? [$this->getExistsRole($roleName)]
            : $this->resource->fetchRoles();

        foreach ($roles as $role) {
            $this->resource->userRemoveRole($userId, $role->getName());
        }
    }

    /**
     * @param $userId
     * @param $roleName
     *
     * @return bool
     *
     */
    public function userHasRole($userId, $roleName) : bool
    {
        $this->getExistsRole($roleName);
        return $this->resource->userHasRole($userId, $roleName);
    }

    /**
     * @param $userId
     *
     * @return array roleNames
     */
    public function userGetRoles($userId)
    {
        $result = [];
        foreach ($this->resource->userFetchRoles($userId) as $role) {
            $result[$role->getName()] = true;
        }
        return array_keys($result);
    }

    /**
     * @param $userId
     *
     * @return string[] permissionNames
     */
    public function userGetPermissions($userId)
    {
        $result = [];
        foreach ($this->resource->userFetchRoles($userId) as $role) {
            $userRoles = [$role->getName() => $role];
            $this->collectChildrenRoles($role, $userRoles);
            foreach ($userRoles as $r) {
                foreach ($r->getPermissionNames() as $permissionName) {
                    $permission = $this->getExistsPermission($permissionName);
                    $result[$permission->getName()] = $permission;
                    /** @var Permission[] $childrenPermissions */
                    $childrenPermissions = [];
                    $this->collectChildrenPermissions($permission, $childrenPermissions);
                    foreach ($childrenPermissions as $childPermission) {
                        $result[$childPermission->getName()] = true;
                    }
                }
            }
        }
        return array_keys($result);
    }

    /**
     * @param $userId
     * @param $permissionName
     * @param array|null $params
     *
     * @return bool
     */
    public function userCheckPermission($userId, $permissionName, array $params = null) : bool
    {
        $roles = $this->resource->fetchRolesWithPermission($permissionName);
        foreach ($roles as $role) {
            if($this->resource->userHasRole($userId, $role->getName())) {
                $permission = $this->getExistsPermission($permissionName);
                return $permission->checkRule($userId, $params);
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return Role|null
     */
    public function getRole($name) : Role
    {
        return $this->resource->fetchRole($name);
    }

    /**
     * @param $name
     *
     * @return Permission|null
     */
    public function getPermission($name) : Permission
    {
        return $this->resource->fetchPermission($name);
    }

    /**
     * @throws RbacException
     */
    public function save()
    {
        $this->resource->persist();
    }

    /**
     *
     */
    public function clear()
    {
        $this->resource->clear();
    }

    /**
     * @param Role $role
     * @param array $roles
     */
    private function collectChildrenRoles(Role $role, &$roles = [])
    {
        foreach ($role->getChildrenNames() as $childRoleName) {
            $childRole = $this->getExistsRole($childRoleName);
            $roles[$childRole->getName()] = $childRole;
            if($childRole->getChildrenNames()) {
                $this->collectChildrenRoles($childRole, $roles);
            }
        }
    }

    /**
     * @param Permission $permission
     * @param array $permissions
     */
    private function collectChildrenPermissions(Permission $permission, &$permissions = [])
    {
        foreach ($permission->getChildrenNames() as $childPermissionName) {
            $childPermission = $this->getExistsPermission($childPermissionName);
            $permissions[$childPermission->getName()] = $childPermission;
            if($childPermission->getChildrenNames()) {
                $this->collectChildrenPermissions($childPermission, $permissions);
            }
        }
    }

    /**
     * @param $roleName
     *
     * @return Role
     * @throws RbacException
     */
    protected function getExistsRole($roleName)
    {
        if(!$role = $this->getRole($roleName)) {
            throw new RbacException('Role not found');
        }

        return $role;
    }

    /**
     * @param $permissionName
     *
     * @return Permission
     * @throws RbacException
     */
    protected function getExistsPermission($permissionName)
    {
        if(!$permission = $this->getPermission($permissionName)) {
            throw new RbacException('Permission not found');
        }

        return $permission;
    }

}