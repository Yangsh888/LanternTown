<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<div class="site-container">
    <?php $this->need('public/header.php'); ?>
    <div class="not-find-container">
        <div class="not-find">404</div>
        <div class="not-find-text">抱歉，页面未找到</div>
    </div>
</div>

<?php $this->need('public/footer.php'); ?>
