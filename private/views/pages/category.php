<div class="gallery">
<?php
/**
 * Variables from render caller
 * @var int $offset
 * @var array $photos
 * @var string $category
 */
  $limit = 24;
  $renderPrevious = false;
  $renderNext = false;
  $nextOffset = null;
  $previousOffset = null;

  if($offset > 0)
  {
    $renderPrevious = true;
    if($offset > $limit)
      $previousOffset = ($offset - $limit);
  }

  if(count($photos) >= $offset+$limit)
  {
    $renderNext = true;
    $nextOffset = $offset+$limit;
  }
?>
<div class="floating-controls">
<?php $this->renderPartial('fragments/pagination', ['page' => "/category/{$category}", 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]); ?>
</div>
<h1><?php echo "Photos of {$category}"; ?></h1>
<div class="photos">
<?php
  for($i = $offset; ($i-$offset) < $limit; $i++)
  {
    if(isset($photos[$i]))
      $this->renderPartial('fragments/card_photo', ['name' => $photos[$i]]);
  }
?>
</div>
<?php
  $this->renderPartial('fragments/pagination', ['page' => "/category/{$category}", 'renderNext' => $renderNext, 'renderPrevious' => $renderPrevious, 'nextOffset' => $nextOffset, 'previousOffset' => $previousOffset]);
?>
</div>