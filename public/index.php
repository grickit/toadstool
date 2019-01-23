<?php
  // Allows us to autoload our other files just by using them
  function ToadstoolAutoloader($class) {
    require __DIR__ . '/../private/includes/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
  }
  spl_autoload_register('ToadstoolAutoloader', true, true);

  $toadstool = new \Toadstool\Toadstool(realpath(__DIR__.'/..'));


  if(isset($_GET['name']))
  {
    if(preg_match('/^([0-9]{14}_[A-Z0-9]+_[A-Z0-9]+)_(big|preview)$/', $_GET['name'], $matches))
    {
      if($matches[2] === 'preview')
      {
        $photo = \Toadstool\Photo::createFromPreviewImagePath($toadstool, "{$toadstool->previewImagesPath}/{$_GET['name']}.jpeg");
      }
      elseif($matches[2] === 'big') {
        $photo = \Toadstool\Photo::createFromBigImagePath($toadstool, "{$toadstool->bigImagesPath}/{$_GET['name']}.jpeg");
        if(!$photo->testBigImagePath())
          $photo->createBigImage();
      }
    }

    if($photo)
    {
      header('Content-Type: image/jpeg');
      header('Content-Length: '. filesize($photo->bigImagePath));
      readfile($photo->bigImagePath);
      exit;
    }
    else
    {
      header('HTTP/1.1 401 Unauthorized');
      exit;
    }
  }
  else
  {
    $toadstool->processUploads();
    $toadstool->processImages();
  }
?>
