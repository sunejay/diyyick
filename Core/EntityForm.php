<?php
namespace Diyyick\Lib\Core;

use Diyyick\Lib\PadamORM\DBContext;
use Diyyick\Lib\Form\FormField;
/**
 * Description of EntityForm
 *
 * @author Sune
 */
abstract class EntityForm 
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_ALPHANUM = 'alphanum';

    public array $errors = [];
    public $alphaNum = "/^[a-zA-Z0-9]*$/";
    public $validations = array();
    public $labels = array();

    public $errorMessages = array(
        self::RULE_REQUIRED => 'This field is required',
        self::RULE_EMAIL => 'This field must be a valid email address',
        self::RULE_MIN => 'Min length of this field must be {min}',
        self::RULE_MAX => 'Max length of this field must be {max}',
        self::RULE_MATCH => 'This field must be the same as {match}',
        self::RULE_UNIQUE => 'User with this {field} already exists',
        self::RULE_ALPHANUM => 'This field can only contain letters and numbers',
    );

    public function getLabels(string $attribute)
    {
        return $this->labels[$attribute] ?? ucwords($attribute);
    }

    public function loadData(array $data)
    {
        foreach ($data as $key => $value){
            if (property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    public function isValid(bool $csrfCheck=false)
    {
        $db = new DBContext();
        $res = new Response();
        if ($csrfCheck) {
            if (!isset($_POST['csrf_token']) || !FormField::checkToken($_POST['csrf_token'])) {
                $res->flash('danger', 'Token not set or invalid request!');
                $this->addError('csrf', 'Token not set or invalid request!');
            }
        }
        
        foreach ($this->validations as $attribute => $rules){
            $value = $this->{$attribute};
            foreach ($rules as $rule){
                $ruleName = $rule;
                if (!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $rule['match'] = $this->getLabels($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $entity = new $className();
                    $record = $db->findOne($entity, [$uniqueAttr => $value]);
                    if ($record){
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabels($attribute)]);
                    }
                }
                if ($ruleName === self::RULE_ALPHANUM && !preg_match($this->alphaNum, $value)){
                    $this->addErrorForRule($attribute, self::RULE_ALPHANUM);
                }
            }
        }
        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute, string $rule, $params=[])
    {
        $message = $this->errorMessages[$rule] ?? '';
        foreach ($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function hasError(string $attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError(string $attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }
}
