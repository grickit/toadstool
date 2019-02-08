<div class="gallery">
<h1><?php echo "Photos of {$category}"; ?></h1>
<div class="photos">
<?php

  $limit = 36;
  $renderNext = false;

  if($offset > 0)
  {
    if($offset > $limit)
      $this->renderPartial('_thumbnail_button', ['page' => "category/{$category}", 'offset' => ($offset - $limit), 'imageURL' => '/images/previous.png']);
    
    elseif($offset != 0)
      $this->renderPartial('_thumbnail_button', ['page' => "category/{$category}", 'offset' => 0, 'imageURL' => '/images/previous.png']);

    // We'll display one less image on this page due to the previous button
    $limit--;
  }

  if(isset($photos[$offset+$limit]))
  {
    $limit--;
    $renderNext = true;
  }


  for($i = $offset; ($i-$offset) < $limit; $i++)
  {
    if(isset($photos[$i]))
      $this->renderPartial('_thumbnail', ['name' => $photos[$i]]);
  }

  if($renderNext === true)
    $this->renderPartial('_thumbnail_button', ['page' => "category/{$category}", 'offset' => $i, 'imageURL' => '/images/next.png']);
?>
</div>
</div>