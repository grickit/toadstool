<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>toad.photos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
</head>
<body>
  <?php
    foreach($index['all'] as $i => $name)
    {
      $this->render('_thumbnail', ['name' => $name]);
    }
  ?>
</body>
</html>