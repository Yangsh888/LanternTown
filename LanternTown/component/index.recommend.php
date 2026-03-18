<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<style>
    .recommend-silder .swiper {
        width: 100%;
        height: 400px;
    }
    .recommend-silder .swiper-slide {
        height: auto;
    }
    @media (max-width: 768px) {
        .recommend-silder .swiper {
            height: 500px;
            margin-bottom: 15px;
        }
    }
</style>

<?php if ($this->is('index')): ?>
    <?php
    $recommend = lt_text($this->options->cIdRecommend ?? '');
    $recommendCounts = array_values(
        array_filter(
            array_map('trim', explode("||", $recommend)),
            static fn(string $cid): bool => ctype_digit($cid)
        )
    );
    $number = count($recommendCounts);
    ?>
    <?php if ($number >= 2): ?>
        <section class="articles-grid">
            <div class="articles-row">
                <div class="recommend-silder">
                    <div class="silder-btn iconfont silder-btn-prev"><?php echo lt_icon('left'); ?></div>
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php for ($i = 0; $i < $number; $i++): ?>
                                <?php $this->widget('\Widget\Archive@recommend' . $i, 'pageSize=1&type=post', 'cid=' . (int) $recommendCounts[$i])->to($item); ?>
                                <div class="swiper-slide">
                                    <div data-href="<?php echo lt_esc_attr(lt_text($item->permalink)); ?>" onclick="toPost(this)" class="article-item single-article-item">
                                        <div class="item-container">
                                            <div class="item-content single-item-content">
                                                <div class="item-title font-bold">
                                                    <?php $item->title(); ?>
                                                </div>
                                                <div class="item-abstract">
                                                    <?php if ($item->fields->articleDesc): ?>
                                                        <?php $item->fields->articleDesc() ?>
                                                    <?php else: ?>
                                                        <?php $item->excerpt(80, "..."); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php $bg = lt_esc_attr(getThumb($item, $this->options)); ?>
                                            <div class="item-img single-item-img <?php if (lt_bool($this->options->greyImg ?? false)) echo "color-filter"; ?>">
                                                <div class="blog-background loaded" style="background-image: url('<?php echo $bg ?>')"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="silder-btn iconfont silder-btn-next"><?php echo lt_icon('right'); ?></div>
                </div>
            </div>
            <div class="hr_2px"></div>
        </section>
        <script type="text/javascript">
            (function() {
                var slideCount = <?php echo $number; ?>;
                var mySwiper = new Swiper('.recommend-silder .swiper', {
                    loop: slideCount >= 3,
                    loopAdditionalSlides: slideCount >= 3 ? 1 : 0,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    observer: true,
                    observeParents: true,
                    navigation: {
                        nextEl: '.silder-btn-next',
                        prevEl: '.silder-btn-prev'
                    }
                });
            })();
        </script>
    <?php elseif ($number === 1): ?>
        <?php $this->widget('\Widget\Archive@recommend0', 'pageSize=1&type=post', 'cid=' . (int) $recommendCounts[0])->to($item); ?>
        <section class="articles-grid">
            <div class="articles-row">
                <div class="recommend-silder" style="display: flex; justify-content: center;">
                    <div class="swiper" style="width: 100%; height: 400px;">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div data-href="<?php echo lt_esc_attr(lt_text($item->permalink)); ?>" onclick="toPost(this)" class="article-item single-article-item">
                                    <div class="item-container">
                                        <div class="item-content single-item-content">
                                            <div class="item-title font-bold">
                                                <?php $item->title(); ?>
                                            </div>
                                            <div class="item-abstract">
                                                <?php if ($item->fields->articleDesc): ?>
                                                    <?php $item->fields->articleDesc() ?>
                                                <?php else: ?>
                                                    <?php $item->excerpt(80, "..."); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php $bg = lt_esc_attr(getThumb($item, $this->options)); ?>
                                        <div class="item-img single-item-img <?php if (lt_bool($this->options->greyImg ?? false)) echo "color-filter"; ?>">
                                            <div class="blog-background loaded" style="background-image: url('<?php echo $bg ?>')"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr_2px"></div>
        </section>
    <?php endif; ?>
<?php endif; ?>
