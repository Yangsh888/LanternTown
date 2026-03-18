<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<div class="site-container">
    <?php $this->need('public/header.php'); ?>
    <div class="post">
        <div class="post-container">
            <form class="search-form" action="<?php $this->options->siteUrl(); ?>" role="search">
                <input type="text" name="s" required="required" placeholder="输入搜索内容..." autofocus="autofocus" class="search-input">
                <button type="submit" class="search-btn iconfont"><?php echo lt_icon('search'); ?></button>
            </form>
            <div class="tags-container">
                <?php $this->widget('\Widget\Metas\Tag\Cloud', 'sort=count&ignoreZeroCount=1&desc=1&limit=50')->to($tags); ?>
                <div class="terms-tags">
                    <?php if ($tags->have()): ?>
                        <?php while ($tags->next()): ?>
                            <a href="<?php $tags->permalink(); ?>" class="terms-link"><?php $tags->name(); ?><span
                                        class="terms-count"><?php $tags->count(); ?></span></a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p> Nothing here ! </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $categoryRows = $this->widget('\Widget\Metas\Category\Rows');
            $categoryList = [];
            if ($categoryRows->have()) {
                while ($categoryRows->next()) {
                    $categoryList[] = [
                        'mid' => (int) $categoryRows->mid,
                        'name' => lt_text($categoryRows->name)
                    ];
                }
            }
            ?>
            <div class="category-container">
                <div id="category-list" class="category-list">
                    <div class="category-name hover-line active">全部类别</div>
                    <?php foreach ($categoryList as $cat): ?>
                        <div class="category-name hover-line"><?php echo lt_esc_html($cat['name']); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="archive-list" id="archive-list">
                <?php
                $buildArchiveHtml = static function ($archiveWidget, bool $active = false): string {
                    if (!$archiveWidget || !$archiveWidget->have()) {
                        return $active ? '<div class="archives active"></div>' : '<div class="archives"></div>';
                    }
                    $year = '';
                    $mon = '';
                    $opened = false;
                    $className = $active ? 'archives active' : 'archives';
                    $output = '<div class="' . $className . '">';
                    while ($archiveWidget->next()) {
                        $yearTmp = date('Y', $archiveWidget->created);
                        $monTmp = date('m', $archiveWidget->created);
                        if ($year !== $yearTmp || $mon !== $monTmp) {
                            if ($opened) {
                                $output .= '</div>';
                            }
                            $year = $yearTmp;
                            $mon = $monTmp;
                            $output .= '<div class="archives-month">' . $yearTmp . ' 年 ' . $monTmp . ' 月</div><div>';
                            $opened = true;
                        }
                        $dayTmp = date('d', $archiveWidget->created);
                        $link = lt_esc_attr(lt_text($archiveWidget->permalink));
                        $title = lt_esc_html(lt_text($archiveWidget->title));
                        $output .= '<div class="archive-post"><span class="archive-post-time">' . $monTmp . '-' . $dayTmp . '</span><span class="archive-post-title"><a class="archive-post-link" href="' . $link . '">' . $title . '</a></span></div>';
                    }
                    if ($opened) {
                        $output .= '</div>';
                    }
                    $output .= '</div>';
                    return $output;
                };
                try {
                    $stat = \Typecho\Widget::widget('\Widget\Stat');
                    $this->widget('\Widget\Contents\Post\Recent', 'pageSize=' . (int) $stat->publishedPostsNum)->to($archives);
                    echo $buildArchiveHtml($archives, true);
                } catch (\Throwable) {
                    echo '<div class="archives active"></div>';
                }
                ?>
                <?php foreach ($categoryList as $cat): ?>
                    <?php $catlist = $this->widget('\Widget\Archive@categorys_' . $cat['mid'], 'pageSize=10000&type=category', 'mid=' . $cat['mid']); ?>
                    <?php echo $buildArchiveHtml($catlist); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            (function() {
                if (typeof jQuery === 'function') {
                    jQuery('#category-list .category-name').click(function() {
                        jQuery(this).addClass('active').siblings().removeClass('active');
                        var a = jQuery(this).index();
                        jQuery('#archive-list .archives:eq(' + a + ')').addClass('active').siblings().removeClass('active');
                    });
                }
            })();
        </script>
    </div>
</div>
<?php $this->need('public/footer.php'); ?>
