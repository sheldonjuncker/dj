<?php


namespace App\Gui\Form\Element;


class DropDownList extends ModelInput
{
	protected $options = [];

	public function addOptions(array $options)
	{
		$this->options = $options;
	}

	public function render(bool $return = false): string
	{
		$optionsHtml = '';
		$selectedValue = $this->getValue();
		foreach($this->options as $key => $value)
		{
			$options = [
				'value' => $key
			];
			if($selectedValue == $key)
			{
				$options['selected'] = 'true';
			}

			$optionTag = new Tag('option', $value, $options);
			$optionsHtml .= $optionTag->render(true);
		}

		$selectTag = new Tag('select', $optionsHtml, array_merge([
			'name' => $this->getName(),
			'class' => 'form-control'
		], $this->htmlAttributes));
		return $selectTag->render($return);
	}
}