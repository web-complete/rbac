<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\PermissionInterface;
use WebComplete\rbac\entity\RoleInterface;

class FileResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $file;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @throws \WebComplete\rbac\exception\RbacException
     */
    public function load()
    {
        $this->clear();

        if (!file_exists($this->file) || (!$data = json_decode(file_get_contents($this->file), true))) {
            $data = [];
        }

        $this->restorePermissions($data['permissions'] ?? []);
        $this->restoreRoles($data['roles'] ?? []);
    }

    /**
     */
    public function save()
    {
        $data = [
            'roles' => [],
            'permissions' => [],
        ];
        foreach ($this->roles as $role) {
            $data['roles'][$role->getName()] = $this->roleToRow($role);
        }
        foreach ($this->permissions as $permission) {
            $data['permissions'][$permission->getName()] = $this->permissionToRow($permission);
        }

        file_put_contents($this->file, json_encode($data));
    }

    /**
     * @param RoleInterface $role
     *
     * @return array
     */
    protected function roleToRow(RoleInterface $role): array
    {
        $result = [];
        $result['name'] = $role->getName();
        $result['description'] = $role->getDescription();
        $childrenNames = [];
        foreach ($role->getChildren() as $child) {
            $childrenNames[] = $child->getName();
        }
        $result['children'] = $childrenNames;
        $permissionNames = [];
        foreach ($role->getPermissions() as $permission) {
            $permissionNames[] = $permission->getName();
        }
        $result['permissions'] = $permissionNames;
        return $result;
    }

    /**
     * @param PermissionInterface $permission
     *
     * @return array
     */
    protected function permissionToRow(PermissionInterface $permission): array
    {
        $result = [];
        $result['name'] = $permission->getName();
        $result['description'] = $permission->getDescription();
        $childrenNames = [];
        foreach ($permission->getChildren() as $child) {
            $childrenNames[] = $child->getName();
        }
        $result['children'] = $childrenNames;
        $result['ruleClass'] = $permission->getRuleClass();
        return $result;
    }

    /**
     * @param array[] $permissionsData
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    protected function restorePermissions($permissionsData)
    {
        /** @var string[][] $permChildrenNames */
        $permChildrenNames = [];

        foreach ($permissionsData as $pData) {
            $permission = $this->createPermission($pData['name'] ?? '', $pData['description'] ?? '');
            $permission->setRuleClass($pData['ruleClass'] ?? '');
            $permChildrenNames[$permission->getName()] = $pData['children'] ?? [];
        }

        foreach ($permChildrenNames as $permissionName => $childrenNames) {
            foreach ($childrenNames as $childName) {
                $permission = $this->getPermission($permissionName);
                $child = $this->getPermission($childName);
                if ($permission && $child) {
                    $permission->addChild($child);
                }
            }
        }
    }

    /**
     * @param array[] $rolesData
     *
     * @throws \WebComplete\rbac\exception\RbacException
     */
    protected function restoreRoles($rolesData)
    {
        /** @var string[][] $rolesChildrenNames */
        $rolesChildrenNames = [];

        foreach ($rolesData as $rData) {
            $role = $this->createRole($rData['name'] ?? '', $rData['description'] ?? '');
            $rolesChildrenNames[$role->getName()] = $rData['children'] ?? [];
            $permissionNames = $rData['permissions'] ?? [];
            foreach ($permissionNames as $permissionName) {
                if ($permission = $this->getPermission($permissionName)) {
                    $role->addPermission($permission);
                }
            }
        }

        foreach ($rolesChildrenNames as $roleName => $childrenNames) {
            foreach ($childrenNames as $childName) {
                $role = $this->getRole($roleName);
                $child = $this->getRole($childName);
                if ($role && $child) {
                    $role->addChild($child);
                }
            }
        }
    }
}
