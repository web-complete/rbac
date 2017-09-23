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
     * @var array
     */
    protected $userIds = [];

    /**
     * @param string $name
     * @param ResourceInterface $resource
     */
    public function __construct(string $name, ResourceInterface $resource)
    {
        $this->name = $name;
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
     * @return PermissionInterface[]
     */
    public function getPermissions(): array
    {
        $result = [];
        $permissionNames = \array_keys($this->permissionNames);
        foreach ($permissionNames as $name) {
            $result[$name] = $this->resource->getPermission($name);
        }
        return $result;
    }

    /**
     * @param string|int $userId
     */
    public function assignUserId($userId)
    {
        $this->userIds[$userId] = true;
    }

    /**
     * @param array $userIds
     */
    public function setUserIds(array $userIds)
    {
        foreach ($userIds as $userId) {
            $this->assignUserId($userId);
        }
    }

    /**
     * @param $userId
     *
     * @return bool
     */
    public function hasUserId($userId): bool
    {
        return isset($this->userIds[$userId]);
    }

    /**
     * @param string|int $userId
     */
    public function removeUserId($userId)
    {
        unset($this->userIds[$userId]);
    }

    /**
     * @return string[]|int[]
     */
    public function getUserIds(): array
    {
        return \array_keys($this->userIds);
    }
}
