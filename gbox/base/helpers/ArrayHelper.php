<?php
namespace Gbox\helpers;
class ArrayHelper 
{
    public static function map ($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element)
        {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null)
            {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    public static function getValue($array, $key, $default = null)
    {
        if (is_array($key))
        {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart)
            {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }
        if (is_array($array) && array_key_exists($key, $array))
        {
            return $array[$key];
        }
        if (($pos = strrpos($key, '.')) !== false)
        {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }
        if (is_object($array) && isset($array->$key))
        {
            return $array->$key;
        } elseif (is_array($array))
        {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }
    public static function remove(&$array, $key, $default = null)
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            $value = $array[$key];
            unset($array[$key]);
            return $value;
        }
        return $default;
    }
}
?>