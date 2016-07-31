<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/1
 * Time: 下午5:37
 */

namespace OK\PhalconEnhance\Util;


use OK\PhalconEnhance\Constant\BuiltinKey;
use Phalcon\Forms\ElementInterface;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;

class FormUtil
{
    /**
     * @param Form $form
     * @param ElementInterface $element
     * @param boolean $nullable
     * @return Form
     */
    static public function autoInitField(Form $form, ElementInterface $element, $nullable = true)
    {
        if (!$nullable) {
            $element->addValidator(new PresenceOf([
                BuiltinKey::VALIDATOR_CANCEL => true
            ]));
        }

        if (method_exists($element, "initialize")) {
            $element->initialize();
        }
        $form->add($element);
        return $form;
    }

    /**
     * @param Form $form
     * @return string
     */
    static public function getValidationMessagesAsString(Form $form)
    {
        $msgGroup = $form->getMessages();
        $array = [];
        $offset = 0;
        $amount = $msgGroup->count();
        while ($offset < $amount) {
            $msgObj = $msgGroup->offsetGet($offset);
            $array[] .= $msgObj->getMessage();
            ++$offset;
        }
        return implode("\n", $array);
    }
}