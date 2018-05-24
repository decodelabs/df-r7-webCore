<?php
$category = (array)($this['categories'] ?? 'statistics');
$category = $this->html->iList($category, null, null, ', ', ' and ');
?>
<div class="video-cookie-placeholder">
    <div class="video-message">
        <p class="strong">This video is hosted on a service that uses <?php echo $category; ?> tracking cookies.</p>
        <p>These cookies are currently disabled - to view this video, you will need to consent to and re-enable <?php echo $category; ?> cookies in your
        <?php echo $this->html->link('cookies/settings', 'Cookie Settings')->addClass('modal')->setIcon('settings'); ?>.</p>
    </div>
</div>
