<?php
namespace Diyyick\Lib\Core;

use Diyyick\Lib\PadamORM\DBContext;
/**
 * Description of Validation
 *
 * @author Sune
 */
class Validation 
{
    public $errors = [];
    
    /**
     * @param string $field form input value
     */
    public function getInput($field)
    {
        $req = new Request();
        if ($req->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            return htmlspecialchars(stripslashes(strip_tags(trim($_POST[$field]))));
        } 
    }

    public function getFile($field)
    {
        $req = new Request();
        if ($req->isPost()) {
            if (isset($_FILES[$field])) return $_FILES[$field];
        } 
    }
    
    /**
     * validations method
     * @param string $field form field
     * @param string $label form label
     * @param array $rules validation rules
     */
    public function validate($field, $label='', $rules=[])
    {
        $db = new DBContext();
        $inputValue = $this->getInput($field);
        
        if (!empty($rules)) {
            foreach ($rules as $key => $value) {
                // Check required rule in the array
                if ($key == "required" && $value == true) {
                    if(empty($inputValue)) {
                        if (!empty($label)) {
                            $this->addError($field, "$label is required");
                        } else {
                            $this->addError($field, "This field is required");
                        }
                    }
                }
                
                // Check unique rule in the array
                if ($key == "unique" && $value == true) {
                    if ($rules["entity"]) {
                        $className   = $rules["entity"];
                        $entity = new $className();
                        $record = $db->findOne($entity, [$field => $inputValue]);
                        if ($record) {
                            if (!empty($label)) {
                                $this->addError($field, "$label already exist");
                            } else {
                                $this->addError($field, "This field is unique and record already exist");
                            }
                        }
                    } else {
                        throw new \Exception("Entity must be provided");
                    }
                }
                
                // Check email rule in the array
                if ($key == "email" && $value == true) {
                    if (!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
                        if (!empty($label)) {
                            $this->addError($field, "$label must be valid email");
                        } else {
                            $this->addError($field, "This field must be valid email address");
                        }
                    }
                }
                
                // Check url rule in the array
                if ($key == "url" && $value == true) {
                    if (!filter_var($inputValue, FILTER_VALIDATE_URL)) {
                        if (!empty($label)) {
                            $this->addError($field, "$label must be valid url");
                        } else {
                            $this->addError($field, "This field must be valid url address");
                        }
                    }
                } 
                
                // Check match rule in the array
                if ($key == "match") {
                    $fieldName   = $value;
                    if ($inputValue !== $this->getInput($fieldName)) {
                        if (!empty($label)) {
                            $this->addError($field, "$label field must be the same as $fieldName");
                        } else {
                            $this->addError($field, "This field must be the same as $fieldName");
                        }
                    }
                } 
                
                // Check min_len rule in the array
                if($key == "minLen"){
                    $minValue = $value;
                    if(strlen($inputValue) < $minValue) {
                        if (!empty($label)) {
                            $this->addError($field, "$label length must be $minValue");
                        } else {
                            $this->addError($field, "Min length of this field must be $minValue");
                        }
                    }
                }
                
                // Check min_len rule in the array
                if($key == "maxLen"){
                    $maxValue = $value;
                    if(strlen($inputValue) > $maxValue) {
                        if (!empty($label)) {
                            $this->addError($field, "$label length must be $maxValue");
                        } else {
                            $this->addError($field, "Max length of this field must be $maxValue");
                        }
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
        if (empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    } 
    /**
     * validations method
     * @param string $field form field
     * @param string $message form error message
     */
    public function addError(string $field, string $message)
    {
        $this->errors[$field] = $message;
    }
}
