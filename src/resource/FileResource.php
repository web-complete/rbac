<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\entity\RoleInterface;
use WebComplete\rbac\exception\RbacException;

class FileResource implements ResourceInterface
{

    /**
     * @var string
     */
    protected $file;
    /**
     * @var Role[]
     */
    protected $roles = [];
    /**
     * @var Permission[]
     */
    protected $permissions = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @param string $name
     *
     * @return RoleInterface
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function createRole(string $name): RoleInterface
    {
        if (isset($this->roles[$name])) {
            throw new RbacException('Role already exists');
        }
        $role = new Role($name, $this);
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
    public function createPermission(string $name, string $description): PermissionInterface
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
    }

    /**
     */
    public function clear()
    {
        $this->roles = [];
        $this->permissions = [];
    }

    /**
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function load()
    {
        // TODO: Implement load() method.
    }

    /**
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function save()
    {
        // TODO: Implement save() method.
    }
}