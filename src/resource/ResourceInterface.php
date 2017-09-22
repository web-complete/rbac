<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\RoleInterface;

interface ResourceInterface
{

    /**
     * @param string $name
     *
     * @return RoleInterface
     */
    public function createRole(string $name): RoleInterface;

    /**
     * @param string $name
     * @param string $description
     *
     * @return PermissionInterface
     */
    public function createPermission(string $name, string $description): PermissionInterface;

    /**
     * @return RoleInterface[]
     */
    public function getRoles(): array;

    /**
     * @param string $name
     *
     * @return RoleInterface|null
     */
    public function getRole(string $name);

    /**
     * @param string $name
     */
    public function deleteRole(string $name);

    /**
     * @return PermissionInterface[]
     */
    public function getPermissions(): array;

    /**
     * @param string $name
     *
     * @return PermissionInterface|null
     */
    public function getPermission(string $name);

    /**
     * @param string $name
     */
    public function deletePermission(string $name);

    /**
     */
    public function load();

    /**
     */
    public function save();
}
