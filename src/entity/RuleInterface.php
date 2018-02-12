<?php

namespace WebComplete\rbac\entity;

interface RuleInterface
{

    /**
     * @param array|null $params
     *
     * @return bool
     */
    public function execute($params): bool;
}
