<?php
if($renderPrevious === true && $previousOffset !== null)
  echo "<a href=\"{$page}?offset={$previousOffset}\" class=\"button page previous\">Previous page</a>";
else
  echo '<span class="button page previous disabled">Previous page</span>';

if($renderNext === true && $nextOffset !== null)
  echo "<a href=\"{$page}?offset={$nextOffset}\" class=\"button page next\">Next page</a>";
else
  echo "<span class=\"button page next disabled\">Next page</span>";
?>
<div class="clear"></div>