<?php


namespace App\Gui\Form\Element;


class Tag extends Element
{
	/** @var  string $name */
	protected $name;

	/** @var  string $contents */
	protected $contents;

	/** @var array $attributes */
	protected $attributes = [];

	/**
	 * Tag constructor.
	 * @param string $name
	 * @param string $contents
	 * @param array $attributes
	 */
	public function __construct(string $name, string $contents = '', array $attributes = [])
	{
		$this->name = strtolower($name);
		$this->contents = $contents;
		$this->attributes = $attributes;
	}

	/**
	 * Sets the HTML contents.
	 *
	 * @param string $contents
	 */
	public function setContents(string $contents)
	{
		$this->contents = $contents;
	}

	/**
	 * Appends to the current contents.
	 *
	 * @param string $contents
	 */
	public function addContents(string $contents)
	{
		$this->contents .= $contents;
	}

	/**
	 * Adds/replaces an HTML attribute.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addAttribute(string $name, string $value)
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function render(bool $return = false): string
	{
		$attributes = $this->generateAttributes();
		$tag = '<' . $this->name . ' ' . $attributes;

		//Invalid tag type/contents combo
		if(!$this->needsClosingTag() && !empty($this->contents))
		{
			throw new Exception("Tag {$this->name} has no closing method and cannot have contents.");
		}
		else if($this->needsClosingTag())
		{
			$tag .= '>' . $this->contents . '</' . $this->name . '>';
		}
		else
		{
			$tag .= ' />';
		}

		if($return)
		{
			return $tag;
		}
		else
		{
			print $tag;
			return '';
		}
	}

	/**
	 * Determines whether or not a closing tag is required.
	 *
	 * @return bool
	 */
	protected function needsClosingTag(): bool
	{
		return !in_array($this->name, [
			'input',
			'hr',
			'br'
		]);
	}

	/**
	 * Generates attribute string for all HTML attributes.
	 * Escapes using htmlspecialchars as this is all that is necessary
	 * when working with properly quoted attributes.
	 *
	 * @return string
	 */
	protected function generateAttributes(): string
	{
		$attributes = '';
		foreach($this->attributes as $name => $value)
		{
			$attributes .= $name . '="' . htmlspecialchars($value) . '" ';
		}
		return trim($attributes);
	}
}