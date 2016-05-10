<?php
namespace Gbox\base;
abstract class Model
{
	private $__errors = [];
	public function rules ()
	{
		return [];
	}
	public function attrLabels ()
	{
		return [];
	}
	public function attrHints()
    {
        return [];
    }
	public static function className ()
	{
		$array = explode('\\', get_called_class());
		return array_pop($array);
	}
	public function load ($data = [])
	{
		if (!array_key_exists(self::className(), $data))
		{
			return false;
		}
		if (!is_array($data[self::className()]))
		{
			return false;
		}
		foreach ($data[self::className()] as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->{$key} = $value;
			}
		}
		return true;
	}
	public function getAttrLabel ($attr)
	{
		if (array_key_exists($attr, $this->attrLabels()))
		{
			return $this->attrLabels()[$attr];
		}
		else
		{
			return $attr;
		}
	}
	public function getAttrHint ($attr)
    {
        $hints = $this->attrHints();
        return isset($hints[$attr]) ? $hints[$attr] : '';
    }
	public function validate ()
	{
		$rules = $this->rules();
		foreach ($rules as $rule)
		{
			if (!is_array($rule[0]))
			{
				$rule[0] = [$rule[0]];
			}
			foreach ($rule[0] as $attr)
			{
				$value = $this->{$attr};
				$valueIsNullOrEmpty = ($value === NULL || $value === '');
				if (!array_key_exists('message', $rule))
				{
					$rule['message'] = 'Error en ' . $this->getAttrLabel($attr);
				}
				switch ($rule[1])
				{
					case 'required':
						if ($valueIsNullOrEmpty)
						{
							$this->addError($attr, $rule['message']);
						}
						break;
					case 'email':
						if (!$valueIsNullOrEmpty && !filter_var($value, FILTER_VALIDATE_EMAIL))
						{
							$this->addError($attr, $rule['message']);
						}
						break;
					case 'integer':
						if (!$valueIsNullOrEmpty && !is_numeric($value))
						{
							$this->addError($attr, $rule['message']);
						}
						break;
					case 'match':
						if (!array_key_exists('pattern', $rule))
						{
							throw new Exception('No se ha indicado el patrón con el que se debe comparar el valor de la propiedad.');
						}
						if (!$valueIsNullOrEmpty && !preg_match($rule['pattern'], $value))
						{
							$this->addError($attr, $rule['message']);
						}
						break;
					default:
						// if (!$valueIsNullOrEmpty)
						// {
							if (method_exists($this, $rule[1]))
							{
								call_user_func_array([$this, $rule[1]], [$attr]);
							}
							else
							{
								throw new Exception("No existe el método.");
							}
						// }
						break;
				}
			}
		}
		return !$this->hasErrors();
	}
	public function addError ($attr, $message)
	{
		$message = str_replace('{label}', $this->getAttrLabel($attr), $message);
		array_push($this->__errors, ['attr' => $attr, 'message' => $message]);
		/*=============================
		=            DEBUG            =
		=============================*/
		\Gbox::$components->debug->saveReportModel([
			'model' => static::className(),
			'message' => $message,
			'attr' => $attr,
			'subtype' => 'error',
		]);
		/*=====  End of DEBUG  ======*/
	}
	public function getErrors ()
	{
		return $this->__errors;
	}
	public function getFirstError ($attr)
	{
		foreach ($this->__errors as $error)
		{
			if ($error['attr'] == $attr)
			{
				return $error['message'];
			}
		}
		return false;
	}
	public function getFirstErrors ()
	{
		$array = [];
		$messages = [];
		foreach ($this->__errors as $error)
		{
			if (!in_array($error['attr'], $array))
			{
				array_push($array, $error['attr']);
				array_push($messages, $error['message']);
			}
		}
		return $messages;
	}
	public function hasErrors ()
	{
		$errors = $this->getErrors();
		return (count($errors) != 0);
	}
}