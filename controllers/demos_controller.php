<?php
Class DemosController extends FlickrAppController {

    public $components = array('Flickr.Flickr');
    public $helpers = array('Flickr.Flickr');
    public $uses = array();

	public function galleriffic() {
	    $params = array(
            'tags' => 'Public',
            'per_page' => 20,
        );
		$photos = $this->Flickr->flickrRequest($params);
		$this->set('photos', $photos);
	}

	public function colorbox() {
	    $params = array(
            'tags' => 'Public',
            'per_page' => 20,
        );
		$photos = $this->Flickr->flickrRequest($params);
		$this->set('photos', $photos);
	}

}