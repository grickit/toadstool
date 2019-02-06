<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);


  // Make ImageMagick respect PHP's temp directory settings
  \Imagick::setRegistry('temporary-path', sys_get_temp_dir());


  // Initialize out Toadstool project
  $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));


  // Parse the URL
  $route = preg_replace('/\?.+$/', '', $_SERVER['REQUEST_URI']);


  // Determmine what page to load
  if(preg_match('#^/category/([A-Za-z0-9]+)$#', $route, $matches))
  {
    if(isset($_GET['offset']) && is_numeric($_GET['offset']))
      $offset = $_GET['offset'];
    else
      $offset = 0;

    $toadstool->processUploads();
    $toadstool->render('category', ['photos' => array_reverse($toadstool->index['categories'][$matches[1]]), 'offset' => $offset, 'category' => $matches[1]]);
  }
  elseif(preg_match('#^/latest$#', $route))
  {
    if(isset($_GET['offset']) && is_numeric($_GET['offset']))
      $offset = $_GET['offset'];
    else
      $offset = 0;

    $toadstool->processUploads();
    $toadstool->render('photos_by_date', ['photos' => $toadstool->index['dates'], 'offset' => $offset]);
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
