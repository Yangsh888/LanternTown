<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$turnPageType = lt_text($this->options->turnPageType ?? 'page');
?>
<?php if ($turnPageType === 'waterfall'): ?>
    <div class="loadmore" data-type="article">
        <?php $this->pageLink('加载更多', 'next'); ?>
    </div>
<?php else: ?>
    <?php $this->pageNav(
        lt_icon('left'),
        lt_icon('right'),
        1,
        '...',
        [
            'wrapTag' => 'ul',
            'wrapClass' => 'pagination-container',
            'itemTag' => 'li',
            'textTag' => 'a',
            'currentClass' => 'active',
            'prevClass' => 'iconfont prev',
            'nextClass' => 'iconfont next'
        ]
    ); ?>
<?php endif; ?>
