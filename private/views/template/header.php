<?php
  $sitename = $this->config['sitename'];
  $description = $this->config['description'];
  $url = $this->config['preferred_base_url'];
  $image_url = $this->config['preview']['image']['url'];
  $image_type = $this->config['preview']['image']['mimetype'];
  $image_width = $this->config['preview']['image']['width'];
  $image_height = $this->config['preview']['image']['height'];
  $image_caption = $this->config['preview']['image']['caption'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $sitename; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $sitename; ?>" />
    <meta property="og:description" content="<?php echo $description; ?>" />
    <meta property="og:url" content="<?php echo $url; ?>" />

    <meta property="og:image" content="<?php echo $image_url; ?>" />
    <meta property="og:image:secure_url" content="<?php echo $image_url; ?>" />
    <meta property="og:image:type" content="<?php echo $image_type; ?>" />
    <meta property="og:image:width" content="<?php echo $image_width; ?>" />
    <meta property="og:image:height" content="<?php echo $image_height; ?>" />
    <meta property="og:image:alt" content="<?php echo $image_caption; ?>" />

    <link rel="stylesheet" type="text/css" media="screen" href="/resources/css/main.css?version=<?php echo filemtime("{$this->basePath}/public/resources/css/main.css"); ?>" />
  </head>
<body>
  <header class="header">
    <div class="container">
      <div class="content">
        <a href="/" class="brand"><?php echo $sitename; ?></a>
        <!-- <span class="description"><?php echo $description; ?></span> -->
        <nav>
          <a href="/">Home</a>
          <a href="/latest">All Photos</a>
        </nav>
      </div>
    </div>
  </header>
  <div id="main">
    <div class="container">