<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\rbac\entity\Permission;
use WebComplete\rbac\entity\Rule;

class PermissionTest extends RbacTestCase
{

    public function testInstance()
    {
        $perm = new Permission('perm', '');
        $this->assertInstanceOf(Permission::class, $perm);
    }

    public function testGetName()
    {
        $perm = new Permission('perm1', '');
        $this->assertEquals('perm1', $perm->getName());
    }

    public function testGetDescription()
    {
        $perm = new Permission('perm1', 'description1');
        $this->assertEquals('description1', $perm->getDescription());
    }

    public function testAddHasGetRemoveChild()
    {
        $perm1 = new Permission('perm1', '');
        $perm2 = new Permission('perm2', '');
        $perm3 = new Permission('perm3', '');
        $perm31 = new Permission('perm31', '');
        $perm3->addChild($perm31);
        $perm1->addChild($perm2);
        $perm1->addChild($perm3);
        $this->assertTrue($perm1->hasChild($perm2));
        $this->assertEquals(['perm2', 'perm3'], $perm1->getChildrenNames());
        $perm1->removeChild($perm2);
        $this->assertFalse($perm1->hasChild($perm2));
    }

    public function testSetGetRuleClass()
    {
        $rule = Mocker::create(Rule::class);
        $ruleClass = get_class($rule);
        $perm = new Permission('perm1', '');
        $perm->setRuleClass($ruleClass);
        $this->assertEquals($ruleClass, $perm->getRuleClass());
    }

    public function testCheckRule()
    {
        $perm = new Permission('perm1', '');
        $perm->setRuleClass(SomeRule::class);
        $this->assertEquals(true, $perm->checkRule(10, ['qwe']));
    }

    public function testCheckNoRule()
    {
        $perm = new Permission('perm1', '');
        $this->assertEquals(true, $perm->checkRule(10, ['qwe']));
    }

    public function testCheckNotRule()
    {
        $this->expectException(\WebComplete\rbac\exception\RbacException::class);
        $this->expectExceptionMessage('Rule is not an instance of ' . Rule::class);
        $perm = new Permission('perm1', '');
        Mocker::setProperty($perm, 'ruleClass', stdClass::class);
        $this->assertEquals(true, $perm->checkRule(10, ['qwe']));
    }

}