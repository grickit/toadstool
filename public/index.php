<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);


  // Pretty print errors
  set_exception_handler(function($exception) {
    echo '<div style="max-width: 1000px; margin: 20px auto 0 auto; font-family: Arial, sans-serif; font-size: 20px; background-image: url(\'/images/toad_wires.png\'); background-repeat: no-repeat; background-position: right -1%; background-color: #EAEAEA; padding: 1em; background-size: auto 100%; border-radius: 3px; min-height: 300px;">';
    echo '<h1 style="color: #AA0000; font-weight: bold; margin-top: 0;">An error occurred!</h1>';
    echo '<p>';
    echo $exception->getMessage();
    echo '</p>';
    echo '</div>';
  });


  // Make ImageMagick respect PHP's temp directory settings
  \Imagick::setRegistry('temporary-path', sys_get_temp_dir());


  // Actually route the request
  (function() {
    // Initialize out Toadstool project
    $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));

    // Default routing parameters
    $page = null;
    $category = null;
    $offset = 0;
    $process = false;

    // Override routing parameters
    if(isset($_GET['page']) && preg_match('/^([A-Za-z0-9]+)$/', $_GET['page']))
      $page = $_GET['page'];

    if(isset($_GET['category']) && preg_match('/^([A-Za-z0-9 ]+)$/', $_GET['category']))
    {
      $page = 'category';
      $category = $_GET['category'];
    }

    if(isset($_GET['offset']) && is_numeric($_GET['offset']))
      $offset = $_GET['offset'];

    if(isset($_GET['process']))
      $toadstool->processUploads();


    // Determmine what page to load
    // Image files
    if(preg_match(\Toadstool\Photo::NAMEREGEX, $_SERVER['REQUEST_URI'], $matches))
    {
      $toadstool->servePhoto($matches[1], $matches[11]);
    }

    // Latest photos
    elseif($page === null)
    {
      $toadstool->render('pages/home');
    }
    elseif($page === 'latest')
    {
      $toadstool->render('pages/photos_by_date', ['photos' => $toadstool->index['dates'], 'offset' => $offset]);
    }

    // Category page
    elseif($page === 'category')
    {
      if(!isset($toadstool->index['categories'][$category]) || !is_array($toadstool->index['categories'][$category]))
        throw new \Exception('Tried to visit nonexistent category.');

      $toadstool->render('pages/category', ['photos' => array_reverse($toadstool->index['categories'][$category]), 'offset' => $offset, 'category' => $category]);
    }

    // Something else?
    else
    {
      header('HTTP/1.1 404 Not Found');
      exit;
    }
  })();

?>
