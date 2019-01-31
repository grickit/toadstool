<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);

  $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));

  $route = preg_replace('/\?.+$/', '', $_SERVER['REQUEST_URI']);

  if(preg_match('#^/category/([A-Za-z0-9]+)$#', $route, $matches))
  {

  }
  elseif(preg_match('#^/images/(original|big|preview)/[A-Za-z0-9_]+\.jpeg$#', $route) && preg_match(\Toadstool\Photo::NAMEREGEX, $_SERVER['REQUEST_URI'], $matches))
  {
    $toadstool->servePhoto($matches[1], $matches[11]);
  }
  elseif($route === '/' || $route === '')
  {
    if(isset($_GET['offset']) && is_numeric($_GET['offset']))
      $offset = $_GET['offset'];
    else
      $offset = 0;

    $toadstool->processUploads();
    $toadstool->render('gallery', ['photos' => array_reverse($toadstool->index['all']), 'offset' => $offset]);
  }
  else
  {
    header('HTTP/1.1 404 Not Found');
    exit;
  }
?>
