<?php
/**
 * UrlShortener
 *
 * Copyright 2012-2017 Bob Ray
 *
 * @author Bob Ray <https://bobsguides.com>
 * 
 * 
 *
 * UrlShortener is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * UrlShortener is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * UrlShortener; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package notify
 */
/**
 * MODX UrlShortener Class
 *
 *
  * The UrlShortener plugin for emailing resources to users
 *
 * The UrlShortener class contains all functions relating to UrlShortener's
 * operation.
 */


class UrlShortener
{
    /* @var $props array */
    protected $props;
    /* @var $curl object */
    protected $curl;

    function __construct(&$props)
    {
        $this->props =& $props;
    }
    public function init_curl() {
        $this->curl = curl_init();
    }
    public function close_curl() {
        curl_close($this->curl);
    }

    public function getShortUrl($longUrl, $service)
    {
        /* remove the period */
        $service = str_replace('.', '', $service);

        $func = strtolower($service);


        //curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        /* note: Goo.gl chokes if you urlencode the url */
        $shortUrl = $this->$func($longUrl);

        return trim($shortUrl);
    }

    /* Shorten URLs in $text using $service */
    public function process (&$text, $service) {
        /* pad with spaces or URL at end of text is missed */
                $text = ' ' . $text . ' ';
        $pattern = '|([\\w]{1,20}+://(?=\S{1,2000}\s)[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+(?:[\'.,;:!?)][\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++)*)(\)?)|';

        $matches = array();
        preg_match_all($pattern,$text,$matches);

        foreach ($matches[0] as $match) {
            $text = str_replace($match, $this->getShortUrl($match,$service),$text);
        }
        reset($matches);
        return trim($text,' ');
    }
    /* is.gd */
    protected function isgd($longUrl)
    {
        /* No API key required (or available) */
        /* note: you can see statistics for a link by adding a hyphen to
         * the end of the URL and visiting it (scroll down to the bottom)
        */
        curl_setopt($this->curl, CURLOPT_URL, 'http://is.gd/api.php?longurl=' . $longUrl);
        //$shortUrl = curl_exec($this->curl);
        return curl_exec($this->curl);
    }

    protected function toly($longUrl) {
        /* No API key required (or available at this time) */
        curl_setopt($this->curl, CURLOPT_URL, "http://to.ly/api.php?longurl=" . $longUrl);
        return curl_exec($this->curl);
    }

    /* TinyUrl */
    protected function tinyurl($longUrl) {
        /* No API key required */

        if (!empty ($this->props['tinyurlApiKey']) && !empty($this->props['tinyurlUsername'])) {
            curl_setopt($this->curl, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $longUrl . '&login=' . $this->props['tinyurlUsername'] . '&apiKey='. $this->props['tinyurlApiKey'] . '&version=2.0,3');
        } else {
            curl_setopt($this->curl, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $longUrl . '&customUrl='. 'zzz');
        }
        return curl_exec($this->curl);
    }

    /* goo.gl (google) */
    protected function googl($longUrl) {
        /* May require API key.
         * Get it at: http://code.google.com/apis/console/
         * (while logged in to Google) */

        $postData = array('longUrl' => $longUrl);
        if (!empty($this->props['googleApiKey'])) {
            $postData['key'] = $this->props['googleApiKey'];
        }
        $jsonData = json_encode($postData);
        $options = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_URL => 'https://www.googleapis.com/urlshortener/v1/url',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
        );
        curl_setopt_array($this->curl, $options);

        $response = curl_exec($this->curl);

        //change the response json string to object
        $json = json_decode($response);
        return $json->id;
    }

    /* su.pr (stumbleupon) */
    protected function supr($longUrl) {
        /* api key and username are optional */
        $supr_api = "http://su.pr/api/simpleshorten";
        if (empty ($this->props['suprUsername']) || empty($this->props['suprApiKey'])) {
            $supr_args = array('url' => $longUrl);
        } else {
            $supr_args = array(
                'url' => $longUrl,
                'login' => $this->props['suprUsername'],
                'apiKey' => $this->props['suprApiKey']
            );
        }

        $url = $supr_api . '?' . http_build_query($supr_args);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        return curl_exec($this->curl);
    }
    /* bit.ly */
    protected function bitly($longUrl) {
        /* requires API key
         * Get it at http://bitly.com/a/sign_in?rd=/a/your_api_key
         * (after registering at http://bitly.com/a/sign_up)
         */

        $bitly_args = array (
            'login' => $this->props['bitlyUsername'],
            'apiKey' => $this->props['bitlyApiKey'],
            'uri' => $longUrl,
            'format' => 'txt',
        );
        $url = 'http://api.bit.ly/v3/shorten?' . http_build_query($bitly_args, "", '&');

            curl_setopt($this->curl, CURLOPT_URL, $url);
            return curl_exec($this->curl);
    }

}
