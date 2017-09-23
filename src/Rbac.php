<?php

namespace WebComplete\rbac;

use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\RoleInterface;
use WebComplete\rbac\entity\RuleInterface;
use WebComplete\rbac\exception\RbacException;
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
     * @param string|int $userId
     * @param string $permissionName
     * @param array|null $params
     *
     * @return bool
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function checkAccess($userId, $permissionName, $params = null): bool
    {
        $result = false;
        $roles = $this->getAllRolesByUserId($userId);
        $permissions = $this->getAllPermissionsByRoles($roles);
        if (isset($permissions[$permissionName])) {
            $result = $this->checkPermissionAccess($permissions[$permissionName], $userId, $params);
        }
        return $result;
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

    /**
     * @param $userId
     *
     * @return RoleInterface[]
     */
    protected function getAllRolesByUserId($userId): array
    {
        $result = [];
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            if ($role->hasUserId($userId)) {
                $result[$role->getName()] = $role;
                $this->collectChildrenRoles($role, $result);
            }
        }
        return $result;
    }

    /**
     * @param RoleInterface $role
     * @param $result
     */
    protected function collectChildrenRoles(RoleInterface $role, &$result)
    {
        foreach ($role->getChildren() as $childRole) {
            $childRoleName = $childRole->getName();
            if (!isset($result[$childRoleName])) {
                $result[$childRoleName] = $childRole;
                $this->collectChildrenRoles($childRole, $result);
            }
        }
    }

    /**
     * @param RoleInterface[] $roles
     *
     * @return PermissionInterface[]
     */
    protected function getAllPermissionsByRoles(array $roles): array
    {
        $result = [];
        foreach ($roles as $role) {
            $permissions = $role->getPermissions();
            foreach ($permissions as $permission) {
                $permissionName = $permission->getName();
                if (!isset($result[$permissionName])) {
                    $result[$permissionName] = $permission;
                    $this->collectChildrenPermissions($permission, $result);
                }
            }
        }
        return $result;
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

    /**
     * @param PermissionInterface $permission
     * @param $userId
     * @param $params
     *
     * @return bool
     * @throws RbacException
     */
    protected function checkPermissionAccess(PermissionInterface $permission, $userId, $params): bool
    {
        $result = true;
        if ($ruleClass = $permission->getRuleClass()) {
            try {
                $rule = new $ruleClass;
                if (!$rule instanceof RuleInterface) {
                    throw new RbacException('Rule class: ' . $ruleClass . ' is not an ' . RuleInterface::class);
                }

                $result = $rule->execute($userId, $params);
            } catch (\Throwable $e) {
                throw new RbacException('Cannot instantiate rule class: ' . $ruleClass, $e);
            }
        }
        return $result;
    }
}
