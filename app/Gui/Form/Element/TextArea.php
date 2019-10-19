<?php


namespace App\Gui\Form\Element;


use App\Storm\Model\Model;

class TextArea extends Element
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
		$input = new Tag('textarea', '', array_merge([
			'name' => $this->attribute,
			'value' => '',
			'class' => 'form-control',
		], $this->htmlAttributes));

		return $input->render($return);
	}
}