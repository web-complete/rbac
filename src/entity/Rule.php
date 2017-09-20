<?php

namespace WebComplete\rbac\entity;

abstract class Rule
{

    abstract public function check($userId, array $params = null) : bool;

}