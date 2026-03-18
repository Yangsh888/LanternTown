<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$title = lt_text($this->title ?? '');
$isPost = $this->is('post');
$rewardUrls = array_slice(
    array_values(
        array_filter(
            lt_lines($this->options->rewardUrl ?? ''),
            static fn(string $url): bool => lt_safe_url($url) !== ''
        )
    ),
    0,
    LT_MAX_REWARD_IMAGES
);
$socialRaw = lt_lines($this->options->socialLink ?? '');
$socialList = [];
foreach ($socialRaw as $line) {
    $parts = explode(':', $line, 3);
    if (count($parts) !== 3) {
        continue;
    }
    $name = trim($parts[0]);
    $type = strtolower(trim($parts[1]));
    $link = trim($parts[2]);
    if ($name === '' || $type === '' || $link === '') {
        continue;
    }
    if (count($socialList) >= LT_MAX_SOCIAL_LINKS) {
        break;
    }
    if ($type === 'url') {
        $safe = lt_safe_url($link);
        if ($safe === '') {
            continue;
        }
        $socialList[] = ['name' => $name, 'type' => 'url', 'link' => $safe];
    } elseif ($type === 'qr') {
        $safe = lt_safe_url($link);
        if ($safe === '') {
            continue;
        }
        $socialList[] = ['name' => $name, 'type' => 'qr', 'link' => $safe];
    }
}
$viewsNum = (int) ($this->viewsNum ?? 0);
$showWriterIntro = lt_bool($this->options->writerIntro ?? false);
$showCopyright = lt_bool($this->options->showCopyright ?? false);
$selfIntro = trim(lt_text($this->options->selfIntro ?? ''));
$directoryStatus = lt_text($this->fields->directoryStatus ?? 'off');
$authorName = lt_text($this->author->name ?? $this->author->screenName ?? '');
$authorMail = lt_text($this->author->mail ?? '');
?>
<?php $this->need('public/header.php'); ?>
<div class="post">
    <div class="post-container">
        <div class="post-title"><?php $this->title(); ?></div>
        <div class="post-meta">
            <a href="<?php $this->author->permalink() ?>"><?php $this->author() ?></a>
            • <?php $this->date('Y年m月d日') ?>
            <?php if ($isPost): ?>
                • <?php $this->category(' , '); ?>
            <?php endif; ?>
            <?php if ($this->user->hasLogin()): ?>
                •
                <?php if ($this->is('page')): ?>
                    <a href="<?php echo lt_esc_attr($this->options->adminUrl . 'write-page.php?cid=' . (int) $this->cid); ?>" target="_blank" rel="noopener noreferrer">编辑</a>
                <?php else: ?>
                    <a href="<?php echo lt_esc_attr($this->options->adminUrl . 'write-post.php?cid=' . (int) $this->cid); ?>" target="_blank" rel="noopener noreferrer">编辑</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($viewsNum >= LT_MIN_VIEWS_DISPLAY): ?>
                • 阅读: <?php echo $viewsNum; ?>
            <?php endif; ?>
        </div>

        <div id="post-content" class="post-content line-numbers">
            <?php if ($isPost): ?>
                <?php
                ob_start();
                $this->content();
                $content = (string) ob_get_clean();
                $content = preg_replace_callback(
                    '/<img\b[^>]*src=["\']([^"\']+)["\'][^>]*>/i',
                    function (array $matches) use ($title): string {
                        $src = lt_safe_url((string) ($matches[1] ?? ''));
                        if ($src === '') {
                            return $matches[0];
                        }
                        return '<a href="' . lt_esc_attr($src) . '" class="fancybox" data-fancybox="gallery"><img src="' . lt_esc_attr($src) . '" alt="' . lt_esc_attr($title) . '" title="点击放大图片"></a>';
                    },
                    $content
                );
                echo $content;
                ?>
            <?php else: ?>
                <?php $this->content(); ?>
            <?php endif; ?>
        </div>

        <div class="post-tags"><?php $this->tags('', true, ''); ?></div>

        <?php if ($isPost): ?>
            <?php if ($showWriterIntro): ?>
                <div class="article-writer">
                    <img src="<?php parseAvatar($this->author->mail) ?>" alt="<?php echo lt_esc_attr($authorName); ?>">
                    <div class="right">
                        <div class="intro">
                            <span class="name"><a href="<?php $this->author->permalink() ?>"><?php $this->author() ?></a></span>
                            <span class="sign">
                                <?php if ($selfIntro !== ''): ?>
                                    <?php echo lt_esc_html($selfIntro); ?>
                                <?php else: ?>
                                    这个人很懒，什么也没有留下
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="social-link">
                            <a href="mailto:<?php echo lt_esc_attr($authorMail); ?>" class="iconfont"><?php echo lt_icon('mail'); ?></a>
                            <?php foreach ($socialList as $item): ?>
                                <?php if ($item['type'] === 'qr'): ?>
                                    <a href="javascript:;" class="iconfont" data-qr="<?php echo lt_esc_attr($item['link']); ?>" data-title="<?php echo lt_esc_attr($item['name']); ?>"><?php echo getIconByType($item['name']); ?></a>
                                <?php else: ?>
                                    <a href="<?php echo lt_esc_attr($item['link']); ?>" target="_blank" rel="noopener noreferrer" class="iconfont"><?php echo getIconByType($item['name']); ?></a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (!empty($rewardUrls)): ?>
                                <a href="javascript:;" data-reward="1" class="iconfont"><?php echo lt_icon('reward'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showCopyright): ?>
                <div class="post-copyright">
                    <div><div>版权属于: </div><div><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>的博客</div></div>
                    <div><div>本文链接: </div><div><?php $this->permalink(); ?></div></div>
                    <div><div>作品采用: </div><div>本作品采用<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" target="_blank" rel="noopener noreferrer">知识共享署名-非商业性使用-相同方式共享 4.0 国际许可协议</a>进行许可</div></div>
                </div>
            <?php endif; ?>

            <h2>推荐阅读</h2>
            <div class="post-prev-next">
                <?php thePrev($this, $this->options); ?>
                <?php theNext($this, $this->options); ?>
            </div>
        <?php endif; ?>

        <div><?php $this->need('public/comments.php'); ?></div>
    </div>

    <?php if ($directoryStatus === 'on'): ?>
        <div class="catalog-container"><div class="catalog-directory" id="catalog-directory"></div></div>
        <script>
            (function () {
                if (typeof jQuery !== 'function') return;
                var postContent = document.getElementById('post-content');
                var mount = document.getElementById('catalog-directory');
                if (!postContent || !mount) return;
                var titles = postContent.querySelectorAll('h1,h2,h3,h4,h5,h6');
                if (!titles.length) return;
                var ul = document.createElement('ul');
                titles.forEach(function (node, index) {
                    if (!node.id) node.id = 'menu-index-' + (index + 1);
                    var li = document.createElement('li');
                    var a = document.createElement('a');
                    a.href = '#' + node.id;
                    a.textContent = node.textContent || '';
                    li.appendChild(a);
                    ul.appendChild(li);
                });
                mount.appendChild(ul);
                $(window).on('scroll', function () {
                    var top = document.documentElement.scrollTop || document.body.scrollTop;
                    var active = 0;
                    titles.forEach(function (node, idx) {
                        if (top + 10 > $(node).offset().top) active = idx;
                    });
                    $('#catalog-directory a').removeClass('current').eq(active).addClass('current');
                    var inRange = top > 70 && top < (70 + $('#post-content').outerHeight(true));
                    $('#catalog-directory').css('opacity', inRange ? 1 : 0);
                });
            })();
        </script>
    <?php endif; ?>
</div>

<div id="background-layer">
    <div class="popup-container" id="social-pop">
        <div class="popup-header"><i class="iconfont" id="social-pop-close"><?php echo lt_icon('close'); ?></i></div>
        <div class="popup-body" id="popup-body"></div>
    </div>
</div>

<?php $this->need('public/footer.php'); ?>
<script>
    (function () {
        if (typeof jQuery !== 'function') return;
        var rewardUrls = <?php echo json_encode($rewardUrls, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        var bg = $('#background-layer');
        var body = $('#popup-body');
        var open = function (images) {
            body.html('');
            images.forEach(function (src) {
                body.append('<img src="' + $('<div/>').text(src).html() + '"/>');
            });
            bg.css('display', 'flex');
            bg.css('opacity', 1);
        };
        var close = function () {
            bg.css('display', 'none');
            bg.css('opacity', 0);
            body.html('');
        };
        $('#social-pop-close').on('click', close);
        $('[data-reward="1"]').on('click', function () {
            if (rewardUrls.length) open(rewardUrls);
        });
        $('[data-qr]').on('click', function () {
            var src = $(this).data('qr');
            if (src) open([src]);
        });
    })();
</script>
