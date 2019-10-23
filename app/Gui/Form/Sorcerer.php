<?php


namespace App\Gui\Form;

use App\Gui\Form\Element\Element;
use App\Gui\Form\Element\SubmitInput;
use App\Gui\Form\Element\Tag;
use App\Storm\Model\Model;

/**
 * Class Sorcerer
 *
 * A helper class for building dynamic forms without using raw HTML.
 * This is emphatically a Sorcerer and not something else like a Wizard.
 * Wizards, as everyone knows, are much more difficult to work with than Sorcerers.
 *
 * The basic design of this class is that you set some basic form options,
 * add some inputs and buttons,
 * and then pass it to the view to be rendered.
 *
 * It's for the create and edit form seen on most standard CRUDs.
 *
 * @package App\Gui\Form
 */
class Sorcerer
{
	const EDIT = 'edit';
	const CREATE = 'create';

	/** @var Model $model */
	protected $model;

	/** @var  string $action */
	protected $action;

	/** @var  string $method */
	protected $method;

	/** @var  string $mode create|edit */
	protected $mode;

	/** @var Element[] $elements The various HTML inputs and elements the form contains. */
	protected $elements = [];

	public function __construct(Model $model, string $action = '', string $method = 'post')
	{
		$this->model = $model;
		$this->action = $action;
		$this->method = $method;
	}

	/**
	 * Sets the form's action.
	 * @todo: Allow this to accept a Nette action where it can be things like [presenter, action] or even an object from the route info.
	 *
	 * @param string $action
	 */
	public function setAction(string $action)
	{
		$this->action = $action;
	}

	/**
	 * Sets the form's method.
	 *
	 * @param string $method
	 */
	public function setMethod(string $method)
	{
		$this->method = $method;
	}

	/**
	 * @param string $mode
	 */
	public function setMode(string $mode)
	{
		if(!in_array($mode, [self::CREATE, self::EDIT]))
		{
			throw new Hex('Invalid form mode ' . $mode . ', only create and edit are allowed.');
		}
		$this->mode = $mode;
	}

	/**
	 * @param Element $element
	 */
	public function addElement(Element $element)
	{
		$element->setForm($this);
		$this->elements[] = $element;
	}

	public function addElements(array $elements)
	{
		foreach($elements as $element)
		{
			$this->addElement($element);
		}
	}

	public function addSubmit( array $htmlAttributes = [])
	{
		$value = $this->mode == self::EDIT ? 'Update' : 'Add';
		$name = $htmlAttributes['name'] ?? ('_' . strtolower($value));
		$this->addElement(
			new SubmitInput($value, $name, $htmlAttributes)
		);
	}

	/**
	 * Renders the form or returns output.
	 *
	 * @param bool $return
	 * @return string
	 */
	public function render(bool $return = false): string
	{
		$form = new Tag('form');
		$form->addAttribute('action', $this->action);
		$form->addAttribute('method', $this->method);
		$form->addAttribute('enctype', 'multipart/form-data');

		foreach($this->elements as $element)
		{
			$formGroup = new Tag('div', $element->render(true), [
				'class' => 'form-group'
			]);
			$form->addContents($formGroup->render(true));
		}

		return $form->render($return);
	}
}