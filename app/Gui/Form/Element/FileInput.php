<?php


namespace App\Gui\Form\Element;


class FileInput extends ModelInput
{
	public function render(bool $return = false): string
	{
		$fileInput = new Tag('input', '', array_merge([
			'type' => 'file',
			'name' => $this->getName(),
			'class' => 'form-control-file'
		], $this->htmlAttributes));
		return $fileInput->render($return);
	}
}