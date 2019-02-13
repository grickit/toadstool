<div class="gallery">
<h1>Categories</h1>
<?php
  foreach($this->index['category_counts'] as $name => $count)
  {
    if($name !== 'Uncategorized')
      $this->renderPartial('fragments/card_category', ['name' => $name, 'category' => $this->index['categories'][$name]]);
  }
  $this->renderPartial('fragments/card_category', ['name' => 'Uncategorized', 'category' => $this->index['categories']['Uncategorized']]);
?>

<h1>Latest Photoshoot</h1>
<?php
  $photos = $this->index['dates'];
  reset($photos);
  $this->renderPartial('fragments/day', ['date' => key($photos), 'photos' => array_shift($photos)]);
?>
<a class="button" href="/latest">See all photos</a>
</div>