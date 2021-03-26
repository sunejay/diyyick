<?php
namespace Diyyick\Form;

use Diyyick\Core\Session;
use Diyyick\PadamORM\Entity;
/**
 * Description of Form
 *
 * @author Sune
 */
class FormField
{
    private function generateToken() {
        $token = base64_encode(openssl_random_pseudo_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }
    
    public static function checkToken(string $token) {
        return (Session::exist('csrf_token') && Session::get('csrf_token') == $token);
    }
    
    public function csrfToken()
    {
        return '<input type="hidden" name="csrf_token" id="csrf_token" value="'.self::generateToken().'">';
    }
    
    public function inputField(Entity $entity, string $attribute)
    {
        return new InputField($entity, $attribute);
    }

    public function textareaField(Entity $entity, string $attribute)
    {
        return new TextareaField($entity, $attribute);
    }
    
    public function selectField(Entity $entity, string $attribute)
    {
        return new SelectField($entity, $attribute);
    }
    
}
