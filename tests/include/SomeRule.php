<?php

use WebComplete\rbac\entity\RuleInterface;

class SomeRule implements RuleInterface
{

    /**
     * @param array|null $params
     *
     * @return bool
     */
    public function execute($params): bool
    {
        return !($params !== ['qwe']);
    }
}
