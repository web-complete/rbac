<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\exception\RbacException;

abstract class AbstractResource
{
    /**
     * @var Role[]
     */
    protected $roles = [];
    /**
     * @var Permission[]
     */
    protected $permissions = [];
    /**
     * @var array [roleName => userIds]
     */
    protected $userAssignments = [];

    /**
     * @param $name
     *
     * @return Role
     * @throws RbacException
     */
    public function createRole($name): Role
    {
        if ($this->getRole($name)) {
            throw new RbacException('Role already exists');
        }

        $role = new Role($name);
        $this->roles[$name] = $role;
        return $role;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param $name
     *
     * @return Role|null
     *
     */
    public function getRole($name)
    {
        return $this->roles[$name] ?? null;
    }

    /**
     * @param $name
     */
    public function deleteRole($name)
    {
        unset($this->roles[$name]);
    }

    /**
     * @param $name
     * @param $description
     *
     * @return Permission
     * @throws RbacException
     */
    public function createPermission($name, $description): Permission
    {
        if ($this->getPermission($name)) {
            throw new RbacException('Permission already exists');
        }

        $permission = new Permission($name, $description);
        $this->permissions[$name] = $permission;
        return $permission;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param $name
     *
     * @return null|Permission
     *
     */
    public function getPermission($name)
    {
        return $this->permissions[$name] ?? null;
    }

    /**
     * @param $name
     */
    public function deletePermission($name)
    {
        unset($this->permissions[$name]);
    }

    /**
     * @param $userId
     * @param $roleName
     */
    public function userAssignRole($userId, $roleName)
    {
        if (!isset($this->userAssignments[$roleName])) {
            $this->userAssignments[$roleName] = [];
        }
        $this->userAssignments[$roleName][$userId] = true;
    }

    /**
     * @param $userId
     * @param $roleName
     */
    public function userRemoveRole($userId, $roleName)
    {
        unset($this->userAssignments[$roleName][$userId]);
    }

    /**
     * @param $userId
     * @param $roleName
     *
     * @return bool
     */
    public function userHasRole($userId, $roleName): bool
    {
        return isset($this->userAssignments[$roleName][$userId]);
    }

    /**
     * @param $userId
     *
     * @return Role[]
     */
    public function userFetchRoles($userId): array
    {
        $result = [];
        foreach ($this->userAssignments as $roleName => $userIds) {
            if (isset($userIds[$userId])) {
                $result[$roleName] = $this->getRole($roleName);
            }
        }
        return $result;
    }

    /**
     */
    public function clear()
    {
        $this->roles = [];
        $this->permissions = [];
    }

    /**
     */
    abstract public function load();

    /**
     */
    abstract public function persist();
}
