<?php
/**
 * UrlShortener
 *
 * Copyright 2012-2022 Bob Ray
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
    /* @var $curl resource */
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


        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        $shortUrl = $this->$func($longUrl);

        return trim($shortUrl);
    }

    /* Shorten URLs in $text using $service */
    public function process (&$text, $service) {
        /* pad with spaces or URL at end of text is missed */
                $text = ' ' . $text . ' ';
        $pattern = '|([\\w]{1,20}+://(?=\S{1,2000}\s)[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+(?:[\'.,;:!?)][\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++)*)(\)?)|';

       $matches = array();
       $text =  preg_replace_callback($pattern,
           function($matches) use($service) {
               return trim($this->getShortUrl($matches[1], $service));
           },
           $text);
        return trim($text,' ');
    }
    /* is.gd */
    protected function isgd($longUrl)
    {
        /* No API key required (or available) */
        /* note: you can see statistics for a link by adding a hyphen to
         * the end of the URL and visiting it (scroll down to the bottom)
        */

        $serviceUrl = 'https://is.gd/create.php?format=simple&url=' . $longUrl;
        curl_setopt($this->curl, CURLOPT_URL,
            $serviceUrl);
        $response = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            return '[' . curl_error($this->curl) . ']';
        }

        return $response;
    }

    /* TinyUrl */
    protected function tinyurl($longUrl) {
        /* API key optional */

        if (!empty ($this->props['tinyurlApiKey']) && !empty($this->props['tinyurlUsername'])) {
            curl_setopt($this->curl, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $longUrl . '&login=' . $this->props['tinyurlUsername'] . '&apiKey='. $this->props['tinyurlApiKey'] . '&version=2.0,3');
        } else {
            curl_setopt($this->curl, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $longUrl);
        }
        return curl_exec($this->curl);
    }

    /* bit.ly */
    protected function bitly($longUrl) {
        /* requires OAuth token
         * Get it at http://bitly.com/ -> Settings -> API -> Generate Token
         * (after registering at http://bitly.com/a/sign_up)
         *
         * Use it for the value of the &bitlyApiKey property in your snippet
         */
        $access_token = $this->props['bitlyApiKey']; /* Really, an OAuth token */
        $json_payload = @json_encode(array(
            "group_guid" => "",
            "domain" => "bit.ly",
            "long_url" => $longUrl,
        ));

        @curl_setopt_array($this->curl, array(
            CURLOPT_URL => "https://api-ssl.bitly.com/v4/shorten",
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json_payload,
            CURLOPT_HTTPHEADER => array(
                "Host: api-ssl.bitly.com",
                "Authorization: Bearer " . $access_token . "",
                "Content-Type: application/json",
            ),
        ));

        $url = 'https://api-ssl.bitly.com/v4/shorten';
            curl_setopt($this->curl, CURLOPT_URL, $url);
            $response =  curl_exec($this->curl);
        if ($response === false) {
            return curl_error($this->curl);
        }
        $json_decoded = @json_decode($response);


        $http_code = @curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        # return results for further processing
        $response =  (array("json" => $json_decoded, "http" => $http_code));

        if (! isset ($response['json']->link) || $http_code !== 200) {
            $retval = curl_error($this->curl);
        } else {
            $retval = $response['json']->link;
        }
        return $retval;
    }
}
