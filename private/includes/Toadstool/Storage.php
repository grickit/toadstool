<?php
  namespace Toadstool;

  abstract class Storage extends \Toadstool\Base
  {
    protected $_parent;
    protected $_credentials;
    protected $_client;

    public abstract function loadCredentials();
    public abstract function loadClient();
    public abstract function testFile($name);
    public abstract function getFile($name);
    public abstract function writeFile($name, $filepath, $public = true);
    // public abstract function writeBlob($name, $blob, $public = true);
    //public abstract function write($name, $filepath, $public = true);

    public static function createFromToadstool($parent)
    {
      $class = get_called_class();
      $storage = new $class();
      $storage->parent = $parent;
      $storage->loadCredentials();
      $storage->loadClient();
      return $storage;
    }

    // Set the parent object (once only)
    public function setParent($parent)
    {
      if($this->_parent === null)
      {
        $this->_parent = $parent;
        return true;
      }

      throw new \Exception('Storage interface already has parent. Cannot set again.');
    }
  }