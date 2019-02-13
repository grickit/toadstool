<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>toad.photos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="/resources/css/main.css?version=1" />
  </head>
<body>
  <header class="header">
    <div class="container">
      <div class="content">
        <a href="/" class="brand">toad.photos</a>
        <nav>
          <a href="/latest">Latest Photos</a>
          <?php
            foreach($this->index['category_counts'] as $name => $category)
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