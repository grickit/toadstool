<div class="gallery">
<?php

  $limit = 35;
  $renderNext = false;

  if($offset > 0)
  {
    if($offset > $limit)
      $this->renderPartial('_thumbnail_button', ['offset' => ($offset - $limit), 'path' => '/images/previous.png']);
    
    elseif($offset != 0)
      $this->renderPartial('_thumbnail_button', ['offset' => 0, 'path' => '/images/previous.png']);

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
    $this->renderPartial('_thumbnail_button', ['offset' => $i, 'path' => '/images/next.png']);
?>
</div>