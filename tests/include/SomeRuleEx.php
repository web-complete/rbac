<?php

use WebComplete\rbac\entity\RuleInterface;

class SomeRuleEx
{

    /**
     * @param int|string $userId
     * @param array|null $params
     *
     * @return bool
     * @throws \Exception
     */
    public function execute($userId, $params): bool
    {
        return true;
    }
}
