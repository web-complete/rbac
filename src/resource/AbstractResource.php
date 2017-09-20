<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;
use WebComplete\rbac\exception\RbacException;


abstract class AbstractResource
{

    protected $roles = [];
    protected $rolesCreate = [];
    protected $rolesUpdate = [];
    protected $rolesDelete = [];

    protected $permissions = [];
    protected $permissionsCreate = [];
    protected $permissionsUpdate = [];
    protected $permissionsDelete = [];

    /**
     * @param $name
     *
     * @return Role
     * @throws RbacException
     */
    public function createRole($name)
    {
        if($this->fetchRole($name)) {
            throw new RbacException('Role already exists');
        }

        $role = new Role($name);
        $this->roles[$name] = $role;
        $this->rolesCreate[$name] = true;
        return $role;
    }

    /**
     * @param $name
     */
    public function deleteRole($name)
    {
        unset($this->roles[$name]);
        $this->rolesDelete[$name] = true;
    }

    /**
     * Mark for update
     *
     * @param $name
     */
    public function markRole($name)
    {
        $this->rolesUpdate[$name] = true;
    }

    /**
     * @param $name
     * @param $description
     *
     * @return Permission
     * @throws RbacException
     */
    public function createPermission($name, $description)
    {
        if($this->fetchPermission($name)) {
            throw new RbacException('Permission already exists');
        }

        $permission = new Permission($name, $description);
        $this->permissions[$name] = $permission;
        $this->permissionsCreate[$name] = true;
        return $permission;
    }

    /**
     * @param $name
     */
    public function deletePermission($name)
    {
        unset($this->permissions[$name]);
        $this->permissionsDelete[$name] = true;
    }

    /**
     * Mark for update
     *
     * @param $name
     */
    public function markPermission($name)
    {
        $this->permissionsUpdate[$name] = $name;
    }

    /**
     * @param $name
     *
     * @return null|Permission
     *
     */
    abstract public function fetchPermission($name) : Permission;

    /**
     * @return Role[]
     */
    abstract public function fetchRoles() : array;

    /**
     * @param $name
     *
     * @return array|Role[]
     */
    abstract public function fetchRolesWithPermission($name) : array;

    /**
     * @param $name
     *
     * @return Role
     *
     */
    abstract public function fetchRole($name) : Role;

    /**
     * @param $userId
     * @param $roleName
     */
    abstract public function userAssignRole($userId, $roleName);

    /**
     * @param $userId
     * @param $roleName
     */
    abstract public function userRemoveRole($userId, $roleName);

    /**
     * @param $userId
     * @param $roleName
     *
     * @return bool
     */
    abstract public function userHasRole($userId, $roleName) : bool;

    /**
     * @param $userId
     *
     * @return Role[]
     */
    abstract public function userFetchRoles($userId) : array;

    /**
     */
    abstract public function persist();

    /**
     */
    abstract public function clear();

}