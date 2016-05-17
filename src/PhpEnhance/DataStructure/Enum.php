<?php
/**
 * @link    http://github.com/myclabs/php-enum
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace OK\PhpEnhance\DataStructure;

/**
 * Base Enum class
 *
 * Create an enum by implementing this class and adding class constants.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Daniel Costa <danielcosta@gmail.com>
 * @author Mirosław Filip <mirfilip@gmail.com>
 */
abstract class Enum
{
    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Store existing constants in a static cache per object.
     *
     * @var array
     */
    private static $cache = [];

    /**
     * Creates a new value of some type
     * No "throws" annotation, because we suggest use like this: new YourEnumSubClass(YourEnumSubClass::CONST_KEY)
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (!static::isValid($value)) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            throw new \UnexpectedValueException("Value [$value] is not part of the enum " . get_called_class());
        }

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the enum key (i.e. the constant name).
     *
     * @return mixed
     */
    public function getKey()
    {
        return self::search($this->value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Returns the names (keys) of all constants in the Enum class
     *
     * @return array
     */
    public static function keys()
    {
        return array_values(self::toArray());
    }

    /**
     * Returns the values of all constants in the Enum class
     *
     * @return array
     */
    public static function values()
    {
        return array_keys(self::toArray());
    }

    /**
     * Returns all possible values as an array
     *
     * @return array Constant name in key, constant value in value
     */
    public static function toArray()
    {
        $class = get_called_class();
        if (!array_key_exists($class, self::$cache)) {
            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getConstants() as $k => $v) {
                $constValue = (string)$v;
                self::$cache[$class][$constValue] = $k;
            }
        }

        return self::$cache[$class];
    }

    /**
     * Check if is valid enum value
     *
     * @param $value
     * @return bool
     */
    public static function isValid($value)
    {
        return array_key_exists($value, self::toArray());
    }

    /**
     * Check if is valid enum key
     *
     * @param $key
     *
     * @return bool
     */
    public static function isValidKey($key)
    {
        $class = get_called_class();
        return defined("$class::$key");
    }

    /**
     * Return key for value
     *
     * @param $value
     *
     * @return mixed
     */
    public static function search($value)
    {
        $class = get_called_class();
        return self::$cache[$class][$value];
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array $arguments
     *
     * @return static
     * @throws \BadMethodCallException
     * @throws \UnexpectedValueException
     */
    public static function __callStatic($name, $arguments)
    {
        if (defined("static::$name")) {
            return new static(constant("static::$name"));
        }

        throw new \BadMethodCallException("No static method or enum constant [$name] in class " . get_called_class());
    }
}
