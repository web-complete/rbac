<?php

namespace WebComplete\rbac\entity;


class Role
{

    protected $name;
    protected $childrenNames = [];
    protected $permissionNames = [];

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Role $role
     */
    public function addChild(Role $role)
    {
        $this->childrenNames[$role->getName()] = true;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasChild(Role $role)
    {
        return isset($this->childrenNames[$role->getName()]);
    }

    /**
     * @param Role $role
     */
    public function removeChild(Role $role)
    {
        unset($this->childrenNames[$role->getName()]);
    }

    /**
     * @return array
     */
    public function getChildrenNames()
    {
        return array_keys($this->childrenNames);
    }

    /**
     * @param Permission $permission
     */
    public function addPermission(Permission $permission)
    {
        $this->permissionNames[$permission->getName()] = true;
    }

    /**
     * @param Permission $permission
     */
    public function removePermission(Permission $permission)
    {
        unset($this->permissionNames[$permission->getName()]);
    }

    /**
     * @param Permission $permission
     *
     * @return bool
     */
    public function hasPermission(Permission $permission)
    {
        return isset($this->permissionNames[$permission->getName()]);
    }

    /**
     * @return array
     */
    public function getPermissionNames()
    {
        return array_keys($this->permissionNames);
    }

}