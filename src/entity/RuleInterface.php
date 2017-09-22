<?php

namespace WebComplete\rbac\entity;

interface RuleInterface
{

    /**
     * @param string|int $userId
     * @param array|null $params
     *
     * @return bool
     */
    public function execute($userId, $params): bool;
}
