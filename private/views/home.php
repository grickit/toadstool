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
          <a href="/article/view/about">About</a></li>
          <a href="/things">Things</a></li>
          <a href="/articles">Articles</a></li>
        </nav>
      </div>
    </div>
  </header>
  <div id="main">
    <div class="container">
      <div class="gallery">
      <?php
        foreach($index['all'] as $i => $name)
        {
          $this->render('_thumbnail', ['name' => $name]);
        }
      ?>
      </div>
    </div>
  </div>
  <footer class="footer">
    <div class="container">
      <p class="pull-left">&copy; Derek Hoagland <?php echo date('Y'); ?></p>
      <p class="pull-right">Powered by Toadstool</p>
    </div>
  </footer>
</body>
</html>






