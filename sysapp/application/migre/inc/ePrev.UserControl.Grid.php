<?php
class ePrev_UserControl_Grid
{

    private $result;
    private $template_commands;
    
    public function set_template_commands( $value )
    {
        $this->template_commands = $value;
    }

    function ePrev_UserControl_Grid()
    {
        // do nothing
    }

    function __destruct()
    {
        // do nothing
    }
    
    function loadResult($rst)
    {
        $this->result = $rst;
    }
    
    public function doRender()
    {
        $html = "";
        $output = "";
        
        $output = "

            <table align='center' class='tb_lista_resultado'>
                <tr>{columns}</tr>
                {rows}
            </table>

        ";

        // ROWS
        $html = "";
        $count = 0;
        if ($this->result) 
        {
            $bgcolor = "";
            while ($row = pg_fetch_array($this->result))
            {
                if ($count==0)
                {
                    // COLUMNS
                    for ($index = 0; $index < pg_num_fields($this->result); $index++)
                    {
                        
                        if (pg_field_name($this->result, $index)!="registro_id")
                        {
                            $html .= "
                
                                    <th align='center'>
                                        " . pg_field_name($this->result, $index) . "
                                    </th>
                
                            ";
                        }
                        
            
                    }
                    $output = str_replace( "{columns}", $html, $output);
                    $html = "";
    			}
    
                $bgcolor = ($bgcolor=="#ffffff")?"#f4f4f4":"#ffffff";
                $html .= "<tr bgcolor='" . $bgcolor . "'>";
                for ( $index = 0; $index < pg_num_fields( $this->result ); $index++ )
                {
                    if ( pg_field_name( $this->result, $index )!="registro_id" )
                    {
                        $html .= "
                            <td>" . $row[ pg_field_name($this->result, $index) ] . "</td>
                        ";
    
                    }
                }
                $html .= "
                            <td>
                                <div id='commands_div' registoId='" . $row["registro_id"] . "'>
                                    " . str_replace("{registro_id}", $row["registro_id"], $this->template_commands) . "
                                </div>
                            </td>
                ";
    
                $html .= "</tr>";
                $count++;
            }
		}
        $output = str_replace( "{rows}", $html, $output);

        $output = str_replace( "{columns}", "", $output);
        $output = str_replace( "{rows}", "", $output);
        echo( $output );

    }
}
?>
