<?php

namespace App\Services\BaseCrud\Traits;

trait HasCrudHooks
{

    protected function callHook(string $name)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }
        return null;
    }

    public function __beforeList()
    {

        return $this->callHook('beforeList');
    }

    public function __afterList()
    {

        return $this->callHook('afterList');
    }

    public function __beforeShow()
    {

        return $this->callHook('beforeShow');
    }

    public function __afterShow()
    {

        return $this->callHook('afterShow');
    }

    public function __beforeCreate()
    {
        return $this->callHook('beforeCreate');
    }

    public function __afterCreate()
    {
        return $this->callHook('afterCreate');
    }

    public function __beforeStore()
    {
        return $this->callHook('beforeStore');
    }

    public function __afterStore()
    {
        return $this->callHook('afterStore');
    }

    public function __beforeEdit()
    {
        return $this->callHook('beforeEdit');
    }

    public function __afterEdit()
    {
        return $this->callHook('afterEdit');
    }

    public function __beforeUpdate()
    {
        return $this->callHook('beforeUpdate');
    }


    public function __afterUpdate()
    {
        return $this->callHook('afterUpdate');
    }

    public function __beforeDestroy()
    {
        return $this->callHook('beforeDestroy');
    }

    public function __afterDestroy()
    {
        return $this->callHook('afterDestroy');
    }

    public function __beforeBulkDestroy()
    {
        return $this->callHook('beforeBulkDestroy');
    }

    public function __afterBulkDestroy()
    {
        return $this->callHook('afterBulkDestroy');
    }

    public function __beforeForceDestroy()
    {
        return $this->callHook('beforeForceDestroy');
    }

    public function __afterForceDestroy()
    {
        return $this->callHook('afterForceDestroy');
    }

    public function __beforeBulkForceDestroy()
    {
        return $this->callHook('beforeBulkForceDestroy');
    }

    public function __afterBulkForceDestroy()
    {
        return $this->callHook('afterBulkForceDestroy');
    }

    public function __beforePrint()
    {
        return $this->callHook('beforePrint');
    }
}
