<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\entity\RoleInterface;
use WebComplete\rbac\exception\RbacException;

abstract class AbstractResource implements ResourceInterface
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
     * @param string $name
     * @param string $description
     *
     * @return RoleInterface
     * @throws RbacException
     */
    public function createRole(string $name, string $description = ''): RoleInterface
    {
        if (isset($this->roles[$name])) {
            throw new RbacException('Role already exists');
        }
        $role = new Role($name, $description, $this);
        $this->roles[$name] = $role;
        return $role;
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return PermissionInterface
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function createPermission(string $name, string $description = ''): PermissionInterface
    {
        if (isset($this->permissions[$name])) {
            throw new RbacException('Permission already exists');
        }
        $permission = new Permission($name, $description, $this);
        $this->permissions[$name] = $permission;
        return $permission;
    }

    /**
     * @return RoleInterface[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string $name
     *
     * @return RoleInterface|null
     */
    public function getRole(string $name)
    {
        return $this->roles[$name] ?? null;
    }

    /**
     * @param string $name
     */
    public function deleteRole(string $name)
    {
        unset($this->roles[$name]);
        foreach ($this->getRoles() as $role) {
            $role->removeChild($name);
        }
    }

    /**
     * @return PermissionInterface[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param string $name
     *
     * @return PermissionInterface|null
     */
    public function getPermission(string $name)
    {
        return $this->permissions[$name] ?? null;
    }

    /**
     * @param string $name
     */
    public function deletePermission(string $name)
    {
        unset($this->permissions[$name]);
        foreach ($this->getRoles() as $role) {
            $role->removePermission($name);
        }
        foreach ($this->getPermissions() as $permission) {
            $permission->removeChild($name);
        }
    }

    /**
     */
    public function clear()
    {
        $this->roles = [];
        $this->permissions = [];
    }
}
