<?php
declare(strict_types=1);

/**
 * 一款经典报纸复古风 Typecho 主题，适配 PHP 8 与 MySQL 8
 *
 * @package LanternTown
 * @author TypeRenew/Yangsh888
 * @version 1.0.1
 * @link https://github.com/Yangsh888/LanternTown
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<?php $this->need('public/header.php'); ?>
<?php $this->need('component/index.recommend.php'); ?>
<?php $this->need('component/index.list.php'); ?>
<?php $this->need('component/pagination.php'); ?>
<?php $this->need('public/footer.php'); ?>
