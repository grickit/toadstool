<?php
if($renderPrevious === true)
  echo "<a href=\"{$page}?offset={$previousOffset}\" class=\"control page previous\">Previous page</a>";
else
  echo '<span class="control page previous disabled">Previous page</span>';

if($renderNext === true)
  echo "<a href=\"{$page}?offset={$nextOffset}\" class=\"control page next\">Next page</a>";
else
  echo "<span class=\"control page next disabled\">Next page</span>";
?>
<div class="clear"></div>