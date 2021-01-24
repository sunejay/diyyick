<?php
namespace Diyyick\Lib\Form;

use Diyyick\Lib\Core\EntityForm;
/**
 * Description of InputField
 *
 * @author Sune
 */
class InputField extends BaseField
{
    public string $type;

    /**
     * Field constructor.
     * @param EntityForm $model
     * @param string $attribute
     */
    public function __construct(EntityForm $entity, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($entity, $attribute);
    }
    
    public function renderInput(): string 
    {
        $regularInput = sprintf('<input type="%s" name="%s" value="%s" class="form-control%s">',
            $this->type,
            $this->attribute,
            $this->entity->{$this->attribute},
            $this->entity->hasError($this->attribute) ? ' is-invalid' : '',
        );
            
        $specialInput = sprintf('<input type="%s" name="%s" value="%s" class="form-check-input%s">',
            $this->type,
            $this->attribute,
            $this->entity->{$this->attribute},
            $this->entity->hasError($this->attribute) ? ' is-invalid' : '',
        );
        if ($this->type == self::TYPE_CHECKBOX || $this->type == self::TYPE_RADIO) {
            return $specialInput;
        } else {
            return $regularInput;
        }
    }
    
    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
    
    public function numberField()
    {
        $this->type = self::TYPE_NUMBER;
        return $this;
    }
    
    public function emailField()
    {
        $this->type = self::TYPE_EMAIL;
        return $this;
    }
    
    public function radioField()
    {
        $this->type = self::TYPE_RADIO;
        return $this;
    }
    
    public function checkboxField()
    {
        $this->type = self::TYPE_CHECKBOX;
        return $this;
    }
    
    public function fileField()
    {
        $this->type = self::TYPE_FILE;
        return $this;
    }
}
