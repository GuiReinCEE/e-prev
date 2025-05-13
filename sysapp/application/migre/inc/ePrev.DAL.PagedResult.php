<?php
include_once( "ePrev.DAL.DBConnection.php" );

/**
 * Classe para Paginar resultados 
 * 
 * @access public
 * @package ePrev
 * @subpackage DAL
 * @require ePrev.Util.Message.php
 */
class PagedResult {

    // DAL
    private $db;
    private $dal;
    // get and set
    private $size = 100;
    private $page;
    // get
    private $firstpage = 0;
    private $lastpage;
    private $count;
    private $pageCount;

    // Queries
    private $queryResultset;
    private $queryCount;

    function PagedResult($_db) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    public function createQueryResultset( $value ){ $this->queryResultset = $value; }
    public function createQueryCount( $value ){ $this->queryCount = $value; }

    public function setAttribute( $_param, $_value ){
        $this->queryCount = str_replace($_param, addslashes($_value), $this->queryCount); 
        $this->queryResultset = str_replace($_param, addslashes($_value), $this->queryResultset); 
    }

    public function setPage( $value ){
        if ($value=="") {
            $this->page = 0; 
		}
        else {
            $this->page = $value;
        } 
    }
    public function getPage(){ return $this->page; }
    public function setSize($value){ $this->size = $value; }
    public function getSize(){ return $this->size; }
    public function getCount(){ 
        return $this->count; 
    }
    public function getPageCount(){ 
        return $this->pageCount; 
    }
    public function getFirstPage(){ return $this->firstpage; }
    public function getLastPage(){ return $this->lastpage; }

    public function getResultset(){

        // Counts
        $this->dal->createQuery( $this->queryCount );
        $result = $this->dal->getResultset();
        if ( $reg = pg_fetch_array($result) ) {
            $count = $reg[0];
        }
        $this->count = $count;
        
        $this->pageCount = $this->count / $this->size;
        $this->pageCount = ceil( $this->pageCount );
        
        $this->lastpage = $this->pageCount; 

        // Resultset
        $this->queryResultset .= " LIMIT {pageSize} OFFSET {pageStart} ";
        
        $this->dal->createQuery( $this->queryResultset );
        $this->dal->setAttribute( "{pageSize}", $this->size );
        $this->dal->setAttribute( "{pageStart}", ($this->page*$this->size) );
        
        $result = $this->dal->getResultset();

        return $result;

    }

    function __destruct(){
        $this->dal = null;
    } 

}

?>