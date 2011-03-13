# Flickr CakePHP Plugin

This is a [CakePHP][1] plugin consisting of one component and one helper that makes integrating [Flickr][2] images into your app **really easy**.

## Requirements:

* CakePHP 1.3 (might work with 1.2, not tested)
* PHP 5 (tested with PHP 5.3.2/Apache 2 OS X and Debian Linux)
* `allow_url_fopen` set to `on` in your php.ini (this uses PHP streams and not cURL)
* A Flickr account and a Flickr API key

## Configuration:

Global configuration is optional, but useful. You can put default values in app/config/bootstrap.php or app/config/core.php like this:

	Configure::write('Flickr.posting_url', 'http://api.flickr.com/services/rest/');
	Configure::write(
		'Flickr.defaults', array(
			'api_key' => '111122223333aaaabbbbccccdddd',
			'user_id' => '1234567@N66',
			'method' => 'flickr.photos.search',
			'format' => 'php_serial',
			'extras' => 'description, date_taken'
		)
	);

Pretty much any option available by the [Flickr API][3] can go here. All defaults can be overridden/replaced in your controller as needed.

## Usage:

First, you might want to take a look at this plugin's [demo project][4] code and [demo site][5].

### Controller:

Add the plugin's component and helper to your controller:

	public $components = array('Flickr.Flickr');
	public $helpers = array('Flickr.Flickr');

Using the defaults above (replacing the `api_key` and `user_id` values with your own), this would get 20 photos from `user_id`'s account with the tag "Public":

	public function somephotos() {
		$params = array(
			'tags' => 'Public',
			'per_page' => 20,
		);
		$photos = $this->Flickr->flickrRequest($params);
		$this->set('photos', $photos);
	}

The $photos variable would be an array, which you or the included Flickr helper can do something with. Again, check the [Flickr API][3] for all of the available options.

This example gets all of the Flickr sets for `user_id`, including everything needed to display the set thumbnail, title, and description:

	public function somesets() {
		$params = array('method' => 'flickr.photosets.getList');
		$sets = $this->Flickr->flickrRequest($params);
		$this->set('sets', $sets);
	}

### Helper:

Output the 20 photos tagged "Public" you set in the above somephotos() method:

	echo $this->Flickr->getPhotos(
		$photos,
		array('type' => 'div'),
		array('rel' => 'example1', 'title' => 'flickr_title')
	);

The resulting HTML would be:

	<div>
		<a href="http://farm5.static.flickr.com/4079/4750870838_52fc9c7167.jpg" rel="example1" title="Doi Mae Salong">
			<img src="http://farm5.static.flickr.com/4079/4750870838_52fc9c7167_s.jpg" alt="" />
		</a>
	</div>

The helper parameters are:

* @param array $photos required The response from Flickr as an array (what this plugin's component returns)

* @param array $formatAttribs optional Special key: type. This is what each thumbnail is wrapped in. The default is nothing. You could use things like 'type' => 'div' or 'type' => 'li' or 'type' => 'p', etc. You can also add additional HTML attributes, such as 'class' => 'thumb', 'id' => '`flickr_id`', etc. See the info on the special `flickr_` values below.

* @param array $linkAttribs optional Attributes for the `<a>` wrapping the thumbnail. Examples (from above 'rel' => 'example1', 'title' => 'flickr_title'.

* @param array $thumbAttribs optional Attributes for the <img> containing the thumbnail. Example: 'class' => 'little', 'alt' => '`flickr_title`'. There is also a special key named 'size', which determines the thumbnail size. Default is Flickr's size 's', which is 75x75. See [Flickr's size codes][6] for the list. For a 500px photo, do this: 'size' => 'n', for 240px: 'size' => 'm'. You can also use numbers (but they must be valid Flickr sizes): 'size' => 100.

* @param array $imgAttribs optional Size for the large image. Default: 'size' => 'n'. The only parameter you can set for the linked image is 'size'. See [Flickr's size codes][6] for the list.

* @param array $captionAttribs optional The special keys for caption are 'type', 'location', and 'caption'. The 'type' key should be things like div, p, li, etc. Example: 'type' => 'div'. The 'location' key can only have the values 'before' or 'after', which determines the placement of the caption in relation to the linked thumbnail image. The 'caption' key sets the contents of the caption. Examples: 'caption' => '`flickr_description`', 'caption' => '`flickr_datetaken`'.

**The special `flickr_` values**

These values act as variables and use the corresponding values returned from Flickr. If you use one for an 'id' anywhere, such as 'id' => `'flickr_id'`, in the $linkAttribs array, it will append type of a attribute to the id to make valid XHTML - so the above would produce something like: `id="link1234567"` in the HTML output. The special variables are:

* 'flickr_id',
* 'flickr_secret',
* 'flickr_title',
* 'flickr_datetaken',
* 'flickr_description'

 [1]: http://cakephp.org/
 [2]: http://flickr.com/
 [3]: http://www.flickr.com/services/api/
 [4]: http://github.com/chronon/flickr_demos
 [5]: http://chronon.com/flickr_demos/demos/
 [6]: http://www.flickr.com/services/api/misc.urls.html
