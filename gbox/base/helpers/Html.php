<?php
namespace Gbox\helpers;
class Html
{

	public static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];

	public static function encode ($content, $doubleEncode = true)
	{
		return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
	}
	public static function decode ($content)
	{
		return htmlspecialchars_decode($content, ENT_QUOTES);
	}

	public static function tag ($name, $content = '', $options = [])
    {
        $html = '<' . $name . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
    }

    public static function renderTagAttributes ($attrs)
    {
    	$html = '';
    	foreach ($attrs as $name => $value)
    	{
			if (is_bool($value))
			{
				if ($value)
				{
					$html .= " $name";
				}
			}
			elseif (is_array($value))
			{
				if ($name === 'data')
				{
					foreach ($value as $n => $v)
					{
						if (is_array($v))
						{
							$html .= " $name-$n='" . Json::encode($v) . "'";
						}
						else
						{
							$html .= " $name-$n=\"" . Html::encode($v) . '"';
						}
					}
				}
				elseif ($name === 'class')
				{
					if (empty($value))
					{
						continue;
					}
					$html .= " $name=\"" . Html::encode(implode(' ', $value)) . '"';
				}
				elseif ($name === 'style')
				{
					if (empty($value))
					{
						continue;
					}
					$html .= " $name=\"" . Html::encode(static::cssStyleFromArray($value)) . '"';
				}
				else
				{
					$html .= " $name='" . Html::encode($value) . "'";
				}
			}
			elseif ($value !== null)
			{
				$html .= " $name=\"" . static::encode($value) . '"';
			}
    	}
    	return $html;
    }




	public static function link ($url, $rel = 'stylesheet', $type = 'text/css')
	{
		return '<link href="' . $url . '" rel="' . $rel . '" type="' . $type . '" />' . "\n";
	}
	public static function script ($url, $type = 'text/javascript', $async = false)
	{
		if (empty($type)) $type = 'text/javascript';
		return '<script src="' . $url . '" type="' . $type . '"' . ($async ? ' async' : '') . '></script>' . "\n";
	}
	public static function meta ($name, $content = '', $http = null)
	{
		return '<meta ' . 
			($name ? 'name="' . $name . '" ' : '') . 
			($content ? 'content="' . $content . '" ' : '') . 
			($http ? 'http-equiv="' . $http . '# ' : '') . 
			'/>' . "\n";
	}
	public static function title ($title)
	{
		return '<title>' . $title . '</title>' . "\n";
	}



	public static function submitButton ($text, $options = [])
	{
		$options['type'] = 'submit';
		return self::button($text, $options);
	}
	public static function button ($content = 'Button', $options = [])
	{
		if (!isset($options['type']))
		{
			$options['type'] = 'button';
		}
		return static::tag('button', $content, $options);
	}
	public static function a ($content, $url = '#', $options = [])
	{
		$options['href'] = $url;
		return static::tag('a', $content, $options);
	}
	public static function input ($type, $name = null, $value = null, $options = [])
	{
		if (!isset($options['type']))
		{
			$options['type'] = $type;
		}
		$options['name'] = $name;
		$options['value'] = $value === null ? null : (string) $value;
		return static::tag('input', '', $options);
	}
	public static function textarea ($name, $value = '', $options = [])
	{
		$options['name'] = $name;
		return static::tag('textarea', Html::encode($value), $options);
	}
	public static function dropDownList ($name, $selection = null, $items = [], $options = [])
	{
		// if (!empty($options['multiple'])) {
		// 	return static::listBox($name, $selection, $items, $options);
		// }
		$options['name'] = $name;
		unset($options['unselect']);
		$selectOptions = static::renderSelectOptions($selection, $items, $options);

		// $selectOptions = [];
		// foreach ($items as $value => $name)
		// {
		// 	$selectOptions[] = static::tag('option', $name, ['value' => empty($value) ? $name : $value]);
		// }
		// $selectOptions = implode("\n", $selectOptions);

		return static::tag('select', "\n" . $selectOptions . "\n", $options);
	}
	public static function listBox ($name, $selection = null, $items = [], $options = [])
	{
		if (!array_key_exists('size', $options))
		{
			$options['size'] = 4;
		}
		if (!empty($options['multiple']) && !empty($name) && substr_compare($name, '[]', -2, 2))
		{
			$name .= '[]';
		}
		$options['name'] = $name;
		if (isset($options['unselect']))
		{
			// add a hidden field so that if the list box has no option being selected, it still submits a value
			if (!empty($name) && substr_compare($name, '[]', -2, 2) === 0)
			{
			$name = substr($name, 0, -2);
			}
			$hidden = static::input('hidden', $name, $options['unselect']);
			unset($options['unselect']);
		}
		else
		{
			$hidden = '';
		}
		$selectOptions = static::renderSelectOptions($selection, $items, $options);
		return $hidden . static::tag('select', "\n" . $selectOptions . "\n", $options);
	}
	public static function label ($content, $for = null, $options = [])
	{
		$options['for'] = $for;
		return static::tag('label', $content, $options);
	}
	public static function error ($model, $attribute, $options = [])
    {
        $error = $model->getFirstError($attribute);
        $tag = isset($options['tag']) ? $options['tag'] : 'div';
        unset($options['tag']);
        return Html::tag($tag, $error, $options);
    }
    public static function hint ($model, $attribute, $options = [])
    {
        // $attribute = static::getAttributeName($attribute);
        $hint = isset($options['hint']) ? $options['hint'] : $model->getAttrHint($attribute);
        if (empty($hint)) {
            return '';
        }
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        unset($options['hint']);
        return static::tag($tag, $hint, $options);
    }
    public static function renderSelectOptions($selection, $items, &$tagOptions = [])
    {
        $lines = [];
        $encodeSpaces = ArrayHelper::remove($tagOptions, 'encodeSpaces', false);
        $encode = ArrayHelper::remove($tagOptions, 'encode', true);
        if (isset($tagOptions['prompt'])) {
            $prompt = $encode ? static::encode($tagOptions['prompt']) : $tagOptions['prompt'];
            if ($encodeSpaces) {
                $prompt = str_replace(' ', '&nbsp;', $prompt);
            }
            $lines[] = static::tag('option', $prompt, ['value' => '']);
        }

        $options = isset($tagOptions['options']) ? $tagOptions['options'] : [];
        $groups = isset($tagOptions['groups']) ? $tagOptions['groups'] : [];
        unset($tagOptions['prompt'], $tagOptions['options'], $tagOptions['groups']);
        $options['encodeSpaces'] = ArrayHelper::getValue($options, 'encodeSpaces', $encodeSpaces);
        $options['encode'] = ArrayHelper::getValue($options, 'encode', $encode);

        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $groupAttrs = isset($groups[$key]) ? $groups[$key] : [];
                if (!isset($groupAttrs['label'])) {
                    $groupAttrs['label'] = $key;
                }
                $attrs = ['options' => $options, 'groups' => $groups, 'encodeSpaces' => $encodeSpaces, 'encode' => $encode];
                $content = static::renderSelectOptions($selection, $value, $attrs);
                $lines[] = static::tag('optgroup', "\n" . $content . "\n", $groupAttrs);
            } else {
                $attrs = isset($options[$key]) ? $options[$key] : [];
                $attrs['value'] = (string) $key;
                $attrs['selected'] = $selection !== null &&
                        (!is_array($selection) && !strcmp($key, $selection)
                        || is_array($selection) && in_array($key, $selection));
                $text = $encode ? static::encode($value) : $value;
                if ($encodeSpaces) {
                    $text = str_replace(' ', '&nbsp;', $text);
                }
                $lines[] = static::tag('option', $text, $attrs);
            }
        }

        return implode("\n", $lines);
    }
}