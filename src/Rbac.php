<?php

namespace WebComplete\rbac;

use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\RoleInterface;
use WebComplete\rbac\resource\ResourceInterface;

class Rbac implements RbacInterface
{

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @param ResourceInterface $resource
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
        $this->resource->load();
    }

    /**
     * @param string $name
     *
     * @return RoleInterface
     */
    public function createRole(string $name): RoleInterface
    {
        return $this->resource->createRole($name);
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
        return $this->resource->createPermission($name, $description);
    }

    /**
     * @return RoleInterface[]
     */
    public function getRoles(): array
    {
        return $this->resource->getRoles();
    }

    /**
     * @param string $name
     *
     * @return RoleInterface|null
     */
    public function getRole(string $name)
    {
        return $this->resource->getRole($name);
    }

    /**
     * @param string $name
     */
    public function deleteRole(string $name)
    {
        $this->resource->deleteRole($name);
    }

    /**
     * @return PermissionInterface[]
     */
    public function getPermissions(): array
    {
        return $this->resource->getPermissions();
    }

    /**
     * @param string $name
     *
     * @return PermissionInterface|null
     */
    public function getPermission(string $name)
    {
        return $this->resource->getPermission($name);
    }

    /**
     * @param string $name
     */
    public function deletePermission(string $name)
    {
        $this->resource->deletePermission($name);
    }

    /**
     */
    public function clear()
    {
        $this->resource->clear();
    }

    /**
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function load()
    {
        $this->resource->load();
    }

    /**
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function save()
    {
        $this->resource->save();
    }
}
