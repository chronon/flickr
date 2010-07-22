<?php
class FlickrHelper extends AppHelper {

    public $helpers = array('Html');

    public function toList(
        $photos,
        $formatAttribs = array(),
        $linkAttribs = array(),
        $imgAttribs = array()
    ) {
        $attribs = array('format', 'link', 'img');

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

        // img attributes, default alt is empty. could be alt, class, id, etc.
        $defImgAttribs = array('alt' => '');
        $imgAttribs = Set::merge($defImgAttribs, $imgAttribs);

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
            $photo = 'http://farm'.$p['farm'].'.static.flickr.com/'.$p['server'];
            $photo .= '/'.$p['id'].'_'.$p['secret'];
            $result .= $this->Html->tag(
                $formatType,
                $this->Html->link(
                    $this->Html->image(
                        $photo.'_s.jpg',
                        $imgAttribs
                    ),
                    $photo.'.jpg',
                    $linkAttribs
                ),
                $formatAttribs
            );
        }
        return $result;
    }

}