<?php

namespace WebComplete\rbac\entity;

abstract class Rule
{

    abstract public function execute($userId, array $params = null);

}