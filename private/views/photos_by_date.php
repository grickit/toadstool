<div class="gallery">
<?php
  // Take off the ones we've already rendered
  for($i = 1; $i <= $offset; $i++)
  {
    array_shift($photos);
  }

  $limit = 10;
  $renderPrevious = false;
  $renderNext = false;

  if($offset > 0)
  {
    $renderPrevious = true;
    if($offset > $limit)
      $previousOffset = ($offset - $limit);
    elseif($offset != 0)
      $previousOffset = 0;
  }

  if(count($photos) >= $limit)
  {
    $renderNext = true;
    $nextOffset = $offset+$limit;
  }

  $this->renderPartial('_page_controls', ['page' => 'latest', 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]);

  for($i = 0; $i < $limit; $i++)
  {
    reset($photos);
    $fancyDate = key($photos);
    $datePhotos = array_shift($photos);
    if(is_array($datePhotos))
    {
      echo "<h1>{$fancyDate}</h1>";
      echo '<div class="photos">';

      /*
      if($i === 0 && $offset > 0)
      {
        if($offset > $limit)
          $this->renderPartial('_thumbnail_button', ['page' => 'latest', 'offset' => ($offset - $limit), 'imageURL' => '/images/previous.png']);
        
        elseif($offset != 0)
          $this->renderPartial('_thumbnail_button', ['page' => 'latest', 'offset' => 0, 'imageURL' => '/images/previous.png']);
      }
      */

      foreach($datePhotos as $index => $name)
        $this->renderPartial('_thumbnail', ['name' => $name]);
      
      /*
      // Check if we're on the last section so that we can print the button card before we close the photos div
      if($i+1 == $limit && array_shift($photos) !== null)
        $this->renderPartial('_thumbnail_button', ['page' => 'latest', 'offset' => ($offset+$i), 'imageURL' => '/images/next.png']);]
      */
      
      echo '</div>';
    }
  }

  $this->renderPartial('_page_controls', ['page' => 'latest', 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]);

?>
</div>