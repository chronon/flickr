<?php
class FlickrComponent extends Object {

    public function flickrRequest($data, $options = array()) {
        // set the posting url
        $postUrl = Configure::read('Flickr.posting_url');
        if (!$postUrl) {
            $postUrl = 'http://api.flickr.com/services/rest/';
        }

        // set the post data
        $defaults = Configure::read('Flickr.defaults');
        if (is_array($defaults)) {
            $data = $data + $defaults;
        }
        $postData = http_build_query($data);

        // make the request
        try {
            $response = $this->__doPost($postUrl, $postData, $options);

            // problem connecting or with the posting_url
            if ($response === false) {
                throw new Exception("No response from $postUrl");
            }

            // response received, make it an array or unserialize returns false
            $response = @unserialize($response);

            // a response was received, but could not be unserialized (ie: empty)
            if ($response === false) {
                throw new Exception('The response was not usable.');
            }

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