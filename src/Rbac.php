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
        $this->resource->load();
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
        if ($parentPermission) {
            $parentPermission->addChild($permission);
        }

        return $permission;
    }

    /**
     * @param $name
     *
     * @throws \WebComplete\rbac\exception\RbacException
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
        if ($parentRole) {
            $parentRole->addChild($role);
        }

        return $role;
    }

    /**
     * @param $name
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function deleteRole($name)
    {
        $role = $this->getExistsRole($name);
        $roles = $this->resource->getRoles();
        foreach ($roles as $r) {
            if ($r->hasChild($role)) {
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
            : $this->resource->getRoles();

        foreach ($roles as $role) {
            $this->resource->userRemoveRole($userId, $role->getName());
        }
    }

    /**
     * @param $userId
     * @param $roleName
     *
     * @return bool
     * @throws \WebComplete\rbac\exception\RbacException
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
    public function userGetRoles($userId): array
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
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function userGetPermissions($userId): array
    {
        $result = [];
        foreach ($this->resource->userFetchRoles($userId) as $role) {
            /** @var Role[] $userRoles */
            $userRoles = [$role->getName() => $role];
            $this->collectChildrenRoles($role, $userRoles);
            foreach ($userRoles as $userRole) {
                foreach ($userRole->getPermissionNames() as $permissionName) {
                    if ($permission = $this->getExistsPermission($permissionName)) {
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
        }
        return array_keys($result);
    }

    /**
     * @param $userId
     * @param $permissionName
     * @param array|null $params
     *
     * @return bool
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function userCheckPermission($userId, $permissionName, array $params = null): bool
    {
        $roles = $this->getRolesByPermission($permissionName);
        foreach ($roles as $role) {
            if ($this->resource->userHasRole($userId, $role->getName())) {
                $permission = $this->getExistsPermission($permissionName);
                return $permission ? $permission->checkRule($userId, $params) : false;
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return Role|null
     */
    public function getRole($name)
    {
        return $this->resource->getRole($name);
    }

    /**
     * @param $name
     *
     * @return Permission|null
     */
    public function getPermission($name)
    {
        return $this->resource->getPermission($name);
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
     * @param $roleName
     *
     * @return Role
     * @throws RbacException
     */
    protected function getExistsRole($roleName): Role
    {
        if (!$role = $this->getRole($roleName)) {
            throw new RbacException('Role not found');
        }

        return $role;
    }

    /**
     * @param $permissionName
     *
     * @return Permission|null
     * @throws RbacException
     */
    protected function getExistsPermission($permissionName)
    {
        if (!$permission = $this->getPermission($permissionName)) {
            throw new RbacException('Permission not found');
        }

        return $permission;
    }

    /**
     * @param $permissionName
     *
     * @return Role[]
     */
    protected function getRolesByPermission($permissionName): array
    {
        $result = [];
        foreach ($this->resource->getRoles() as $role) {
            if ($role->hasPermission($permissionName)) {
                $result[$role->getName()] = $role;
            }
        }
        return $result;
    }

    /**
     * @param Role $role
     * @param array $roles
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    private function collectChildrenRoles(Role $role, array &$roles = [])
    {
        foreach ($role->getChildrenNames() as $childRoleName) {
            $childRole = $this->getExistsRole($childRoleName);
            $roles[$childRole->getName()] = $childRole;
            if ($childRole->getChildrenNames()) {
                $this->collectChildrenRoles($childRole, $roles);
            }
        }
    }

    /**
     * @param Permission $permission
     * @param array $permissions
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    private function collectChildrenPermissions(Permission $permission, array &$permissions = [])
    {
        foreach ($permission->getChildrenNames() as $childPermissionName) {
            if ($childPermission = $this->getExistsPermission($childPermissionName)) {
                $permissions[$childPermission->getName()] = $childPermission;
                if ($childPermission->getChildrenNames()) {
                    $this->collectChildrenPermissions($childPermission, $permissions);
                }
            }
        }
    }
}
