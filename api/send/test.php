<?php
$title = 'Привет, мир'; // заголовок
$summary = 'Короткое описание данной статьи, например можно использовать функцию WordPress the_excerpt()'; // анонс поста
$url = 'http://truemisha.ru?p=1'; // ссылка на пост
$image_url = 'http://rudrastyh.com/wp-content/themes/truemisha/a/apple-touch-icon-144x144-precomposed.png' // URL изображения
?>
<a href="http://www.facebook.com/sharer.php?s=100&p[url]=<?php echo urlencode( $url ); ?>&p[title]=<?php echo $title ?>&p[summary]=<?php echo $summary ?>&p[images][0]=<?php echo $image_url ?>" onclick="window.open(this.href, this.title, 'toolbar=0, status=0, width=548, height=325'); return false" title="Поделиться ссылкой на Фейсбук" target="_parent">Поделиться</a>
