<?php
namespace Diyyick\Lib\Form;

use Diyyick\Lib\Core\EntityForm;
/**
 * Description of BaseField
 *
 * @author Sune
 */
abstract class BaseField 
{
    //put your code here
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_EMAIL = 'email';
    public const TYPE_RADIO = 'radio';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_FILE = 'file';

    public EntityForm $entity;
    public string $attribute;
    public string $type;
    /**
     * Field constructor.
     * @param EntityForm $entity
     * @param string $attribute
     */
    public function __construct(EntityForm $entity, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        $this->entity = $entity;
        $this->attribute = $attribute;
    }

    abstract public function renderInput(): string;

    public function __toString()
    {
        $regularInput = sprintf('
                <div class="form-group">
                    <label>%s</label>
                        %s
                    <div class="invalid-feedback">%s</div>
                </div>
            ',
            $this->entity->getLabels($this->attribute),
            $this->renderInput(),
            $this->entity->getFirstError($this->attribute)
        );
        
        $specialInput = sprintf('
                <div class="form-group form-check">
                    <label class="form-check-label">%s %s</label>
                    <div class="invalid-feedback">%s</div>
                </div>
            ',
            $this->renderInput(),    
            $this->entity->getLabels($this->attribute),
            $this->entity->getFirstError($this->attribute)
        );
        
        if ($this->type == self::TYPE_CHECKBOX || $this->type == self::TYPE_RADIO) {
            return $specialInput;
        } else {
            return $regularInput;
        }
    }
}
