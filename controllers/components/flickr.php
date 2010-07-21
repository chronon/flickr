<?php
class FlickrComponent extends Object {

    public function initialize($controller) {
		Configure::load('Flickr.settings');
	}

    public function flickrRequest($data, $options = array()) {
        // set the posting url
        $posting_url = Configure::read('Flickr.posting_url');

        // set the post data
        $defaults = Configure::read('Flickr.defaults');
        $postdata = http_build_query($data + $defaults);

        // set the http options
        $postDefaults = array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        );
        $options['http'] = $options + $postDefaults;

        // make the request
        try {
            $context = stream_context_create($options);
            $response = @file_get_contents($posting_url, false, $context);
            if ($response === false) {
                throw new Exception("Problem reading data from $posting_url");
            }
        } catch(Exception $e) {
            return $e->getMessage();
        }

        // make the response an array
        return unserialize($response);
    }

}