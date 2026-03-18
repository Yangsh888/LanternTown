<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

use Utils\Helper;

lt_send_security_headers();

$themeUrlFull = Helper::options()->themeUrl(null, Helper::options()->theme);
$themeUrlRelative = str_replace(
    '//usr',
    '/usr',
    str_replace(
        Helper::options()->siteUrl,
        Helper::options()->rootUrl . '/',
        $themeUrlFull
    )
);
$themeConfig = [
    'THEME_URL' => rtrim($themeUrlRelative, '/') . '/',
    'BLOG_TITLE' => lt_text($this->options->title ?? ''),
    'THEME_LOGO' => lt_text($this->options->logoUrl ?? ''),
    'TURN_PAGE_TYPE' => lt_text($this->options->turnPageType ?? 'page'),
    'THEME_MODE' => (int) lt_text($this->options->themeMode ?? 0)
];
$shortcutIcon = trim(lt_text($this->options->shortcutIcon ?? ''));
$fieldKeywords = lt_text($this->fields->keywords ?? '');
$fieldDesc = lt_text($this->fields->desc ?? '');
$currentPage = (int) $this->request->filter('int')->get('page', 1);
$logoUrl = lt_text($this->options->logoUrl ?? '');
$siteTitle = lt_text($this->options->title ?? '');

$navItems = [];
$pages = $this->widget('\Widget\Contents\Page\Rows');
while ($pages->next()) {
    $navItems[] = [
        'permalink' => lt_text($pages->permalink),
        'title' => lt_text($pages->title)
    ];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php if ($shortcutIcon !== '' && strlen($shortcutIcon) > 5): ?>
        <link rel="shortcut icon" href="<?php echo lt_esc_attr($shortcutIcon); ?>">
    <?php else: ?>
        <link rel="shortcut icon" href="<?php echo lt_esc_attr(rtrim(lt_text($this->options->siteUrl), '/') . '/favicon.ico'); ?>">
    <?php endif ?>
    <script type="text/javascript" src="<?php $this->options->themeUrl('libs/jquery/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('libs/headroom/headroom.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('assets/css/font.css'); ?>"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('assets/css/lantern.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('libs/swiper/swiper-bundle.min.css'); ?>"/>
    <script type="text/javascript" src="<?php $this->options->themeUrl('libs/swiper/swiper-bundle.min.js'); ?>"></script>
    <script>
        window.LANTERTOWN_CONFIG = <?php echo json_encode($themeConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR); ?>;
    </script>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('libs/prism/prism.min.css'); ?>"/>
    <script type="text/javascript" src="<?php $this->options->themeUrl('libs/prism/prism.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('libs/clipboard/clipboard.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('libs/fancybox/jquery.fancybox.min.css'); ?>"/>
    <?php if ($fieldKeywords !== '' || $fieldDesc !== '') : ?>
        <?php $this->header('keywords=' . rawurlencode($fieldKeywords) . '&description=' . rawurlencode($fieldDesc)); ?>
    <?php else : ?>
        <?php $this->header(); ?>
    <?php endif; ?>
    <title>
        <?php if ($currentPage > 1) echo '第 ' . $currentPage . ' 页 - '; ?>
        <?php $this->archiveTitle(
            [
                'category' => '分类 %s 下的文章',
                'search' => '包含关键字 %s 的文章',
                'tag' => '标签 %s 下的文章',
                'author' => '%s 发布的文章'
            ],
            '',
            ' - '
        ); ?>
        <?php $this->options->title(); ?>
    </title>
</head>
<body id="blog_container" class="bg0">
<div class="site-container">
    <nav class="navbar" id="navbar">
        <a class="navbar-logo" href="<?php $this->options->siteUrl(); ?>">
            <?php if ($logoUrl): ?>
                <img class="logo" src="<?php echo lt_esc_attr($logoUrl); ?>" alt="<?php echo lt_esc_attr($siteTitle); ?>"/>
            <?php else: ?>
                <span class="logo-text"><?php $this->options->title(); ?></span>
            <?php endif; ?>
        </a>
        <div class="navbar-menu">
            <?php foreach ($navItems as $item): ?>
                <a class="navbar-item hover-line" href="<?php echo lt_esc_attr($item['permalink']); ?>"><?php echo lt_esc_html($item['title']); ?></a>
            <?php endforeach; ?>
        </div>
        <div class="navbar-mobile-menu">
            <div id="navbar-mobile-menu-icon" class="navbar-mobile-menu-icon" onclick="showMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul id="mobile-menu-list">
                <?php foreach ($navItems as $item): ?>
                    <li>
                        <a href="<?php echo lt_esc_attr($item['permalink']); ?>"><?php echo lt_esc_html($item['title']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    <script type="text/javascript">
        (function() {
            var navNode = document.getElementById('navbar');
            if (navNode && typeof Headroom === 'function') {
                var header = new Headroom(navNode, {
                    tolerance: 0,
                    offset: 70,
                    classes: {
                        initial: 'animated',
                        pinned: 'slideDown',
                        unpinned: 'slideUp'
                    }
                });
                header.init();
            }
        })();
        var showMobileMenu = function () {
            var obj = document.getElementById("navbar-mobile-menu-icon");
            var ul = document.getElementById("mobile-menu-list");
            if (!obj || !ul) return;
            if (obj.classList.contains("open")) {
                obj.classList.remove("open");
                ul.style.display = "none";
            } else {
                obj.classList.add("open");
                ul.style.display = "block";
            }
        };
    </script>
