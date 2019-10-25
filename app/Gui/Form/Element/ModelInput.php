<?php


namespace App\Gui\Form\Element;

use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\Model;

abstract class ModelInput extends Input
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

	protected function getValue(): string
	{
		$dataDefinition = $this->model->getDataDefinition();
		$field = $dataDefinition->getField($this->attribute);
		if(!$field)
		{
			throw new Exception('Model ' . get_class($this->model) . ' has no field ' . $this->attribute . '.');
		}
		return (string) $field->getValue(DataFieldDefinition::FORMAT_TYPE_TO_UI);
	}

	protected function getName(): string
	{
		return $this->model->getBaseName() . '[' . $this->attribute . ']';
	}
}