<?php
echo $this->Html->script(
    array(
        '/flickr/js/jquery-1.3.2.min',
        '/flickr/js/jquery.galleriffic',
        '/flickr/js/jquery.opacityrollover',
        '/flickr/js/demo.galleriffic'
    ),
    array('inline' => false)
);
echo $this->Html->css(
    array('/flickr/css/demos','/flickr/css/galleriffic-2'),
    null,
    array('inline' => false)
);
?>
<div id="page">
	<div id="container">
		<h1>Flickr CakePHP Plugin - <a href="http://www.twospy.com/galleriffic/">Galleriffic</a> Demo</h1>

        <div id="gallery" class="content">
        	<div id="controls" class="controls"></div>
        	<div class="slideshow-container">
        		<div id="loading" class="loader"></div>
        		<div id="slideshow" class="slideshow"></div>
        	</div>
        	<div id="caption" class="caption-container"></div>
        </div>

        <div id="thumbs" class="navigation">
        	<ul class="thumbs">
            <?php
            echo $this->Flickr->getPhotos(
                $photos,
                array('type' => 'li'),
                array('class' => 'thumb', 'name' => 'flickr_id', 'title' => 'flickr_datetaken'),
                array('alt' => 'flickr_title'),
                array(),
                array(
                    'type' => 'div',
                    'location' => 'after',
                    'class' => 'caption',
                    'caption' => 'flickr_title'
                )
            );
            ?>
            </ul>
        </div>

        <div style="clear: both;"></div>
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
    array('type' => 'li'),
    array('class' => 'thumb', 'name' => 'flickr_id', 'title' => 'flickr_datetaken'),
    array('alt' => 'flickr_title'),
    array(),
    array(
        'type' => 'div',
        'location' => 'after',
        'class' => 'caption',
        'caption' => 'flickr_title'
    )
);
                </pre>
    </div>
</div>