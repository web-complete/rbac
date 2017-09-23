# WebComplete RBAC

[![Build Status](https://travis-ci.org/web-complete/rbac.svg?branch=master)](https://travis-ci.org/web-complete/rbac)
[![Coverage Status](https://coveralls.io/repos/github/web-complete/rbac/badge.svg?branch=master)](https://coveralls.io/github/web-complete/rbac?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/web-complete/rbac/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/web-complete/rbac/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/web-complete/rbac/version)](https://packagist.org/packages/web-complete/rbac)
[![License](https://poser.pugx.org/web-complete/rbac/license)](https://packagist.org/packages/web-complete/rbac)

Tiny flexible RBAC implementation with no dependencies.

Role-based-access-control ([RBAC](https://en.wikipedia.org/wiki/Role-based_access_control)) is a policy neutral access control mechanism defined around roles and privileges. The components of RBAC such as role-permissions, user-role and role-role relationships make it simple to perform user assignments.

## Installation

```
composer require web-complete/rbac
```

## Usage

- **Initiate with resource object**. Resource object can be a FileResource or a RuntimeResource. You can also create any necessary resource (Mysql, Redis, Bongo etc) by extending AbstractResource or implementing ResourceInterface.

```php
$resource = new FileResource($path . '/rbac.data');
$rbac = new Rbac($resource);
```

- **Create permissions hierarchy**

```php
$p1 = $rbac->createPermission('post:create', 'Can create posts');
$p2 = $rbac->createPermission('post:moderate', 'Can moderate posts');
$p3 = $rbac->createPermission('post:update', 'Can update posts');
$p4 = $rbac->createPermission('post:delete', 'Can delete posts');
$p2->addChild($p3); // moderator can also update
$p2->addChild($p4); // and delete posts
```

- **Create role hierarchy**

```php
$adminRole = $rbac->createRole('admin');
$moderatorRole = $rbac->createRole('moderator');
$authorRole = $rbac->createRole('author');
$adminRole->addChild($moderatorRole); // admin has all moderator's rights
```
- **Bind roles and permissions**

```php
...
$moderatorRole->addPermission($p2);
...
```

- **Configure user access rights**

```php
$adminRole->assignUserId(1);
$moderatorRole->assignUserId(2);
$moderatorRole->assignUserId(3);
$authorRole->setUserIds([4,5,6,7,8,9,10]);
```

- **Persist state**

```php
$rbac->save();
```

- **Checking access rights**

```php
if($rbac->checkAccess($userId, 'post:moderate') {
    ... // User can moderate posts
}
// or add to your user's class something like:
$user->can('post:moderate') 
```

### Rules

Sometimes it's not enough to simple check the permission. For example, an author can edit and delete only his own posts. 
For that case you can create a rule by implementing RuleInterface with one method «execute»:

```php

class AuthorRule implements WebComplete\rbac\entity\RuleInterface
{

    /**
     * @param int|string $userId
     * @param array|null $params
     *
     * @return bool
     */
    public function execute($userId, $params): bool
    {
        // @var Post $post
        if($post = $params['post'] ?? null) {
            return $post->authorId === $userId;
        }
        return false;
    }
}
```

- **Configure RBAC**

```php
$p5 = $rbac->createPermission('post:author:update', 'Author can update his posts');
$p6 = $rbac->createPermission('post:author:delete', 'Author can delete his posts');
$authorRole->addPermission($p5);
$authorRole->addPermission($p6);
```

- **And then check rights with parameters**

```php
if($rbac->checkAccess($userId, 'post:author:delete', ['post' => $post]) {
    ... // The user is author of the post and can delete it
}
```