<?php
/* File : Rest.php
*/
require_once('TwitterApi.php');
class Rest {

    //public $_content_type = "application/json";
    public $_request = array();

    public $settings = array();

    private $_code = 200;

    private $image;

    public function __construct(){

        $this->settings = array(
            'oauth_access_token' => $_GET['oauth_access_token'],
            'oauth_access_token_secret' => $_GET['oauth_access_token_secret'],
            'consumer_key' => $_GET['consumer_key'],
            'consumer_secret' => $_GET['consumer_secret'],
        );
        $this->inputs();
    }

    /*public function get_referer(){
        return $_SERVER['HTTP_REFERER'];
    }*/

    public function response($data,$status){
        $this->_code = ($status)?$status:200;
        $this->set_headers();
        $this->createImage($data, 10, (10*strlen($data)), 2*strlen($data));
        $this->saveImage();

        // Post it on Twitter
        $this->postTwitter();
    }

    protected function get_status_message(){
        $status = array( 
            200 => 'OK',
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'we didnot Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'
        );
        return ($status[$this->_code])?$status[$this->_code]:$status[500];
    }

    /*public function get_request_method(){
    return $_SERVER['REQUEST_METHOD'];
    }*/

    private function inputs(){
        /*switch($_SERVER['REQUEST_METHOD']){
        case "POST":
        $this->_request = $this->cleanInputs($_POST);
        break;
        case "GET":
        case "DELETE":
        $this->_request = $this->cleanInputs($_GET);
        break;
        case "PUT":
        parse_str(file_get_contents("php://input"),$this->_request);
        $this->_request = $this->cleanInputs($this->_request);
        break;
        default:
        $this->response('',406);
        break;
        }*/

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->_request = $this->cleanInputs($_GET);
        }
    }       

    protected function cleanInputs($data){
        $clean_input = array();
        if(is_array($data)) {
            foreach($data as $key => $value) {
                $clean_input[$key] = $this->cleanInputs($value);
            }
        }
        else {
            if(get_magic_quotes_gpc()){
                $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            $clean_input = trim($data);
        }
        return $clean_input;
    }       

    protected function set_headers(){
        header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
        header("Content-Type:application/json");
    }


    /**
    * Create image from text
    * @param string text to convert into image
    * @param int font size of text
    * @param int width of the image
    * @param int height of the image
    * @return boolean
    */
    protected function createImage($text, $fontSize = 20, $imgWidth = 400, $imgHeight = 80) {

        //get the text font path
        putenv('GDFONTPATH=' . realpath('.'));
        $font = 'Roboto-Bold';

        //create the image
        $this->image = imagecreatetruecolor($imgWidth, $imgHeight);

        //create some colors
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $grey = imagecolorallocate($this->image, 128, 128, 128);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        imagefilledrectangle($this->image, 0, 0, $imgWidth - 1, $imgHeight - 1, $white);

        //break lines
        $splitText = explode ( "\\n" , $text );
        $lines = count($splitText);

        foreach($splitText as $txt) {
            $textBox = imagettfbbox($fontSize,0,$font,$txt);
            $textWidth = abs(max($textBox[2], $textBox[4]));
            $textHeight = abs(max($textBox[5], $textBox[7]));
            $x = (imagesx($this->image) - $textWidth)/2;
            $y = ((imagesy($this->image) + $textHeight)/2)-($lines-2)*$textHeight;
            $lines = $lines-1;

            //add some shadow to the text
            imagettftext($this->image, $fontSize, 0, $x, $y, $grey, $font, $txt);

            //add the text
            imagettftext($this->image, $fontSize, 0, $x, $y, $black, $font, $txt);
        }
        return true;
    }

    /**
    * Save image as png format
    * @param string file name to save
    * @param string location to save image file
    * @return boolean true|false on success|failure
    */
    protected function saveImage($fileName = 'text-image', $location = ''){
        $fileName = $fileName . ".png";
        $fileName = !empty($location) ? $location . $fileName : $fileName;
        return imagepng($this->image, $fileName);
    }

    /**
     * Posts the image onto the twitter profile of the user.
     */
    protected function postTwitter() {
        $twitter = new TwitterApi($this->settings);


        $url = 'https://upload.twitter.com/1.1/media/upload.json';
        $requestMethod = 'POST';

        $file_name = 'text-image.png';

        $postfields = array(
        'media' => base64_encode(file_get_contents($file_name))
        );
        $response = $twitter->buildOauth($url, $requestMethod)
        ->setPostfields($postfields)
        ->performRequest();

        // get the media_id from the API return
        $media_id = json_decode($response)->media_id;

        // then send the Tweet along with the media ID
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $requestMethod = 'POST';

        $postfields = array(
        'status' => 'My amazing tweet',
        'media_ids' => $media_id,
        );

        $string = json_decode($twitter->setPostfields($postfields)
        ->buildOauth($url, $requestMethod)
        ->performRequest(),$assoc = TRUE);
        if($string["errors"][0]["message"] != "") {
            echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";
        }
        else echo "Updated";

        exit(1);
    }
} 

?>