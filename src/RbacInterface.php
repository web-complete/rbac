<?php

namespace WebComplete\rbac;

use WebComplete\rbac\resource\ResourceInterface;

interface RbacInterface extends ResourceInterface
{

    /**
     * @param string|int $userId
     * @param string $permissionName
     * @param array|null $params
     *
     * @return bool
     */
    public function checkAccess($userId, $permissionName, $params = null): bool;
}
