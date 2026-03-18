<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$isIndex = $this->is('index');
$greyImg = lt_bool($this->options->greyImg ?? false);
?>
<section class="articles-grid">
    <section class="category-heading">
        <div class="iconfont category-icon"><?php echo lt_icon('grid'); ?></div>
        <?php if ($isIndex): ?>
            <span>最新文章</span>
        <?php else: ?>
            <?php $this->need('component/search.title.php'); ?>
        <?php endif; ?>
    </section>
    <?php if ($this->have()): ?>
        <div id="articleList">
            <?php
            $count = $this->length;
            $rows = (int) ceil($count / 3);
            ?>
            <?php while ($rows > 0): ?>
                <?php
                $rowNum = 3;
                if ($rows === 1) {
                    $rowNum = $count % 3;
                    if ($rowNum === 0) {
                        $rowNum = 3;
                    }
                }
                $rows--;
                ?>
                <section class="articles-row recent">
                    <?php for ($i = 1; $i <= $rowNum; $i++): ?>
                        <?php $this->next(); ?>
                        <?php
                        $permalink = lt_text($this->permalink);
                        $title = lt_text($this->title);
                        $thumb = getThumb($this, $this->options);
                        $articleDesc = lt_text($this->fields->articleDesc ?? '');
                        ?>
                        <div id="article-item-<?php $this->cid(); ?>" class="article-item" data-href="<?php echo lt_esc_attr($permalink); ?>" onclick="toPost(this)">
                            <div class="item-container">
                                <div class="item-content">
                                    <div class="item-title font-bold">
                                        <?php $this->title(); ?>
                                    </div>
                                    <div class="item-meta">
                                        <?php $this->date('Y年m月d日'); ?>
                                        <?php $this->category('  '); ?>
                                    </div>
                                    <div class="item-abstract">
                                        <?php if ($articleDesc !== ''): ?>
                                            <?php echo lt_esc_html($articleDesc); ?>
                                        <?php else: ?>
                                            <?php $this->excerpt(LT_DEFAULT_EXCERPT_LENGTH, "..."); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="item-img <?php if ($greyImg) echo "color-filter"; ?>">
                                    <div class="blog-background loaded" style="background-image: url('<?php echo lt_esc_attr($thumb); ?>')"></div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </section>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</section>
<script>
    function toPost(node) {
        if (!node || !node.dataset || !node.dataset.href) {
            return;
        }
        window.location.href = node.dataset.href;
    }
</script>
