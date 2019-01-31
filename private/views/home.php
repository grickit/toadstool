<div class="gallery">
<?php
  foreach(array_reverse($this->index['all']) as $i => $name)
  {
    $this->renderPartial('_thumbnail', ['name' => $name]);
  }
?>
</div>