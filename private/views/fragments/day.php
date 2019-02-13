<?php
  if(!count($photos))
    return;
?>
<h2><?php echo $date; ?></h2>
<div class="photos">
<?php
  foreach($photos as $index => $name)
    $this->renderPartial('fragments/card_photo', ['name' => $name]);
?>
</div>