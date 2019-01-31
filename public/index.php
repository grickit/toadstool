<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);

  $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));

  if(preg_match('#^/category/([A-Za-z0-9]+)$#', $_SERVER['REQUEST_URI'], $matches))
  {

  }
  elseif(preg_match('#^/images/(original|big|preview)/[A-Za-z0-9_]+\.jpeg$#', $_SERVER['REQUEST_URI']) && preg_match(\Toadstool\Photo::NAMEREGEX, $_SERVER['REQUEST_URI'], $matches))
  {
    $toadstool->servePhoto($matches[1], $matches[11]);
  }
  elseif($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '')
  {
    $toadstool->processUploads();
    $toadstool->render('home');
  }
  else
  {
    header('HTTP/1.1 404 Not Found');
    exit;
  }
?>
