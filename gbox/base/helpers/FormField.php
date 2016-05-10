<?php
namespace Gbox\helpers;
class FormField
{
	private $form;
	private $attr;
	private $model;
	private $type;
	private $fieldType = 'input';
	private $label;
	private $labelText;
	private $hint;
	private $error;
	private $inputId;
	private $items = [];
	private $template = "{label}\n{hint}\n{input}\n{error}";
	private $options = ['class' => ['form-group']];
	private $fieldOptions = ['class' => 'form-control'];
	private $labelOptions = ['class' => 'control-label'];
	private $hintOptions = ['class' => 'help-block hint-block'];
	private $errorOptions = ['class' => 'help-block'];
	public function __construct ($form, $model, $attr, $options = null)
	{
		$this->form = $form;
		$this->attr = $attr;
		$this->model = $model;
		$this->inputId = $model::className() . '-' . $attr;
		if ($form->template)
		{
			$this->setTemplate($form->template);
		}
		if (!is_null($options))
		{
			$this->options = $options;
		}
	}
	public function input ($type = 'text', $options = [])
	{
		$this->fieldType = 'input';
		$this->type = $type;
		$this->fieldOptions = array_merge($this->fieldOptions, $options);
		return $this;
	}
	public function textarea ($options = [])
	{
		$this->fieldType = 'textarea';
		$this->fieldOptions = array_merge($this->fieldOptions, $options);
		return $this;
	}
	public function select ($data = [], $options = [])
	{
		$this->fieldType = 'select';
		$this->items = $data;
		$this->fieldOptions = array_merge($this->fieldOptions, $options);
		return $this;
	}
	public function label ($options = [])
	{
		if (is_bool($options))
		{
			$this->label = $options;
		}
		else if (is_string($options))
		{
			$this->label = $options;
		}
		else
		{
			$this->labelOptions = $options;
		}
		return $this;
	}
	public function error ($options = [])
	{
		if (is_bool($options))
		{
			$this->error = $options;
		}
		else
		{
			$this->errorOptions = $options;
		}
		return $this;
	}
	public function hint ($options = [])
	{
		if (is_bool($options))
		{
			$this->hint = $options;
		}
		else
		{
			$this->hint = true;
			if (is_string($options))
			{
				$this->hintOptions['hint'] = $options;
			}
			else
			{
				$this->hintOptions = $options;
			}
		}
		return $this;
	}
	public function __toString ()
	{
		$label = '';
		$input = '';
		$hint = '';
		$error = '';
		$model = $this->model;

		if ($model->getFirstError($this->attr))
		{
			$this->options['class'][] = 'has-error';
		}

		if (!array_key_exists('id', $this->fieldOptions))
		{
			$this->fieldOptions['id'] = $this->inputId;
		}/*
		if (is_array($this->labelOptions) && !array_key_exists('for', $this->labelOptions))
		{
			$this->labelOptions['for'] = $this->inputId;
		}*/

		if ($this->label !== false)
		{
			$label = Html::label(empty($this->label) ? $this->model->getAttrLabel($this->attr) : $this->label, $this->inputId, $this->labelOptions);
		}
		if ($this->error !== false)
		{
			$error = Html::error($this->model, $this->attr, $this->errorOptions);
		}
		if ($this->hint !== false)
		{
			$hint = Html::hint($this->model, $this->attr, $this->hintOptions);
		}

		switch ($this->fieldType)
		{
			case 'input':
				if (in_array($this->type, ['checkbox', 'radio']))
				{
					if (!empty($this->model->{$this->attr}))
					{
						$this->fieldOptions['checked'] = true;
					}
					$input = Html::input($this->type, $model::className() . '[' . $this->attr . ']', null, $this->fieldOptions);
				}
				else
				{
					$input = Html::input($this->type, $model::className() . '[' . $this->attr . ']', $this->model->{$this->attr}, $this->fieldOptions);
				}
				break;
			case 'textarea':
				$input = Html::textarea($model::className() . '[' . $this->attr . ']', $this->model->{$this->attr}, $this->fieldOptions);
				break;
			case 'select':
				$input = Html::dropDownList($model::className() . '[' . $this->attr . ']', $this->model->{$this->attr}, $this->items, $this->fieldOptions);
				break;
		}
		$content = str_replace(['{label}', '{input}', '{hint}', '{error}'], [$label, $input, $hint, $error], $this->template);
		return Html::tag('div', $content, $this->options);
	}
	public function setTemplate ($template)
	{
		$this->template = $template;
		return $this;
	}
}