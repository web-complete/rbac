<?php

use WebComplete\rbac\entity\Rule;

class SomeRule extends Rule
{
    public function check($userId, array $params = null): bool
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
