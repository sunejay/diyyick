<?php
namespace Diyyick\Lib\Form;

/**
 * Description of SelectField
 *
 * @author Sune
 */
class SelectField extends BaseField
{
    public function __construct(EntityForm $entity, string $attribute)
    {
        parent::__construct($entity, $attribute);
    }
    
    public function choices($param) {
        $options = '';
        foreach ($param as $value) {
            $options .= "<option>$value</option>\n";
        }
        return $options;
    }

    public function renderInput(): string
    {
        // TODO: Implement renderInput() method.
        return sprintf('<select name="%s" class="form-control%s">%s</select>',
            $this->attribute,
            $this->entity->hasError($this->attribute) ? ' is-invalid' : '',
            $this->choices($this->entity->selectFieldOptions),
        );
    }
}
