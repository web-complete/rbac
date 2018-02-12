<?php

namespace WebComplete\rbac\entity;

interface RoleInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

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
     * @param bool $withChildren
     *
     * @return PermissionInterface[]
     */
    public function getPermissions(bool $withChildren = false): array;

    /**
     * @param string $permissionName
     * @param array|null $params
     *
     * @return bool
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function checkAccess($permissionName, $params = null): bool;
}
