<?php


namespace App\Gui\Form\Element;


use App\Storm\Model\Model;

class DateInput extends ModelInput
{
	public function render(bool $return = false): string
	{
		$input = new Tag('input', '', [
			'type' => 'hidden',
			'name' => $this->getName(),
			'value' => $this->getValue(),
			'class' => 'form-control flatpickr-input',
			'data-flatpickr' => '',
			'data-default-date' => '',
			'data-alt-input' => 'true'
		]);

		return $input->render($return);
	}
}