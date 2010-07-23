<?php
echo $this->Html->script(
    array(
        '/flickr/js/jquery-1.4.2.min',
        '/flickr/js/jquery.colorbox-min',
        '/flickr/js/demo.colorbox'
    ),
    array('inline' => false)
);
echo $this->Html->css(
    array('/flickr/css/demos', '/flickr/css/colorbox3'),
    null,
    array('inline' => false)
);
?>
<div id="page">
    <div id="cb-content">
        <h1>Flickr CakePHP Plugin: <a href="http://colorpowered.com/colorbox/">Colorbox</a> Demo</h1>
        <h3>Controller:</h3>
        <pre>
$params = array(
    'tags' => 'Public',
    'per_page' => 20,
);
$photos = $this->Flickr->flickrRequest($params);
        </pre>
        <h3>Helper:</h3>
        <pre>
echo $this->Flickr->getPhotos(
    $photos,
    array(),
    array('rel' => 'example1', 'title' => 'flickr_title')
);
        </pre>

    </div>
    <div id="cb-photos">
        <?php
        echo $this->Flickr->getPhotos(
            $photos,
            array(),
            array('rel' => 'example1', 'title' => 'flickr_title'),
            array(),
            array(),
            array()
        );
        ?>
    </div>
</div>