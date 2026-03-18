<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$recordNum = trim(lt_text($this->options->recordNum ?? ''));
$currentYear = date('Y');
?>
<footer>
    <div class="site-container footer">
        <div>
            &copy; <?php echo lt_esc_html($currentYear); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?> </a>的官方网站
        </div>
        <div>
            <?php if ($recordNum !== ''): ?>
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer"><?php echo lt_esc_html($recordNum); ?></a> •
            <?php endif; ?>Theme • <a href="https://github.com/Yangsh888/LanternTown" target="_blank" rel="noopener noreferrer">LanternTown</a>
        </div>
    </div>
    <script src="<?php $this->options->themeUrl('assets/js/lantern.config.js');?>"></script>
    <script src="<?php $this->options->themeUrl('libs/fancybox/jquery.fancybox.min.js');?>"></script>
    <?php $this->footer(); ?>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'function' || typeof jQuery.fn.fancybox !== 'function') {
        return;
    }
    jQuery('.fancybox').fancybox({
        loop: true,
        buttons: ['zoom', 'slideShow', 'thumbs', 'close'],
        animationEffect: 'zoom-in-out',
        transitionEffect: 'slide'
    });
});
</script>
