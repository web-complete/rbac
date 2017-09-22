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
        if ($userId !== 10) {
            return false;
        }
        if ($params !== ['qwe']) {
            return false;
        }
        return true;
    }
}
