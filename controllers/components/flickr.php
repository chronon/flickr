<?php
class FlickrComponent extends Object {

    public function initialize($controller) {
		Configure::load('Flickr.settings');
	}

    public function flickrRequest($data, $options = array()) {
        // set the posting url
        $postUrl = Configure::read('Flickr.posting_url');

        // set the post data
        $defaults = Configure::read('Flickr.defaults');
        $postData = http_build_query($data + $defaults);

        // make the request
        try {
            $response = $this->__doPost($postUrl, $postData, $options);

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
        // valid response
        return $response;
    }

    private function __doPost($postUrl, $postData, $options = array()) {
        // set the http options
        $postDefaults = array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postData
        );
        // combine any other options with the defaults
        $postOptions['http'] = $options + $postDefaults;

        // post the request
        $context = stream_context_create($postOptions);
        $response = @file_get_contents($postUrl, false, $context);

        // problem connecting or bad url
        if ($response === false) {
            return false;
        }
        // got something
        return $response;
    }

}