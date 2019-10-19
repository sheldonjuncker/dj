<?php


namespace App\Gui\Form\Element;


use App\Storm\Model\Model;

class TextInput extends Element
{
	/** @var Model $model */
	protected $model;

	/** @var string $attribute */
	protected $attribute;

	/** @var array $htmlAttributes */
	protected $htmlAttributes = [];

	public function __construct(Model $model, string $attribute, array $htmlAttributes = [])
	{
		$this->model = $model;
		$this->attribute = $attribute;
		$this->htmlAttributes = $htmlAttributes;
	}

	public function render(bool $return = false): string
	{
		$input = new Tag('input', '', [
			'type' => 'text',
			'name' => $this->attribute,
			'value' => '',
			'class' => 'form-control'
		]);

		return $input->render($return);
	}
}