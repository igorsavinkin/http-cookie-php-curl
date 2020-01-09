function get_web_page( $url ){
        $options = array( 
	    CURLOPT_RETURNTRANSFER => true, // to return web page
            CURLOPT_HEADER         => true, // to return headers in addition to content
            CURLOPT_FOLLOWLOCATION => true, // to follow redirects
            CURLOPT_ENCODING       => "",   // to handle all encodings
            CURLOPT_AUTOREFERER    => true, // to set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,  // set a timeout on connect
            CURLOPT_TIMEOUT        => 120,  // set a timeout on response
            CURLOPT_MAXREDIRS      => 10,   // to stop after 10 redirects
            CURLINFO_HEADER_OUT    => true, // no header out
            CURLOPT_SSL_VERIFYPEER => false,// to disable SSL Cert checks
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        );

        $handle = curl_init( $url );
        curl_setopt_array( $handle, $options );
 
    // additional for storing cookie 
        $tmpfname = dirname(__FILE__).'/cookie.txt';
        curl_setopt($handle, CURLOPT_COOKIEJAR, $tmpfname);
        curl_setopt($handle, CURLOPT_COOKIEFILE, $tmpfname);

        $raw_content = curl_exec( $handle );
        $err = curl_errno( $handle );
        $errmsg = curl_error( $handle );
        $header = curl_getinfo( $handle ); 
        curl_close( $handle );
 
        $header_content = substr($raw_content, 0, $header['header_size']);
        $body_content = trim(str_replace($header_content, '', $raw_content));
    
    // let's extract cookie from raw content for the viewing purpose         
        $cookiepattern = "#Set-Cookie:\\s+(?[^=]+=[^;]+)#m"; 
        preg_match_all($cookiepattern, $header_content, $matches); 
        $cookiesOut = implode("; ", $matches['cookie']);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['headers']  = $header_content;
        $header['content'] = $body_content;
        $header['cookies'] = $cookiesOut;
    return $header;
}

$header=get_web_page('http://www.example.com');
print_r($header);
