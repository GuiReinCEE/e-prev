<?php
/**
 * TODO: Documentar a classe String (ePrev.Util.Message)
 * 
 * @access public
 * @package ePrev
 * @subpackage Util
 */
class String 
{
    static public function IfBlankReturn( $value, $return )
    {
        if ($value=="")
        {
            return $return;
        }
        else
        {
            return $value;
        }
    }

    static public function if_blank_return( $value, $return )
    {
        if ($value=="")
        {
			return $return;
		}
        else
        {
            return $value;
        }
    }
    
}
?>