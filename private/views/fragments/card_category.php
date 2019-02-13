<div class="card category">
<a href="/category/<?php echo $name;?>">
<h2><?php echo $name; ?></h2>
<?php

  for($i = 0; $i <= 3; $i++)
  {
    $photoName = array_pop($category);

    if($photoName)
      $this->renderPartial('fragments/card_category_photo', ['name' => $photoName]);
    
    else 
      echo '<div class="card photo"></div>';
  }
?>
</a>
</div>