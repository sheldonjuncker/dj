<?php


namespace App\Gui\Form\Element;


use App\Storm\Model\Model;

class TextInput extends ModelInput
{
	public function render(bool $return = false): string
	{
		$input = new Tag('input', '', array_merge([
			'type' => 'text',
			'name' => $this->getName(),
			'value' => $this->getValue(),
			'class' => 'form-control'
		], $this->htmlAttributes));

		return $input->render($return);
	}
}