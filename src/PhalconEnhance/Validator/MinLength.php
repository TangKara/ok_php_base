<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/10/10
 * Time: 下午11:19
 */

namespace OK\PhalconEnhance\Validator;


use OK\PhalconEnhance\Constant\BuiltinKey;
use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class MinLength extends Validator
{
    /**
     * @param Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(Validation $validation, $attribute)
    {
        /** @var int $minLength */
        $minLength = $this->getOption(BuiltinKey::VALIDATOR_MIN_LENGTH);
        if (mb_strlen($validation->getValue($attribute)) >= $minLength) {
            return true;
        }

        $message = $this->getOption(BuiltinKey::VALIDATOR_MESSAGE);
        if (!$message) {
            $message = "Field $attribute must be at least $minLength characters long";
        }
        $validation->appendMessage(new Message($message, $attribute));
        return false;
    }
}