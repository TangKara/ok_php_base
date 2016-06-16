<?php
/**
 * Created by PhpStorm.
 * User: qinjx
 * Date: 15/6/28
 * Time: 上午11:30
 */

namespace OK\PhalconEnhance\Constant;


use OK\PhpEnhance\DataStructure\Enum;

class ValidatorKeyEnum extends Enum
{
    //can not be empty
    const REQUIRED          	= "REQUIRED";

    const MIN_VALUE         	= "MIN_VALUE";
    const MAX_VALUE         	= "MAX_VALUE";
    const MIN_LENGTH        	= "MIN_LENGTH";
    const MAX_LENGTH        	= "MAX_LENGTH";

    //value must match the regular expression pattern
    const MATCH_REGEX       	= "MATCH_REGEX";

    //alpha only, equals to [a-zA-Z]+
    const IS_ALPHA          	= "IS_ALPHA";

    //alpha and number
    const IS_ALPHANUMERIC   	= "IS_ALPHANUMERIC";

    //regex word, alpha, number and underscore (_) \w in regex, equals to \w+
    const IS_RE_WORD        	= "IS_RE_WORD";

    //regex word plus dash (-), equals to [\w-]+
    const IS_RE_WORD_DASH   	= "IS_RE_WORD_DASH";

    //regex word plus dash (-) and dot (.), equals to [\w-\.]+
    const IS_RE_WORD_DASH_DOT   = "IS_RE_WORD_DASH_DOT";

    //integer only, no fraction
    const IS_INT        	    = "IS_INT";

    //integer or fraction
    const IS_NUMBER         	= "IS_NUMBER";

    //0 or 1
    const IS_INT_BOOL       	= "IS_INT_BOOL";

    //integer array
    const IS_INT_ARRAY    	    = "IS_INT_ARRAY";

    //string array
    const IS_STRING_ARRAY   	= "IS_STRING_ARRAY";

    const IS_URL            	= "IS_URL";
    const IS_EMAIL          	= "IS_EMAIL";

    //value must be a part of your set, such as: ['male', 'female']
    const IN_SET            	= "IN_SET";

    //value must NOT be a part of your set, such as: ['fuck', 'damn']
    const NOT_IN_SET        	= "NOT_IN_SET";
}