<?php

namespace WebComplete\rbac\entity;

interface PermissionInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param PermissionInterface $permission
     */
    public function addChild(PermissionInterface $permission);

    /**
     * @param string $permissionName
     */
    public function removeChild(string $permissionName);

    /**
     * @return PermissionInterface[]
     */
    public function getChildren(): array;

    /**
     * @param string $ruleClass
     */
    public function setRuleClass(string $ruleClass);

    /**
     * @return string|null;
     */
    public function getRuleClass();

    /**
     * @param array|null $params
     *
     * @return bool
     */
    public function checkAccess($params = null): bool;
}
