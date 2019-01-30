<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);

  $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));


  if(isset($_GET['name']))
  {
    $toadstool->servePhoto($_GET['name']);
  }
  else
  {
    $toadstool->processUploads();
    $toadstool->render('home', ['index' => $toadstool->index]);
  }
?>
