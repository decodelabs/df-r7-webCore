<?php
use DecodeLabs\Tagged as Html;

?>
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
    <msapplication>
    <tile>
        <?php if ($this['hasImage']) { ?>
        <square70x70logo src="<?php echo $this->uri('touch-icon-70x70.png?cts'); ?>"/>
        <square150x150logo src="<?php echo $this->uri('touch-icon-150x150.png?cts'); ?>"/>
        <square310x310logo src="<?php echo $this->uri('touch-icon-310x310.png?cts'); ?>"/>
        <?php } ?>
        <TileColor><?php echo Html::esc($this['tileColor']); ?></TileColor>
    </tile>
    </msapplication>
</browserconfig>
