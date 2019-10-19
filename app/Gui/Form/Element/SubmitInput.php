<?php


namespace App\Gui\Form\Element;


class SubmitInput extends Element
{
	/** @var string $value */
	protected $value;

	/** @var string $name */
	protected $name;

	/** @var array $htmlAttributes */
	protected $htmlAttributes = [];

	public function __construct(string $value, string $name = '', array $htmlAttributes = [])
	{
		$this->value = $value;
		$this->name = $name;
		$this->htmlAttributes = $htmlAttributes;
	}

	public function render(bool $return = false): string
	{
		$input = new Tag('input', '', array_merge([
			'type' => 'submit',
			'name' => $this->name,
			'value' => $this->value,
			'class' => 'form-control col-sm-2 btn-primary',
		], $this->htmlAttributes));

		return $input->render($return);
	}
}