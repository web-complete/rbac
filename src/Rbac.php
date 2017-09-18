<?php

namespace WebComplete\rbac;

use WebComplete\rbac\resource\RbacResourceInterface;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;


class Rbac
{

    /**
     * @var RbacResourceInterface
     */
    private $resource;

    public function __construct(RbacResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function createPermission($code, $description, Permission $parentPermission = null) : Permission { /* TODO */ }
    public function createRole($code, Role $parentRole = null) : Role { /* TODO */ }
    public function assign($userId, Role $role) : bool { /* TODO */ }
    public function hasRole($userId, Role $role) : bool { /* TODO */ }
    public function hasPermissions($userId, Permission $permission, array $params = null) : bool { /* TODO */ }
    public function getRole($code) : Role { /* TODO */ }
    public function getPermission($code) : Permission { /* TODO */ }

}