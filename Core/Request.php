<?php
namespace Diyyick\Lib\Core;
/**
 * Description of Request
 *
 * @author Sune
 */
class Request 
{
    public $method;
    public $contentType;

    public function __construct() 
    {
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
    
    public function formData()
    {
        $body = [];
        if ($this->method() == 'get'){
            foreach ($_GET as $key => $value) {
                $key = htmlspecialchars(stripslashes(strip_tags(trim($key))));
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() == 'post'){
            foreach ($_POST as $key => $value) {
                $key = htmlspecialchars(stripslashes(strip_tags(trim($key))));
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }
    
    public function getParamId()
    {
        $ids = "/^[0-9]*$/";
        $params = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
        foreach ($params as $key => $value) {
            if (preg_match($ids, $value)) return $value;
        }
    }  
    
    public function getData(string $field)
    {
        if ($this->isPost() && !empty($_POST)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_POST[$field])) return htmlspecialchars(trim(stripslashes(strip_tags($_POST[$field]))));
        }
    }
    
    public function getJSON()
    {
        if ($this->method !== 'POST') return [];
        if (strcasecmp($this->contentType, 'application/json') !== 0) return [];
        // Receive the RAW post data
        return json_decode(trim(file_get_contents("php://input")));
    }    
}
