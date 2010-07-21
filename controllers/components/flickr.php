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

        // try to make the request
        try {

            $context = stream_context_create($options);
            $response = @file_get_contents($posting_url, false, $context);
            // problem connecting or with the posting_url
            if ($response === false) {
                throw new Exception("No response from $posting_url");
            }

            // response received, make it an array
            $response = unserialize($response);

            // check to see if Flickr returned an error
            if ($response['stat'] == 'fail') {
                throw new Exception(
                    'Flickr error code '.$response['code'].': '.$response['message']
                );
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $response;
    }

}