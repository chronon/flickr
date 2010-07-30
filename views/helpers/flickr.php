<?php
class FlickrHelper extends AppHelper {

    public $helpers = array('Html');

    // http://www.flickr.com/services/api/misc.urls.html
    protected $_flickrSizes = array(
        '75' => 's',
        '100' => 't',
        '240' => 'm',
        '500' => 'n',
        '1024' => 'b'
    );
    protected $_flickrFields = array(
        'flickr_id',
        'flickr_secret',
        'flickr_title',
        'flickr_datetaken',
        'flickr_description'
    );
    protected $_formatDefaults = array('type' => false);
    protected $_linkDefaults = array('escape' => false);
    protected $_thumbDefaults = array('alt' => '', 'size' => 's');
    protected $_imgDefaults = array('size' => 'n');
    protected $_captionDefaults = array(
        'type' => false,
        'caption' => null,
        'location' => 'after'
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
 * @param array $formatAttribs optional Special key: type (see example).
 * @param array $linkAttribs optional Attributes for the <a> wrapping the thumbnail
 * @param array $thumbAttribs optional Attributes for the <img> containing the thumbnail
 * @param array $imgAttribs optional Size for the large image. Default: 'size' => 'n'
 * @param array $captionAttribs optional A caption, wrapped in 'type', before or after the thumbnail
 * @return string The wrapped, linked images as HTML
 * @access public
 */
    public function getPhotos(
        $photos,
        $formatAttribs = array(),
        $linkAttribs = array(),
        $thumbAttribs = array(),
        $imgAttribs = array(),
        $captionAttribs = array()
    ) {
        // check if $photos is an array, if not it's probably an error message from the component.
        if (!is_array($photos)) {
            return $photos;
        }
        $attribs = array('format', 'link', 'thumb', 'img', 'caption');

        // format attributes, could be things like li, p, etc.
        $formatAttribs = Set::merge($this->_formatDefaults, $formatAttribs);
        $formatType = $formatAttribs['type'];
        unset($formatAttribs['type']);

        // link attributes, could be things like name, id, class, rel, etc.
        $linkAttribs = Set::merge($this->_linkDefaults, $linkAttribs);

        // thumb attributes, could be alt, class, id, etc.
        $thumbAttribs = Set::merge($this->_thumbDefaults, $thumbAttribs);
        $thumbSize = $this->__setSize($thumbAttribs['size']);
        unset($thumbAttribs['size']);

        // (large) img attributes, only valid key actually is 'size'
        $imgAttribs = Set::merge($this->_imgDefaults, $imgAttribs);
        $imgSize = $this->__setSize($imgAttribs['size']);
        unset($imgAttribs['size']);

        // caption attributes, could be things like div, p, span, etc.
        $captionAttribs = Set::merge($this->_captionDefaults, $captionAttribs);
        $captionType = $captionAttribs['type'];
        $captionLocation = $captionAttribs['location'];
        $caption = $captionAttribs['caption'];
        $caption = str_replace('flickr_', '', $caption);
        unset($captionAttribs['type'], $captionAttribs['location'], $captionAttribs['caption']);

        // create an array of flickAttrib if any flickrFields are in use
        foreach ($attribs as $attrib) {
            ${'flickr'.$attrib} = array();
            foreach (${$attrib.'Attribs'} as $k => $v) {
                if (in_array($v, $this->_flickrFields)) {
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
                        } elseif ($k == 'title') {
                            ${$attrib.'Attribs'}[$k] = htmlentities($p[$v]);
                        } else {
                            ${$attrib.'Attribs'}[$k] = $p[$v];
                        }
                    }
                }
            }

            // open the wrapper
            if ($formatType) {
                $result .= $this->Html->tag($formatType, null, $formatAttribs);
            }
            // set the caption to a Flickr val or use the supplied val
            if ($captionType) {
                if (isset($p[$caption])) {
                    $cap = htmlentities($p[$caption]['_content']);
                }
            }
            // cpation before the thumbnail
            if ($captionType && $captionLocation == 'before') {
                $result .= $this->Html->tag(
                    $captionType,
                    $cap,
                    $captionAttribs
                );
            }
            // the thumbnail wrapped in a href
            $flickrThumb = $this->getPhoto($p, $thumbSize, 'img', $thumbAttribs);
            $flickrPhoto = $this->getPhoto($p, $imgSize, 'url');
            $result .= $this->Html->link(
                $flickrThumb,
                $flickrPhoto,
                $linkAttribs
            );
            // caption after the thumbnail
            if ($captionType && $captionLocation == 'after') {
                $result .= $this->Html->tag(
                    $captionType,
                    $cap,
                    $captionAttribs
                );
            }
            // close the wrapper
            if ($formatType) {
                $result .= "</$formatType>\n";
            }
        }
        return $result;
    }

/**
 * Build a Flickr formatted img or url
 *
 * @param array $flk required Keys must include farm, server, id, secret
 * @param string $size required The Flickr code for photo size
 * @param string $type required 'img' to return an <img> or 'url' for...an url!
 * @param string $attribs optional Attributes if returning an <img>
 * @return string A formated img or an url
 * @access public
 */
    public function getPhoto($flk, $size, $type, $attribs = array()) {
        $base = 'http://farm'.$flk['farm'].'.static.flickr.com/'.$flk['server'];
        $base .= '/'.$flk['id'].'_'.$flk['secret'];
        if ($type == 'img') {
            $photo = $this->Html->image(
                $base.$size.'.jpg',
                $attribs
            );
        } elseif ($type == 'url') {
            $photo = $base.$size.'.jpg';
        }
        return $photo;
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