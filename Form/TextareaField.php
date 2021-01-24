<?php
namespace Diyyick\Lib\Form;

use Diyyick\Lib\Core\EntityForm;
/**
 * Description of TextareaField
 *
 * @author Sune
 */
class TextareaField extends BaseField
{
    public function __construct(EntityForm $entity, string $attribute)
    {
        parent::__construct($entity, $attribute);
    }

    public function renderInput(): string
    {
        // TODO: Implement renderInput() method.
        return sprintf('<textarea name="%s" class="form-control%s">%s</textarea>',
            $this->attribute,
            $this->entity->hasError($this->attribute) ? ' is-invalid' : '',
            $this->entity->{$this->attribute},
        );
    }
}

