<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>toad.photos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="content">
        <a href="/" class="brand">toad.photos</a>
        <nav>
          <a href="/all">All Photos</a>
          <?php
            foreach($index['categories'] as $name => $category)
            {
              if($name !== 'Uncategorized')
                echo "<a href=\"/category/{$name}\">{$name}</a>";
            }
          ?>
          <a href="/category/Uncategorized">Other</a>
        </nav>
      </div>
    </div>
  </header>
  <div id="main">
    <div class="container">
      <div class="gallery">
      <?php
        foreach(array_reverse($index['all']) as $i => $name)
        {
          $this->render('_thumbnail', ['name' => $name]);
        }
      ?>
      </div>
    </div>
  </div>
  <footer class="footer">
    <div class="container">
      <p style="margin-bottom: 4em;">High quality original photos available upon request and approval.</p>
      <p>&copy; <?php echo date('Y'); ?> Derek Hoagland </p>
      <p>Powered by <a href="https://github.com/grickit/toadstool">Toadstool</a></p>
    </div>
  </footer>
</body>
</html>






