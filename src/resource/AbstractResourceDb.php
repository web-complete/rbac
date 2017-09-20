<?php

namespace WebComplete\rbac\resource;

abstract class AbstractResourceDb extends AbstractResource
{

    /**
     * Table name
     * @return string
     */
    abstract protected function getTable() : string;

}