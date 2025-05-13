<?php
class Collection
{
	public $items = array();
	
	public function length()
	{
		return sizeof($this->items);
	}
	
	public function add($item)
	{
		$this->items[sizeof($this->items)] = $item;
	}
}
?>