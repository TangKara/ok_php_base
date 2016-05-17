<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/24
 * Time: 下午11:44
 */

namespace OK\PhalconEnhance\Validator;


use OK\PhalconEnhance\Constant\BuiltinKey;
use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class Integer extends Validator
{
    /**
     * @param Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);
        if (is_int($value) || ctype_digit($value)) {
            return true;
        }

        $message = $this->getOption(BuiltinKey::VALIDATOR_MESSAGE);
        if (!$message) {
            $message = "Field $attribute must be integer";
        }
        $validation->appendMessage(new Message($message, $attribute));
        return false;
    }
}