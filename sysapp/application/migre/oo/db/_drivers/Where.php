<?php
class WhereComp
{
	static public $EQUALS = 0;
	static public $NOT_EQUALS = 1;
	static public $LIKE = 2;
}
class WhereSepar
{
	static public $FIRST = 0;
	static public $AND = 1;
	static public $OR = 2;
}
class Where
{
	public $separation;
	public $column;
	public $comparator;
	public $value;

	function __construct( $separation = WhereSepar::FIRST, $coluna, $comparador = WhereComp::EQUALS, $valor )
	{
		$this->column = $coluna;
		$this->comp = $comparador;
		$this->value = $valor;
	}
}
?>