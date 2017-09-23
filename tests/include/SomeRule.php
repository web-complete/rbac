<?php

use WebComplete\rbac\entity\RuleInterface;

class SomeRule implements RuleInterface
{

    /**
     * @param int|string $userId
     * @param array|null $params
     *
     * @return bool
     */
    public function execute($userId, $params): bool
    {
        if (!$userId) {
            return false;
        }
        return !($params !== ['qwe']);
    }
}
