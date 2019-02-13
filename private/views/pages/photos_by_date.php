<div class="gallery">
<?php
  // Take off the ones we've already rendered
  for($i = 1; $i <= $offset; $i++)
  {
    array_shift($photos);
  }

  $limit = 5;
  $renderPrevious = false;
  $renderNext = false;
  $nextOffset = null;
  $previousOffset = null;

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
?>
<div class="floating-controls">
<?php $this->renderPartial('fragments/pagination', ['page' => 'latest', 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]); ?>
</div>
<h1>Latest Photos</h1>
<?php
  for($i = 0; $i < $limit; $i++)
  {
    reset($photos);
    $this->renderPartial('fragments/day', ['date' => key($photos), 'photos' => array_shift($photos)]);
  }

  $this->renderPartial('fragments/pagination', ['page' => 'latest', 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]);
?>
</div>