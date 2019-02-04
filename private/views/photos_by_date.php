<div class="gallery">
<?php

  $limit = 20;
  $renderPrevious = false;
  $renderNext = false;

  // Take off the ones we've already rendered
  for($i = 0; $i < $offset; $i++)
  {
    array_shift($photos);
  }

  for($i = 0; $i < $limit; $i++)
  {
    reset($photos);
    $fancyDate = key($photos);
    $datePhotos = array_shift($photos);
    if(count($datePhotos))
    {
      echo "<h1>{$fancyDate}</h1>";

      if($i === 0 && $offset > 0)
      {
        if($offset > $limit)
          $this->renderPartial('_thumbnail_button', ['page' => 'newest', 'offset' => ($offset - $limit), 'imageURL' => '/images/previous.png']);
        
        elseif($offset != 0)
          $this->renderPartial('_thumbnail_button', ['page' => 'newest', 'offset' => 0, 'imageURL' => '/images/previous.png']);
      }

      foreach($datePhotos as $index => $name)
        $this->renderPartial('_thumbnail', ['name' => $name]);
    }
  }

  if(array_shift($photos) !== null)
    $this->renderPartial('_thumbnail_button', ['page' => 'newest', 'offset' => ($offset+$i), 'imageURL' => '/images/next.png']);
?>
</div>