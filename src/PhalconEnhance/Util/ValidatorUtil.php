<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/11/1
 * Time: 下午5:58
 */

namespace OK\PhalconEnhance\Util;

use OK\PhalconEnhance\Constant\BuiltinKey;
use OK\PhalconEnhance\Constant\ValidatorKeyEnum;
use OK\PhalconEnhance\Validator\IntArray;
use OK\PhalconEnhance\Validator\Integer;
use OK\PhalconEnhance\Validator\MaxLength;
use OK\PhalconEnhance\Validator\MaxValue;
use OK\PhalconEnhance\Validator\MinLength;
use OK\PhalconEnhance\Validator\MinValue;
use OK\PhalconEnhance\Validator\StringArray;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Validator\Alnum;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\ExclusionIn;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Url;

class ValidatorUtil
{
    /**
     * @param array $rule
     * @return Validator
     * <code>
     *
     * getValidatorObject([
     *  ValidatorKeyEnum::REQUIRED
     * ]);
     *
     * getValidatorObject([
     *  ValidatorKeyEnum::MAX_VALUE,
     *  1024
     * ]);
     *
     * getValidatorObject([
     *  ValidatorKeyEnum::IS_EMAIL,
     *  null,
     *  "please enter a valid mail address"
     * ]);
     * </code>
     */
    static public function getValidatorObject(array $rule)
    {
        $option = [BuiltinKey::VALIDATOR_CANCEL => true];
        switch (new ValidatorKeyEnum($rule[0])) {
            case ValidatorKeyEnum::REQUIRED:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new PresenceOf($option);
            case ValidatorKeyEnum::MIN_VALUE:
                $option[BuiltinKey::VALIDATOR_MIN_VALUE] = $rule[1];
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new MinValue($option);
            case ValidatorKeyEnum::MAX_VALUE:
                $option[BuiltinKey::VALIDATOR_MAX_VALUE] = $rule[1];
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new MaxValue($option);
            case ValidatorKeyEnum::MIN_LENGTH:
                $option[BuiltinKey::VALIDATOR_MIN_LENGTH] = $rule[1];
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new MinLength($option);
            case ValidatorKeyEnum::MAX_LENGTH:
                $option[BuiltinKey::VALIDATOR_MAX_LENGTH] = $rule[1];
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new MaxLength($option);
            case ValidatorKeyEnum::MATCH_REGEX:
                if ($rule[1][0] !== "/"){
                    $option[BuiltinKey::VALIDATOR_RE_PATTERN] = "/" . $rule[1] . "/";
                } else {
                    $option[BuiltinKey::VALIDATOR_RE_PATTERN] = $rule[1];
                }
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Regex($option);
            case ValidatorKeyEnum::IS_ALPHA:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Alpha($option);
            case ValidatorKeyEnum::IS_ALPHANUMERIC:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Alnum($option);
            case ValidatorKeyEnum::IS_RE_WORD:
                $option[BuiltinKey::VALIDATOR_RE_PATTERN] = "/\\w+/";
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Regex($option);
            case ValidatorKeyEnum::IS_RE_WORD_DASH:
                $option[BuiltinKey::VALIDATOR_RE_PATTERN] = "/[\\w-]+/";
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Regex($option);
            case ValidatorKeyEnum::IS_RE_WORD_DASH_DOT:
                $option[BuiltinKey::VALIDATOR_RE_PATTERN] = "/[\\w-\\.]+/";
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Regex($option);
            case ValidatorKeyEnum::IS_INT:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Integer($option);
            case ValidatorKeyEnum::IS_NUMBER:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Numericality($option);
            case ValidatorKeyEnum::IS_INT_BOOL:
                $option[BuiltinKey::VALIDATOR_SET_DOMAIN] = [0, 1];
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new InclusionIn($option);
            case ValidatorKeyEnum::IS_URL:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Url($option);
            case ValidatorKeyEnum::IS_INT_ARRAY:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new IntArray($option);
            case ValidatorKeyEnum::IS_STRING_ARRAY:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new StringArray($option);
            case ValidatorKeyEnum::IS_EMAIL:
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new Email($option);
            case ValidatorKeyEnum::IN_SET:
                $option[BuiltinKey::VALIDATOR_SET_DOMAIN] = explode(",", $rule[1]);
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new InclusionIn($option);
            case ValidatorKeyEnum::NOT_IN_SET:
                $option[BuiltinKey::VALIDATOR_SET_DOMAIN] = explode(",", $rule[1]);
                if (array_key_exists(2, $rule)) {
                    $option[BuiltinKey::VALIDATOR_MESSAGE] = $rule[2];
                }
                return new ExclusionIn($option);
        }
        return null;
    }
}