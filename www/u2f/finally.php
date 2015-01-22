<?php

// php <5.5 has no support for finally
// mimic using class, see
// http://phpsadness.com/sad/49
class Finally
{
  private $_callback = NULL;
  private $_args = array();

  function __construct($callback, array $args = array())
  {
    if (!is_callable($callback))
    {
      throw new Exception("Callback was not callable!");
    }

    $this->_callback = $callback;
    $this->_args = $args;
  }

  public function done()
  {
    if ($this->_callback)
    {
      call_user_func_array($this->_callback, $this->_args);
      $this->_callback = NULL;
      $this->_args = array();
    }
  }

  function __destruct()
  {
    $this->done();
  }
}
