<?php

namespace WebComplete\rbac\resource;

use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Role;

class FileResource extends AbstractResource
{
    protected $roleUserIds = [];

    /**
     * @var
     */
    private $file;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->load();
    }

    /**
     * @param $name
     *
     * @return Permission|null
     *
     */
    public function getPermission($name)
    {
        return $this->permissions[$name] ?? null;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param $name
     *
     * @return array|Role[]
     */
    public function getRolesByPermission($name): array
    {
        $result = [];
        /** @var Role $role */
        foreach ($this->roles as $role) {
            if ($role->hasPermission($name)) {
                $result[$role->getName()] = $role;
            }
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return Role|null
     *
     */
    public function getRole($name)
    {
        return $this->roles[$name] ?? null;
    }

    /**
     * @param $userId
     * @param $roleName
     */
    public function userAssignRole($userId, $roleName)
    {
        if (!isset($this->roleUserIds[$roleName])) {
            $this->roleUserIds[$roleName] = [];
        }
        $this->roleUserIds[$roleName][$userId] = true;
    }

    /**
     * @param $userId
     * @param $roleName
     */
    public function userRemoveRole($userId, $roleName)
    {
        if (isset($this->roleUserIds[$roleName][$userId])) {
            unset($this->roleUserIds[$roleName][$userId]);
        }
    }

    /**
     * @param $userId
     * @param $roleName
     *
     * @return bool
     */
    public function userHasRole($userId, $roleName): bool
    {
        return isset($this->roleUserIds[$roleName][$userId]);
    }

    /**
     * @param $userId
     *
     * @return Role[]
     */
    public function userFetchRoles($userId): array
    {
        $result = [];

        $roleNames = [];
        foreach ($this->roleUserIds as $roleName => $userIds) {
            if (isset($userIds[$userId])) {
                $roleNames[] = $roleName;
            }
        }

        foreach ($roleNames as $roleName) {
            if ($role = $this->getRole($roleName)) {
                $result[$roleName] = $role;
            }
        }

        return $result;
    }

    /**
     */
    public function persist()
    {
        $data = [
            'roles' => [],
            'permissions' => [],
        ];
        file_put_contents($this->file, json_encode($data));
    }

    /**
     *
     */
    private function load()
    {
        if (file_exists($this->file)) {
            $data = json_decode(file_get_contents($this->file), true);
            if (!is_array($data)) {
                $data = [];
            }
        } else {
            $data = [];
        }

        if (isset($data['roles']) && is_array($data['roles'])) {
            /** @var string[] $dataRoles */
            $dataRoles = $data['roles'];
            foreach ($dataRoles as $dataRole) {
                /** @var Role $role */
                $role = unserialize($dataRole, ['allowed_classes' => [Role::class]]);
                $this->roles[$role->getName()] = $role;
            }
        }

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            /** @var string[] $dataPermissions */
            $dataPermissions = $data['permissions'];
            foreach ($dataPermissions as $dataPermission) {
                /** @var Permission $permission */
                $permission = unserialize($dataPermission, ['allowed_classes' => [Permission::class]]);
                $this->permissions[$permission->getName()] = $permission;
            }
        }
    }
}
