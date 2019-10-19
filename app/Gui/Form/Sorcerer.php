<?php


namespace App\Gui\Form;

use App\Gui\Form\Element\Element;
use App\Gui\Form\Element\SubmitInput;
use App\Gui\Form\Element\Tag;

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
	/** @var  string $action */
	protected $action;

	/** @var  string $method */
	protected $method;

	/** @var Element[] $elements The various HTML inputs and elements the form contains. */
	protected $elements = [];

	public function __construct(string $action = '', string $method = 'post')
	{
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
	 * @param Element $element
	 */
	public function addElement(Element $element)
	{
		$this->elements[] = $element;
	}

	public function addElements(array $elements)
	{
		foreach($elements as $element)
		{
			$this->addElement($element);
		}
	}

	public function addSubmit(string $value, string $name = '', array $htmlAttributes = [])
	{
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