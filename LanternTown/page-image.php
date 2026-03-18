<?php
declare(strict_types=1);

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$contentHtml = lt_text($this->content ?? '');
$images = [];
if ($contentHtml !== '') {
    if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $contentHtml, $srcMatches, PREG_SET_ORDER)) {
        foreach ($srcMatches as $match) {
            $src = lt_safe_url((string) ($match[1] ?? ''));
            if ($src === '') {
                continue;
            }

            $alt = '';
            if (preg_match('/\balt=["\']([^"\']*)["\']/i', $match[0], $altMatch)) {
                $alt = trim((string) ($altMatch[1] ?? ''));
            }

            $images[] = ['src' => $src, 'alt' => $alt];
        }
    }
}
?>
<?php $this->need('public/header.php'); ?>
<div class="site-container">
    <div id="image-list">
        <div class="image-item" style="visibility: hidden; position: absolute;"></div>
    </div>
</div>
<?php $this->need('public/footer.php'); ?>
<script type="text/javascript">
(function() {
    var imageData = <?php echo json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var perPageCount = 9;
    var colSumHeight = [];
    var nodeWidth = 0;
    var colNum = 1;
    var initialized = false;

    function initLayout() {
        var $firstItem = $('.image-item').first();
        if ($firstItem.length === 0) return false;
        
        $firstItem.css({ visibility: 'visible', position: 'relative' });
        nodeWidth = $firstItem.outerWidth(true) || 200;
        $firstItem.css({ visibility: 'hidden', position: 'absolute' });
        
        var containerWidth = $('#image-list').width();
        colNum = Math.max(1, Math.floor(containerWidth / nodeWidth) || 1);
        
        colSumHeight = [];
        for (var i = 0; i < colNum; i++) {
            colSumHeight[i] = 0;
        }
        return true;
    }

    function start() {
        if (!initialized) {
            if (!initLayout()) return;
            initialized = true;
        }
        
        getData(function (imgList) {
            if (!imgList || imgList.length === 0) return;
            
            $.each(imgList, function (idx, img) {
                var imgUrl = img.attr('data-src');
                if (!imgUrl) return;
                
                var tpl = '<a class="image-item fancybox" data-fancybox="gallery" href="' + $('<div/>').text(imgUrl).html() + '"></a>';
                var $node = $(tpl);
                $node.append(img);
                var desc = img.attr('alt');
                if (desc != null && desc !== '') {
                    desc = '<div class="desc"><p>' + $('<div/>').text(desc).html() + '</p></div>';
                    $node.append($(desc));
                }
                $node.find('img').on('load', function () {
                    $('#image-list').append($node);
                    waterFallPlace($node);
                }).on('error', function() {
                    $(this).remove();
                });
            });
        });
    }

    function getData(callback) {
        var data = [];
        var len = imageData.length < perPageCount ? imageData.length : perPageCount;
        for (var i = 0; i < len; i++) {
            var item = imageData.shift();
            if (!item || !item.src) {
                continue;
            }
            var src = item.src;
            var alt = item.alt || '';
            var $img = $('<img/>');
            $img.attr('data-src', src);
            $img.attr('alt', alt);
            $img.attr('src', src);
            data.push($img);
        }
        callback(data);
    }

    function waterFallPlace($node) {
        var idx = 0;
        var minSumHeight = colSumHeight[0];
        for (var i = 0; i < colSumHeight.length; i++) {
            if (colSumHeight[i] < minSumHeight) {
                idx = i;
                minSumHeight = colSumHeight[i];
            }
        }
        $node.css({
            left: nodeWidth * idx,
            top: minSumHeight,
            opacity: 1
        });
        colSumHeight[idx] = $node.outerHeight(true) + colSumHeight[idx];
        $('#image-list').height(Math.max.apply(null, colSumHeight));
    }

    $(document).ready(function() {
        if (imageData && imageData.length > 0) {
            start();
        }
    });

    $(window).on('scroll', function () {
        var scrollTop = $(this).scrollTop();
        var scrollHeight = $(document).height();
        var windowHeight = $(this).height();
        if (scrollTop + windowHeight >= scrollHeight - 5) {
            start();
        }
    });
})();
</script>
