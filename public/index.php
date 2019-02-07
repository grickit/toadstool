<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);

  set_exception_handler(function($exception) {
    echo '<div style="max-width: 1000px; margin: 20px auto 0 auto; font-family: Arial, sans-serif; font-size: 20px;">';
    echo '<h1 style="color: #AA0000; font-weight: bold;">An error occurred!</h1>';
    echo '<p>';
    echo $exception->getMessage();
    echo '</p>';
    echo '</div>';
  });


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
  elseif($route === '/' || $route === '' || preg_match('#^/latest$#', $route))
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
  else
  {
    header('HTTP/1.1 404 Not Found');
    exit;
  }
?>
