<?php

namespace WebComplete\rbac\entity;

use WebComplete\rbac\resource\ResourceInterface;

class Role implements RoleInterface
{

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var ResourceInterface
     */
    protected $resource;
    /**
     * @var array
     */
    protected $childrenNames = [];
    /**
     * @var array
     */
    protected $permissionNames = [];

    /**
     * @param string $name
     * @param string $description
     * @param ResourceInterface $resource
     */
    public function __construct(string $name, string $description, ResourceInterface $resource)
    {
        $this->name = $name;
        $this->description = $description;
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param RoleInterface $role
     */
    public function addChild(RoleInterface $role)
    {
        $this->childrenNames[$role->getName()] = true;
    }

    /**
     * @param string $roleName
     */
    public function removeChild(string $roleName)
    {
        unset($this->childrenNames[$roleName]);
    }

    /**
     * @return RoleInterface[]
     */
    public function getChildren(): array
    {
        $result = [];
        $roleNames = \array_keys($this->childrenNames);
        foreach ($roleNames as $name) {
            $result[$name] = $this->resource->getRole($name);
        }
        return $result;
    }

    /**
     * @param PermissionInterface $permission
     */
    public function addPermission(PermissionInterface $permission)
    {
        $this->permissionNames[$permission->getName()] = true;
    }

    /**
     * @param string $permissionName
     */
    public function removePermission(string $permissionName)
    {
        unset($this->permissionNames[$permissionName]);
    }

    /**
     * @param bool $withChildren
     *
     * @return PermissionInterface[]
     */
    public function getPermissions(bool $withChildren = false): array
    {
        $result = [];
        $permissionNames = \array_keys($this->permissionNames);

        foreach ($permissionNames as $name) {
            $permission = $this->resource->getPermission($name);
            $result[$name] = $permission;
        }

        if ($withChildren) {
            foreach ($result as $permission) {
                $this->collectChildrenPermissions($permission, $result);
            }
            foreach ($this->getChildren() as $child) {
                $result = \array_merge($result, $child->getPermissions(true));
            }
        }

        return $result;
    }

    /**
     * @param string $permissionName
     * @param array|null $params
     *
     * @return bool
     */
    public function checkAccess($permissionName, $params = null): bool
    {
        $permissions = $this->getPermissions(true);
        if (isset($permissions[$permissionName])) {
            if ($permissions[$permissionName]->checkAccess($params)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param PermissionInterface $permission
     * @param $result
     */
    protected function collectChildrenPermissions(PermissionInterface $permission, &$result)
    {
        foreach ($permission->getChildren() as $childPermission) {
            $childPermissionName = $childPermission->getName();
            if (!isset($result[$childPermissionName])) {
                $result[$childPermissionName] = $childPermission;
                $this->collectChildrenPermissions($childPermission, $result);
            }
        }
    }
}
