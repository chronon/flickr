<?php
class FlickrHelper extends AppHelper {

    public $helpers = array('Html');

/**
 * Size options available from Flickr, 'n' in place of empty.
 *
 * @var array
 * @access protected
 * @link http://www.flickr.com/services/api/misc.urls.html
 */
    protected $_flickrSizes = array(
        '75' => 's',
        '100' => 't',
        '240' => 'm',
        '500' => 'n',
        '1024' => 'b'
    );

/**
 * Display one or more photos, wrapped in soemthing (<div>, <li>, etc.) with
 * various attributes (class, id, etc.) optionally set. Special values that can be set for
 * things like class or id are 'flickr_id', 'flickr_secret', and 'flickr_title'. If any are
 * used for an id, the attribute type is prepended for XHTML validation. Thumbnail and large
 * image sizes can be given as the Flickr code (s, t, m, b) or as numbers (75, 100, 240, 500, 1024).
 * For example, 'size' => 't' and 'size' => 100 are the same thing. The value of 'n' is the Flickr
 * default for large images (500px).
 *
 * @param array $photos required The response from Flickr as an array
 * @param array $formatAttribs optional Special key: type (see example). Default: 'type' => 'div'
 * @param array $linkAttribs optional Attributes for the <a> wrapping the thumbnail
 * @param array $thumbAttribs optional Attributes for the <img> containing the thumbnail
 * @param array $imgAttribs optional Size for the large image. Default: 'size' => 'n'
 * @return string The wrapped, linked images as HTML
 * @access public
 */
    public function getPhotos(
        $photos,
        $formatAttribs = array(),
        $linkAttribs = array(),
        $thumbAttribs = array(),
        $imgAttribs = array()
    ) {
        $attribs = array('format', 'link', 'thumb', 'img');

        // special fields that will use the values returned from Flickr
        $flickrFields = array('flickr_id', 'flickr_secret', 'flickr_title');

        // format attributes, default = div. could be things like li, p, etc.
        $defFormatAttribs = array('type' => 'div');
        $formatAttribs = Set::merge($defFormatAttribs, $formatAttribs);
        $formatType = $formatAttribs['type'];
        unset($formatAttribs['type']);

        // link attributes, no defaults. could be things like name, id, class, rel, etc.
        $defLinkAttribs = array('escape' => false);
        $linkAttribs = Set::merge($defLinkAttribs, $linkAttribs);

        // thumb attributes, default alt is empty, size is s: could be alt, class, id, etc.
        $defThumbAttribs = array(
            'alt' => '',
            'size' => 's'
        );
        $thumbAttribs = Set::merge($defThumbAttribs, $thumbAttribs);
        $thumbSize = $this->__setSize($thumbAttribs['size']);
        unset($thumbAttribs['size']);

        // (large) img attributes, default alt is empty. could be alt, class, id, etc.
        $defImgAttribs = array('size' => 'n');
        $imgAttribs = Set::merge($defImgAttribs, $imgAttribs);
        $imgSize = $this->__setSize($imgAttribs['size']);
        unset($imgAttribs['size']);

        // create an array of flickAttrib if any flickrFields are in use
        foreach ($attribs as $attrib) {
            ${'flickr'.$attrib} = array();
            foreach (${$attrib.'Attribs'} as $k => $v) {
                if (in_array($v, $flickrFields)) {
                    ${'flickr'.$attrib}[$k] = $v;
                }
            }
        }

        $result = '';
        foreach ($photos['photos']['photo'] as $p) {
            // use the dynamically generated values from Flickr if necessary
            foreach ($attribs as $attrib) {
                if (${'flickr'.$attrib}) {
                    foreach (${'flickr'.$attrib} as $k => $v) {
                        $v = str_replace('flickr_', '', $v);
                        if ($k == 'id') {
                            ${$attrib.'Attribs'}[$k] = $attrib.$p[$v];
                        } else {
                            ${$attrib.'Attribs'}[$k] = $p[$v];
                        }
                    }
                }
            }
            // build the base url to the image
            $base = 'http://farm'.$p['farm'].'.static.flickr.com/'.$p['server'];
            $base .= '/'.$p['id'].'_'.$p['secret'];
            $result .= $this->Html->tag(
                $formatType,
                $this->Html->link(
                    $this->Html->image(
                        $base.$thumbSize.'.jpg',
                        $thumbAttribs
                    ),
                    $base.$imgSize.'.jpg',
                    $linkAttribs
                ),
                $formatAttribs
            );
        }
        return $result;
    }

/**
 * Set the size for the thumbnail and large image
 *
 * @param string $size
 * @return string The size Flickr understands, such as _s, _t, etc.
 * @access private
 */
    private function __setSize($size) {
        // change a numeric size to the Flickr code if valid, other default to 's'
        if (is_numeric($size)) {
            if (array_key_exists($size, $this->_flickrSizes)) {
                $size = $this->_flickrSizes[$size];
            } else {
                $size = 's';
            }
        }
        // set the size to 's' if invalid value
        if (!in_array($size, $this->_flickrSizes)) {
            $size = 's';
        }
        if ($size == 'n') {
            $size = '';
        } else {
            $size = '_'.$size;
        }
        return $size;
    }

}