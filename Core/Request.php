<?php
namespace Diyyick\Lib\Core;
/**
 * Description of Request
 *
 * @author Sune
 */
class Request 
{
    public array $params;
    public string $method;
    public string $contentType;

    public function __construct(array $params=[]) 
    {
        $this->params = $params;
        $this->method = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
    }

    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get' ?? $this->method() === 'GET';
    }

    public function isPost()
    {
        return $this->method() === 'post' ?? $this->method() === 'POST';
    }
    
    public function getData()
    {
        $body = [];
        if ($this->method() === 'get'){
            foreach ($_GET as $key => $value) {
                $key = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                $body[$key] = htmlspecialchars(stripslashes(strip_tags(trim($key))));
            }
        }
        if ($this->method() === 'post'){
            foreach ($_POST as $key => $value) {
                $key = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                $body[$key] = htmlspecialchars(stripslashes(strip_tags(trim($key))));
            }
        }
        return $body;
    }
    
    public function getParamId()
    {
        $ids = "/^[0-9]*$/";
        $uri = $_SERVER['REQUEST_URI'];
        $url = ltrim($uri, '/');
        $params = explode('/', $url);
        foreach ($params as $key => $value) {
            if (preg_match($ids, $value)) {
                return $value;
            }
        }
    }  
    
    public function cleanData(string $data)
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST[$data])){
            $data = htmlspecialchars(trim(stripslashes(strip_tags($_POST[$data]))));
            // $data = mysqli_real_escape_string($db, $data);
            return $data;
        }
    }
    
    public function input(string $field)
    {
        if($this->isPost() && !empty($_POST)){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            return htmlspecialchars(stripslashes(strip_tags(trim($_POST[$field]))));
        } else if($this->isGet() && !empty($_GET)){
            $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
            return htmlspecialchars(stripslashes(strip_tags(trim($_GET[$field]))));
        }
    }
    
    /*public function sanitize(string $value)
    {
	return htmlentities($value, ENT_QUOTES, "UTF-8");
    }*/
    
    public function getJSON()
    {
        if ($this->method !== 'POST') {
            return [];
        }
        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return [];
        }
        // Receive the RAW post data
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content);
        return $decoded;
    }    
}
