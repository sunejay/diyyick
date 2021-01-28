<?php
namespace Diyyick\Lib\Core;

use Diyyick\Lib\Core\Request;
use Diyyick\Lib\PadamORM\DBContext;
/**
 * Description of Validation
 *
 * @author Sune
 */
class Validation extends EntityForm
{
    /**
     * @param string $field form input value
     */
    public function getInput($field)
    {
        $req = new Request();
        if($req->isPost()){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            return htmlspecialchars(stripslashes(strip_tags(trim($_POST[$field]))));
        } else if($req->isGet()){
            $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
            return htmlspecialchars(stripslashes(strip_tags(trim($_GET[$field]))));
        }
    }
    
    /**
     * validations method
     * @param string $field form field
     * @param string $label form label
     * @param array $rules validation rules
     */
    public function validate($field, $label, $rules=[])
    {
        $db = new DBContext();
        $inputValue = $this->getInput($field);
        
        if (!empty($rules)) {
            foreach ($rules as $key => $value) {
                // Check required rule in the array
                if ($key == "required" && $value == true) {
                    if(empty($inputValue)){
                        return $this->addError($field, "$label is required");
                    }
                }
                
                // Check unique rule in the array
                if ($key == "unique" && $value == true) {
                    if ($rules["entity"]) {
                        $className   = $rules["entity"];
                        $entity = new $className();
                        $record = $db->findOne($entity, [$field => $inputValue]);
                        if ($record){
                            $this->addError($field, "$label already exist");
                        }
                    } else {
                        throw new \Exception("Entity must be provided");
                    }
                }
                
                // Check email rule in the array
                if ($key == "email" && $value == true) {
                    if (!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, "$label must be valid email");
                    }
                }
                
                // Check url rule in the array
                if ($key == "url" && $value == true) {
                    if (!filter_var($inputValue, FILTER_VALIDATE_URL)) {
                        $this->addError($field, "$label must be valid url");
                    }
                } 
                
                // Check match rule in the array
                if ($key == "match") {
                    $fieldName   = $value;
                    if ($inputValue !== $this->getInput($fieldName)){
                        $this->addError($field, "This field must be the same as $fieldName");
                    }
                } 
                
                // Check min_len rule in the array
                if($key == "minLen"){
                    $minValue = $value;
                    if(strlen($inputValue) < $minValue){
                        return $this->addError($field, "$label is too short");
                    }
                }
                
                // Check min_len rule in the array
                if($key == "maxLen"){
                    $maxValue = $value;
                    if(strlen($inputValue) > $maxValue){
                        return $this->addError($field, "$label is too long");
                    }
                }
            }
        }
    }
    /**
     * Check if all fields is validated
     */
    public function isValidated()
    {
        if(empty($this->errors)){
            return true;
        } else {
            return false;
        }
    } 
}
