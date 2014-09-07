<?php
namespace DataStore\Query\Field;

use DataStore\Query\Condition,
	DataStore\Query\Query;

class Field extends \Bliss\Component
{
	const ALL = "*";
	
	/**
	 * @var string
	 */
	private $sourceName;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var string
	 */
	protected $alias;
	
	/**
	 * @var string
	 */
	protected $label;
	
	/**
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * @var \DataQuery\Query\Condition\Collection
	 */
	private $conditions;
	
	/**
	 * @var boolean
	 */
	protected $isVisible = true;
	
	/**
	 * @var int
	 */
	protected $order = 0;
	
	/**
	 * @var \DataStore\Query\Query
	 */
	private $query;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->conditions = new Condition\Collection();
		$this->setIsBasic();
	}
	
	/**
	 * Set the conditions for the field
	 * 
	 * @param \DataQuery\Query\Condition\Collection $conditions
	 */
	public function setConditions(Condition\Collection $conditions)
	{
		$this->conditions = $conditions;
	}
	
	/**
	 * Get all conditions for the field
	 * 
	 * @return \DataStore\Query\Condition\Collection
	 */
	public function getConditions()
	{
		return $this->conditions;
	}
	
	/**
	 * Add a condition to the field
	 * 
	 * @param \DataQuery\Query\Condition\Condition $condition
	 */
	public function addCondition(Condition\Condition $condition)
	{
		$this->conditions->add($condition);
	}
	
	/**
	 * Create and add a new condition for the field
	 * 
	 * @param string $value
	 * @param string $operator
	 * @param boolean $replace Whether to replace any current conditions for the field
	 * @return \DataStore\Query\Field\Field
	 */
	public function compareTo($value, $operator = Condition\Condition::EQUAL_TO, $replace = true)
	{
		$condition = new Condition\Condition();
		$condition->setField($this);
		$condition->setOperator($operator);
		$condition->setValue($value);
		
		if ($replace === true) {
			$this->conditions->clearItems();
		}
		
		$this->addCondition($condition);
		
		return $this;
	}
	
	/**
	 * Get the name of the source the field belongs to
	 * 
	 * @return string
	 */
	public function getSourceName() 
	{
		return $this->sourceName;
	}

	/**
	 * Set the name of the source the field belongs to
	 * 
	 * @param string $sourceName
	 */
	public function setSourceName($sourceName) 
	{
		$this->sourceName = $sourceName;
	}
		
	/**
	 * Get the name of the field
	 * 
	 * @return string
	 */
	public function getName() 
	{
		return $this->name;
	}
	
	/**
	 * Set the name of the field
	 * 
	 * @param string $name
	 */
	public function setName($name) 
	{
		$this->name = $name;
	}

	/**
	 * Get the field's value
	 * 
	 * @return mixed
	 */
	public function getValue() 
	{
		return $this->value;
	}
	
	/**
	 * Set the field's value
	 * 
	 * @param string $value
	 */
	public function setValue($value) 
	{
		$this->value = $value;
	}
		
	/**
	 * Get the field's alias
	 * 
	 * @return string
	 */
	public function getAlias() 
	{
		return isset($this->alias) ? $this->alias : $this->name;
	}

	/**
	 * Set the field's alias
	 * 
	 * @param string $alias
	 * @return \DataStore\Query\Field\Field
	 */
	public function setAlias($alias) 
	{
		$this->alias = $alias;
		
		return $this;
	}
	
	/**
	 * Get the field's label
	 * 
	 * @return string
	 */
	public function getLabel() 
	{
		return isset($this->label) ? $this->label : $this->getAlias();
	}

	/**
	 * Set the field's label
	 * 
	 * @param string $label
	 */
	public function setLabel($label) 
	{
		$this->label = $label;
	}
	
	/**
	 * Set whether the field is visible
	 * 
	 * @param boolean $isVisible
	 */
	public function setIsVisible($isVisible) 
	{
		$this->isVisible = (boolean) $isVisible;
	}
	
	/**
	 * Check if the field is visible
	 * 
	 * @return boolean
	 */
	public function getIsVisible() 
	{
		return $this->isVisible;
	}

	/**
	 * Get the number to order the field by 
	 * 
	 * @return int
	 */
	public function getOrder() 
	{
		return $this->order;
	}
	
	/**
	 * Set the number to order the field by
	 * 
	 * @param int $order
	 */
	public function setOrder($order) 
	{
		$this->order = (int) $order;
	}
	
	/**
	 * Set a query specific for the field
	 * 
	 * @param \DataStore\Query\Query $query
	 */
	public function setQuery(Query $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Get the field's query
	 * 
	 * @return \DataStore\Query\Query|null
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	/**
	 * Check if the field has its own query
	 * 
	 * @return boolean
	 */
	public function hasQuery()
	{
		return $this->query !== null;
	}
				
	/**
	 * Create a new field instance from an array of properties
	 * 
	 * @param array $properties
	 * @return \DataQuery\Query\Field\Field
	 */
	public static function factory(array $properties)
	{
		$field = new self();
		$field->setProperties($properties);
		
		return $field;
	}
}