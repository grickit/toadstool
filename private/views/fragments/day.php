<?php
  if(!count($photos))
    return;
?>
<h1><?php echo $date; ?></h1>
<div class="photos">
<?php
  foreach($photos as $index => $name)
    $this->renderPartial('fragments/card_photo', ['name' => $name]);
?>
</div>