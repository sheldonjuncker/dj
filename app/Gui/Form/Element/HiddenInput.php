<?php


namespace App\Gui\Form\Element;


use App\Storm\DataDefinition\DataFieldDefinition;

class HiddenInput extends ModelInput
{
	public function render(bool $return = false): string
	{
		$input =  new Tag('input', '', array_merge([
			'type' => 'hidden',
			'name' => $this->getName(),
			'value' => $this->getValue()
		], $this->htmlAttributes));
		return $input->render($return);
	}
}