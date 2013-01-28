---
layout: post
title: "Use Zend_Service_Flickr to retrieve latitude and longitude co-ords for a picture"
date: 2011-08-08 17:47

---
This functionality isn't available to you in the Zend\_Service\_Flickr class (as of v 1.11) but you can add this by extending the class yourself and doing something like this:

    /**
     * add a method to Zend_Service_Flickr to retrieve lat and lng coords for a given photo id
     *
     * @author Ed van Beinum <e@edvanbeinum.com>
     * @throws Zend_Service_Exception
     *
     */
    class Evb_Service_Flickr extends Zend_Service_Flickr {
    
        /**
         * Return array of lng and lat coordinates for a given photo id
         *
         * @link http://www.flickr.com/services/api/flickr.photos.geo.getLocation.html
         * @author Ed van Beinum <e@edvanbeinum.com>
         * @throws Zend_Service_Exception
         * @param int $id
         * @return array
         */
        public function getImageLocation($id) {
            static $method = 'flickr.photos.geo.getLocation';
    
            if (empty($id)) {
                /**
                 * @see Zend_Service_Exception
                 */
                require_once 'Zend/Service/Exception.php';
                throw new Zend_Service_Exception('You must supply a photo ID');
            }
    
            $options = array(
                'api_key' => $this->apiKey, 
                'method' => $method,
                'photo_id' => $id
            );
    
            $restClient = $this->getRestClient();
            $restClient->getHttpClient()->resetParameters();
            $response = $restClient->restGet('/services/rest/', $options);
    
            $dom = new DOMDocument();
            $dom->loadXML($response->getBody());
            $xpath = new DOMXPath($dom);
            self::_checkErrors($dom);
            $retval = array();
            
            foreach($xpath->query('//rsp/photo/location') as $location) {
                $retval['lng'] = (string)  $location->getAttribute('longitude');
                $retval['lat'] = (string)  $location->getAttribute('latitude');
            }
            return $retval;
    
        }
    }

You may want to add some error handling if the picture doesn't have location data associated with it.