<?php

namespace WebComplete\rbac\entity;

use WebComplete\rbac\RbacInterface;

class Permission implements PermissionInterface
{

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var RbacInterface
     */
    protected $rbac;
    /**
     * @var array
     */
    protected $childrenNames = [];
    /**
     * @var string
     */
    protected $ruleClass;

    /**
     * @param string $name
     * @param string $description
     * @param RbacInterface $rbac
     */
    public function __construct(string $name, string $description, RbacInterface $rbac)
    {
        $this->name = $name;
        $this->description = $description;
        $this->rbac = $rbac;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param PermissionInterface $permission
     */
    public function addChild(PermissionInterface $permission)
    {
        $this->childrenNames[$permission->getName()] = true;
    }

    /**
     * @param string $permissionName
     */
    public function removeChild(string $permissionName)
    {
        unset($this->childrenNames[$permissionName]);
    }

    /**
     * @return PermissionInterface[]
     */
    public function getChildren(): array
    {
        $result = [];
        $permissionNames = \array_keys($this->childrenNames);
        foreach ($permissionNames as $name) {
            $result[$name] = $this->rbac->getPermission($name);
        }
        return $result;
    }

    /**
     * @param string $ruleClass
     */
    public function setRuleClass(string $ruleClass)
    {
        $this->ruleClass = $ruleClass;
    }

    /**
     * @return string|null;
     */
    public function getRuleClass()
    {
        return $this->ruleClass;
    }
}
