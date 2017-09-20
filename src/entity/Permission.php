<?php

namespace WebComplete\rbac\entity;

use WebComplete\rbac\exception\RbacException;

class Permission
{

    protected $name;
    protected $description;
    protected $childrenNames = [];
    protected $ruleClass;

    /**
     * @param $name
     * @param $description
     */
    public function __construct($name, $description) {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Permission $permission
     */
    public function addChild(Permission $permission)
    {
        $this->childrenNames[$permission->getName()] = true;
    }

    /**
     * @param Permission $permission
     *
     * @return bool
     */
    public function hasChild(Permission $permission)
    {
        return isset($this->childrenNames[$permission->getName()]);
    }

    /**
     * @param Permission $permission
     */
    public function removeChild(Permission $permission)
    {
        unset($this->childrenNames[$permission->getName()]);
    }

    /**
     * @return mixed
     */
    public function getChildrenNames()
    {
        return array_keys($this->childrenNames);
    }

    /**
     * @return mixed
     */
    public function getRuleClass()
    {
        return $this->ruleClass;
    }

    /**
     * @param mixed $ruleClass
     *
     * @throws RbacException
     */
    public function setRuleClass($ruleClass)
    {
        $this->checkRuleInstance(new $ruleClass);
        $this->ruleClass = $ruleClass;
    }

    /**
     * @param $userId
     * @param array $params
     *
     * @return bool
     */
    public function checkRule($userId, array $params) : bool
    {
        if($ruleClass = $this->getRuleClass()) {
            $rule = new $ruleClass;
            $this->checkRuleInstance($rule);
            /** @var Rule $rule */
            return $rule->check($userId, $params);
        }
        return true;
    }

    /**
     * @param $rule
     *
     * @throws RbacException
     */
    protected function checkRuleInstance($rule)
    {
        if(!$rule instanceof Rule) {
            throw new RbacException('Rule is not an instance of ' . Rule::class);
        }
    }

}