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

class StringArray extends Validator
{
    /**
     * @param Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(Validation $validation, $attribute)
    {
        $isStringArray = true;
        /** @var array $array */
        $array = $validation->getValue($attribute);
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $element) {
                if (!is_string($element)) {
                    $isStringArray = false;
                }
            }
        } else {
            $isStringArray = false;
        }

        if ($isStringArray) {
            return true;
        }

        $message = $this->getOption(BuiltinKey::VALIDATOR_MESSAGE);
        if (!$message) {
            $message = "Field $attribute must be an string array";
        }
        $validation->appendMessage(new Message($message, $attribute));
        return false;
    }
}