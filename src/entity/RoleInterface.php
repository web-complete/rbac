<?php

namespace WebComplete\rbac\entity;

interface RoleInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param RoleInterface $role
     */
    public function addChild(RoleInterface $role);

    /**
     * @param string $roleName
     */
    public function removeChild(string $roleName);

    /**
     * @return RoleInterface[]
     */
    public function getChildren(): array;

    /**
     * @param PermissionInterface $permission
     */
    public function addPermission(PermissionInterface $permission);

    /**
     * @param string $permissionName
     */
    public function removePermission(string $permissionName);

    /**
     * @return PermissionInterface[]
     */
    public function getPermissions(): array;

    /**
     * @param string|int $userId
     */
    public function assignUserId($userId);

    /**
     * @param $userId
     *
     * @return bool
     */
    public function hasUserId($userId): bool;

    /**
     * @param string|int $userId
     */
    public function removeUserId($userId);

    /**
     * @return string[]|int[]
     */
    public function getUserIds(): array;
}
