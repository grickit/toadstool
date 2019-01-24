<?php
  namespace Toadstool;

  abstract class Base
  {
    public function __get($name)
    {
      if(method_exists($this, "get{$name}") && is_callable([$this, "get{$name}"]))
        return call_user_func_array([$this, "get{$name}"], []);

      else return null;
    }

    public function __set($name, $value)
    {
      if(method_exists($this, "set{$name}") && is_callable([$this, "set{$name}"]))
        return call_user_func_array([$this, "set{$name}"], [$value]);

      else return null;
    }
  }
  