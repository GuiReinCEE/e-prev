<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');	
	require('inc/fpdf153/fpdf.php');

	function ConvertSize($size=5,$maxsize=0)
	{
		// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
		//Identify size (remember: we are using 'mm' units here)
		if ( stristr($size,'px') ) $size *= 0.2645; //pixels
		elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
		elseif ( stristr($size,'mm') ) $size += 0; //millimeters
		elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
		elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
		elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
		elseif ( stristr($size,'%') )
		{
			$size += 0; //make "90%" become simply "90" 
			$size *= $maxsize/100;
		}
		else $size *= 0.2645; //nothing == px

		return $size;
	}

	function execClick($data_string,$url)
	{
		$ch = curl_init($url);			   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		#print_r($response);
		#echo "<hr>";
		
		$a = $response;
		$b = json_decode($response,TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_DEPTH:
					echo utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
				break;
				case JSON_ERROR_STATE_MISMATCH:
					echo utf8_encode('(JSON) Inválido ou mal formado');
				break;
				case JSON_ERROR_CTRL_CHAR:
					echo utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
				break;
				case JSON_ERROR_SYNTAX:
					echo utf8_encode('(JSON) Erro de sintaxe');
				break;
				case JSON_ERROR_UTF8:
					echo utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
				break;
				default:
					echo utf8_encode('(JSON) Erro não identificado');
				break;
			}
		}
		else
		{
			
			#echo "<PRE>";
			#print_r($b);
			#echo $b['document']['key'];
			#exit;
			#echo "DOC KEY => ".$b['document']['key']."<BR>";
			#echo "ASS 1 KEY => ".$b['document']['signers'][0]['key']."<BR>";
			#echo "</PRE>";

			
		}		

		return array("JSON" => $a, "ARRAY" => $b);
	}

	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 ) 
				VALUES
					 (
					   ".$_SESSION['SID'].",
					   CURRENT_TIMESTAMP,
					   'INSTITUIDOR_FORMA_PAGAMENTO_FORMULARIO'
					 )
		      ";
	@pg_query($db,$qr_sql); 	
	
	
	$ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	
	class PDF extends FPDF
	{
		var $widths;
		var $aligns;

		function SetWidths($w)
		{
		    //Set the array of column widths
		    $this->widths=$w;
		}

		function SetAligns($a)
		{
		    //Set the array of column alignments
		    $this->aligns=$a;
		}

		function Row($data)
		{
		    //Calculate the height of the row
		    $nb=0;
		    for($i=0;$i<count($data);$i++)
		        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		    $h=3*$nb;
		    //Issue a page break first if needed
		    $this->CheckPageBreak($h);
		    //Draw the cells of the row
		    for($i=0;$i<count($data);$i++)
		    {
		        $w=$this->widths[$i];
		        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		        //Save the current position
		        $x=$this->GetX();
		        $y=$this->GetY();
		        //Draw the border
		        $this->Rect($x,$y,$w,$h);
		        //Print the text
		        $this->MultiCell($w,3,$data[$i],0,$a);
		        //Put the position to the right of the cell
		        $this->SetXY($x+$w,$y);
		    }
		    //Go to the next line
		    $this->Ln($h);
		}

		function CheckPageBreak($h)
		{
		    //If the height h would cause an overflow, add a new page immediately
		    if($this->GetY()+$h>$this->PageBreakTrigger)
		        $this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt)
		{
		    //Computes the number of lines a MultiCell of width w will take
		    $cw=&$this->CurrentFont['cw'];
		    if($w==0)
		        $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s=str_replace("\r",'',$txt);
		    $nb=strlen($s);
		    if($nb>0 and $s[$nb-1]=="\n")
		        $nb--;
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb)
		    {
		        $c=$s[$i];
		        if($c=="\n")
		        {
		            $i++;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		            continue;
		        }
		        if($c==' ')
		            $sep=$i;
		        $l+=$cw[$c];
		        if($l>$wmax)
		        {
		            if($sep==-1)
		            {
		                if($i==$j)
		                    $i++;
		            }
		            else
		                $i=$sep+1;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		        }
		        else
		            $i++;
		    }
		    return $nl;
		}		
		
	    // Sets line style
	    // Parameters:
	    // - style: Line style. Array with keys among the following:
	    //   . width: Width of the line in user units
	    //   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
	    //   . join: miter, round or bevel
	    //   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
	    //           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
	    //                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
	    //   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
	    //   . color: Draw color. Array with components (red, green, blue)
	    function SetLineStyle($style) {
	        extract($style);
	        if (isset($width)) {
	            $width_prev = $this->LineWidth;
	            $this->SetLineWidth($width);
	            $this->LineWidth = $width_prev;
	        }
	        if (isset($cap)) {
	            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
	            if (isset($ca[$cap]))
	                $this->_out($ca[$cap] . ' J');
	        }
	        if (isset($join)) {
	            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
	            if (isset($ja[$join]))
	                $this->_out($ja[$join] . ' j');
	        }
	        if (isset($dash)) {
	            $dash_string = '';
	            if ($dash) {
	                if(preg_match('/^.+,/', $dash))
	                    $tab = explode(',', $dash);
	                else
	                    $tab = array($dash);
	                $dash_string = '';
	                foreach ($tab as $i => $v) {
	                    if ($i > 0)
	                        $dash_string .= ' ';
	                    $dash_string .= sprintf('%.2f', $v);
	                }
	            }
	            if (!isset($phase) || !$dash)
	                $phase = 0;
	            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
	        }
	        if (isset($color)) {
	            list($r, $g, $b) = $color;
	            $this->SetDrawColor($r, $g, $b);
	        }
	    }

	    // Draws a line
	    // Parameters:
	    // - x1, y1: Start point
	    // - x2, y2: End point
	    // - style: Line style. Array like for SetLineStyle
	    function Line($x1, $y1, $x2, $y2, $style = null) {
	        if ($style)
	            $this->SetLineStyle($style);
	        parent::Line($x1, $y1, $x2, $y2);
	    }

	    // Draws a rectangle
	    // Parameters:
	    // - x, y: Top left corner
	    // - w, h: Width and height
	    // - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
	    // - border_style: Border style of rectangle. Array with some of this index
	    //   . all: Line style of all borders. Array like for SetLineStyle
	    //   . L: Line style of left border. null (no border) or array like for SetLineStyle
	    //   . T: Line style of top border. null (no border) or array like for SetLineStyle
	    //   . R: Line style of right border. null (no border) or array like for SetLineStyle
	    //   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $border_style = null;
	                parent::Rect($x, $y, $w, $h, $style);
	                break;
	            case 'DF': case 'FD':
	                if (!$border_style || isset($border_style['all'])) {
	                    if (isset($border_style['all'])) {
	                        $this->SetLineStyle($border_style['all']);
	                        $border_style = null;
	                    }
	                } else
	                    $style = 'F';
	                parent::Rect($x, $y, $w, $h, $style);
	                break;
	            default:
	                if (!$border_style || isset($border_style['all'])) {
	                    if (isset($border_style['all']) && $border_style['all']) {
	                        $this->SetLineStyle($border_style['all']);
	                        $border_style = null;
	                    }
	                    parent::Rect($x, $y, $w, $h, $style);
	                }
	                break;
	        }
	        if ($border_style) {
	            if (isset($border_style['L']) && $border_style['L'])
	                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
	            if (isset($border_style['T']) && $border_style['T'])
	                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
	            if (isset($border_style['R']) && $border_style['R'])
	                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
	            if (isset($border_style['B']) && $border_style['B'])
	                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
	        }
	    }

	    // Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
	    // Parameters:
	    // - x0, y0: Start point
	    // - x1, y1: Control point 1
	    // - x2, y2: Control point 2
	    // - x3, y3: End point
	    // - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
	    // - line_style: Line style for curve. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $op = 'f';
	                $line_style = null;
	                break;
	            case 'FD': case 'DF':
	                $op = 'B';
	                break;
	            default:
	                $op = 'S';
	                break;
	        }
	        if ($line_style)
	            $this->SetLineStyle($line_style);

	        $this->_Point($x0, $y0);
	        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
	        $this->_out($op);
	    }

	    // Draws an ellipse
	    // Parameters:
	    // - x0, y0: Center point
	    // - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
	    // - angle: Orientation angle (anti-clockwise)
	    // - astart: Start angle
	    // - afinish: Finish angle
	    // - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
	    // - line_style: Line style for ellipse. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - nSeg: Ellipse is made up of nSeg Bézier curves
	    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
	        if ($rx) {
	            if (!(false === strpos($style, 'F')) && $fill_color) {
	                list($r, $g, $b) = $fill_color;
	                $this->SetFillColor($r, $g, $b);
	            }
	            switch ($style) {
	                case 'F':
	                    $op = 'f';
	                    $line_style = null;
	                    break;
	                case 'FD': case 'DF':
	                    $op = 'B';
	                    break;
	                case 'C':
	                    $op = 's'; // small 's' means closing the path as well
	                    break;
	                default:
	                    $op = 'S';
	                    break;
	            }
	            if ($line_style)
	                $this->SetLineStyle($line_style);
	            if (!$ry)
	                $ry = $rx;
	            $rx *= $this->k;
	            $ry *= $this->k;
	            if ($nSeg < 2)
	                $nSeg = 2;

	            $astart = deg2rad((float) $astart);
	            $afinish = deg2rad((float) $afinish);
	            $totalAngle = $afinish - $astart;

	            $dt = $totalAngle/$nSeg;
	            $dtm = $dt/3;

	            $x0 *= $this->k;
	            $y0 = ($this->h - $y0) * $this->k;
	            if ($angle != 0) {
	                $a = -deg2rad((float) $angle);
	                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
	                $x0 = 0;
	                $y0 = 0;
	            }

	            $t1 = $astart;
	            $a0 = $x0 + ($rx * cos($t1));
	            $b0 = $y0 + ($ry * sin($t1));
	            $c0 = -$rx * sin($t1);
	            $d0 = $ry * cos($t1);
	            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
	            for ($i = 1; $i <= $nSeg; $i++) {
	                // Draw this bit of the total curve
	                $t1 = ($i * $dt) + $astart;
	                $a1 = $x0 + ($rx * cos($t1));
	                $b1 = $y0 + ($ry * sin($t1));
	                $c1 = -$rx * sin($t1);
	                $d1 = $ry * cos($t1);
	                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
	                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
	                            ($a1 - ($c1 * $dtm)) / $this->k,
	                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
	                            $a1 / $this->k,
	                            $this->h - ($b1 / $this->k));
	                $a0 = $a1;
	                $b0 = $b1;
	                $c0 = $c1;
	                $d0 = $d1;
	            }
	            $this->_out($op);
	            if ($angle !=0)
	                $this->_out('Q');
	        }
	    }

	    // Draws a circle
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius
	    // - astart: Start angle
	    // - afinish: Finish angle
	    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
	    // - line_style: Line style for circle. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - nSeg: Ellipse is made up of nSeg Bézier curves
	    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
	        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
	    }

	    // Draws a polygon
	    // Parameters:
	    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
	        $np = count($p) / 2;
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $line_style = null;
	                $op = 'f';
	                break;
	            case 'FD': case 'DF':
	                $op = 'B';
	                break;
	            default:
	                $op = 'S';
	                break;
	        }
	        $draw = true;
	        if ($line_style)
	            if (isset($line_style['all']))
	                $this->SetLineStyle($line_style['all']);
	            else { // 0 .. (np - 1), op = {B, S}
	                $draw = false;
	                if ('B' == $op) {
	                    $op = 'f';
	                    $this->_Point($p[0], $p[1]);
	                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
	                        $this->_Line($p[$i], $p[$i + 1]);
	                    $this->_Line($p[0], $p[1]);
	                    $this->_out($op);
	                }
	                $p[$np * 2] = $p[0];
	                $p[($np * 2) + 1] = $p[1];
	                for ($i = 0; $i < $np; $i++)
	                    if (!empty($line_style[$i]))
	                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
	            }

	        if ($draw) {
	            $this->_Point($p[0], $p[1]);
	            for ($i = 2; $i < ($np * 2); $i = $i + 2)
	                $this->_Line($p[$i], $p[$i + 1]);
	            $this->_Line($p[0], $p[1]);
	            $this->_out($op);
	        }
	    }

	    // Draws a regular polygon
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius of circumscribed circle
	    // - ns: Number of sides
	    // - angle: Orientation angle (anti-clockwise)
	    // - circle: Draw circumscribed circle or not
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
	        if ($ns < 3)
	            $ns = 3;
	        if ($circle)
	            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
	        $p = null;
	        for ($i = 0; $i < $ns; $i++) {
	            $a = $angle + ($i * 360 / $ns);
	            $a_rad = deg2rad((float) $a);
	            $p[] = $x0 + ($r * sin($a_rad));
	            $p[] = $y0 + ($r * cos($a_rad));
	        }
	        $this->Polygon($p, $style, $line_style, $fill_color);
	    }

	    // Draws a star polygon
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius of circumscribed circle
	    // - nv: Number of vertices
	    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
	    // - angle: Orientation angle (anti-clockwise)
	    // - circle: Draw circumscribed circle or not
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
	        if ($nv < 2)
	            $nv = 2;
	        if ($circle)
	            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
	        $p2 = null;
	        $visited = null;
	        for ($i = 0; $i < $nv; $i++) {
	            $a = $angle + ($i * 360 / $nv);
	            $a_rad = deg2rad((float) $a);
	            $p2[] = $x0 + ($r * sin($a_rad));
	            $p2[] = $y0 + ($r * cos($a_rad));
	            $visited[] = false;
	        }
	        $p = null;
	        $i = 0;
	        do {
	            $p[] = $p2[$i * 2];
	            $p[] = $p2[($i * 2) + 1];
	            $visited[$i] = true;
	            $i += $ng;
	            $i %= $nv;
	        } while (!$visited[$i]);
	        $this->Polygon($p, $style, $line_style, $fill_color);
	    }

	    // Draws a rounded rectangle
	    // Parameters:
	    // - x, y: Top left corner
	    // - w, h: Width and height
	    // - r: Radius of the rounded corners
	    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
	    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
	    // - border_style: Border style of rectangle. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
	        if ('0000' == $round_corner) // Not rounded
	            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
	        else { // Rounded
	            if (!(false === strpos($style, 'F')) && $fill_color) {
	                list($red, $g, $b) = $fill_color;
	                $this->SetFillColor($red, $g, $b);
	            }
	            switch ($style) {
	                case 'F':
	                    $border_style = null;
	                    $op = 'f';
	                    break;
	                case 'FD': case 'DF':
	                    $op = 'B';
	                    break;
	                default:
	                    $op = 'S';
	                    break;
	            }
	            if ($border_style)
	                $this->SetLineStyle($border_style);

	            $MyArc = 4 / 3 * (sqrt(2) - 1);

	            $this->_Point($x + $r, $y);
	            $xc = $x + $w - $r;
	            $yc = $y + $r;
	            $this->_Line($xc, $y);
	            if ($round_corner[0])
	                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
	            else
	                $this->_Line($x + $w, $y);

	            $xc = $x + $w - $r ;
	            $yc = $y + $h - $r;
	            $this->_Line($x + $w, $yc);

	            if ($round_corner[1])
	                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
	            else
	                $this->_Line($x + $w, $y + $h);

	            $xc = $x + $r;
	            $yc = $y + $h - $r;
	            $this->_Line($xc, $y + $h);
	            if ($round_corner[2])
	                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
	            else
	                $this->_Line($x, $y + $h);

	            $xc = $x + $r;
	            $yc = $y + $r;
	            $this->_Line($x, $yc);
	            if ($round_corner[3])
	                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
	            else {
	                $this->_Line($x, $y);
	                $this->_Line($x + $r, $y);
	            }
	            $this->_out($op);
	        }
	    }

	    /* PRIVATE METHODS */

	    // Sets a draw point
	    // Parameters:
	    // - x, y: Point
	    function _Point($x, $y) {
	        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
	    }

	    // Draws a line from last draw point
	    // Parameters:
	    // - x, y: End point
	    function _Line($x, $y) {
	        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
	    }

	    // Draws a Bézier curve from last draw point
	    // Parameters:
	    // - x1, y1: Control point 1
	    // - x2, y2: Control point 2
	    // - x3, y3: End point
	    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
	        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	    }		
		
		function Footer()
		{
		    //Go to 1.5 cm from bottom
		    $this->SetY(-15);
		    //Select Arial italic 8
		    $this->SetFont('Courier','I',8);
		    //Print current and total page numbers
		    //$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}
		
		
		
	}


	if($_REQUEST['fl_conta'] == "T")
	{
		$dados_conta = "CONTA CORRENTE	
Nome: ".$_REQUEST['nome_correntista']."
CPF: ".$_REQUEST['cpf_correntista']."		
E-mail: ".$_REQUEST['email_correntista']."		
Telefone: ".$_REQUEST['celular_correntista']."		
Banco: 41 - BANRISUL
Agência: ".$_REQUEST['cd_agencia']."
Conta corrente: ".$_REQUEST['nr_conta'];

		$dados_assinatura = "
____________________________             ____________________________
 Assinatura Participante                  Assinatura Correntista
"; 
	}
	else
	{
		$dados_conta = "CONTA CORRENTE	
Banco: 41 - BANRISUL
Agência: ".$_REQUEST['cd_agencia']."
Conta corrente: ".$_REQUEST['nr_conta'];	

		$dados_assinatura = "
____________________________
 Assinatura Participante     
"; 	
	}		


	$qr_sql = "
				SELECT nome,
					   funcoes.format_cpf(TO_CHAR(cpf_mf,'FM00000000000')) AS cpf
				  FROM public.participantes
				 WHERE cd_empresa            = ".$_SESSION['EMP']."
				   AND cd_registro_empregado = ".$_SESSION['RE']."
				   AND seq_dependencia       = ".$_SESSION['SEQ']."
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);
	$dados_participante = "PARTICIPANTE
Nome: ".trim($ar_reg['nome'])."  
Nº de identificação do participante: ".$_SESSION['EMP']."/".$_SESSION['RE']."/".$_SESSION['SEQ']."
CPF: ".$ar_reg['cpf'];
	
	$ob_pdf = new PDF();

	$ob_pdf->AddPage();

	$ob_pdf->setX(11);
	$ob_pdf->Image('img/logofundacao_carta.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), ConvertSize(150,$ob_pdf->pgwidth), ConvertSize(33,$ob_pdf->pgwidth),'','',false);


	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->Text(160,25, date('d/m/Y G:i:s'));

	
	$ob_pdf->SetXY(10,12);
	$ob_pdf->SetFont('Courier','B',14);
	$ob_pdf->MultiCell(190, 4, "   AUTORIZAÇÃO PARA DÉBITO EM CONTA",0,"C");
	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','',11);
	$ob_pdf->MultiCell(190, 5, "À
	
FUNDAÇÃO FAMÍLIA PREVIDÊNCIA

".$_REQUEST['texto_correntista']."
 
".$dados_participante."

".$dados_conta."



".$dados_assinatura."
");

	###### $ob_pdf->Output(); exit;
	$pdf_64 = base64_encode($ob_pdf->Output('AUTORIZA-DEBITO-'.md5(uniqid(rand(), true)).'.pdf','S'));
	
	
	#### VALIDAR TELEFONE CELULAR E EMAIL DO PARTICIPANTE ####
	$qr_sql = "
				SELECT (p.ddd_celular::TEXT || p.celular::TEXT) AS celular,
				       LOWER(COALESCE(COALESCE(p.email,p.email_profissional),'')) AS email
				  FROM public.participantes p
				 WHERE p.cd_empresa                                   = ".$_SESSION['EMP']."
				   AND p.cd_registro_empregado                        = ".$_SESSION['RE']." 
				   AND p.seq_dependencia                              = ".$_SESSION['SEQ']."
				   AND COALESCE(p.celular,0)                          > 0
				   AND p.celular::TEXT                                LIKE '9%'                   
				   AND LENGTH(p.ddd_celular::TEXT || p.celular::TEXT) = 11	
				   AND COALESCE(COALESCE(p.email,p.email_profissional),'') LIKE '%@%.%'
		      ";
	$ob_resul = pg_query($db,$qr_sql);		
	$ar_reg   = pg_fetch_array($ob_resul);
	$_CELULAR = intval($ar_reg['celular']);	
	$_EMAIL   = trim($ar_reg['email']);	
	
	echo ($_FL_DEBUG ? $_CELULAR."|".$_EMAIL."<BR>" : "");
	#print_r($ar_reg); exit;
	
	if((trim($_CELULAR) == "") OR (trim($_EMAIL) == ""))
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center;'>
							<H2>Telefone celular ou e-mail do(a) Participante não identificado</H2>
							Para assinar digitalmente a autorização débito em conta é necessário ter um telefone celular e um e-mail cadastrado, entre em contato com a nossa central de atendimento 08005102596 de segunda à sexta.
						</DIV>
					<BR><BR>";		
		
		
		echo $conteudo;
		pg_close($db);		
		exit;
	}	
	
	#### VALIDAR TELEFONE CELULAR E EMAIL DO CORRENTISTA/TERCEIRO ####
	$_CELULAR_CORRENTISTA = intval(trim(str_replace("(","",str_replace(")","",trim($_REQUEST['celular_correntista'])))));
	$_EMAIL_CORRENTISTA   = trim($_REQUEST['email_correntista']);
	
	if($_REQUEST['fl_conta'] == "T")
	{
		if((intval(trim($_CELULAR_CORRENTISTA)) == 0) OR (trim($_CELULAR_CORRENTISTA) == "") OR (trim($_EMAIL_CORRENTISTA) == ""))
		{
			$conteudo = "<BR>
							<DIV style='font-family: Calibri, Arial; width:100%; text-align:center;'>
								<H2>Telefone celular ou e-mail do(a) Correntista não identificado</H2>
								Para assinar digitalmente a autorização débito em conta é necessário ter um telefone celular e um e-mail cadastrado, entre em contato com a nossa central de atendimento 08005102596 de segunda à sexta.
							</DIV>
						<BR><BR>";		
			
			
			echo $conteudo;
			pg_close($db);		
			exit;
		}	
	}

	#### ENVIAR PARA ASSINATURA ####
	
	
	#### ATIVA O DEBUG ####
	$_FL_DEBUG = FALSE;
	echo ($_FL_DEBUG ? "<PRE>" : "");
	
	#### BUSCA CONFIGURACAO DA API ####
	if($ip_host == 'srvpg.eletroceee.com.br')
	{
		#### PRODUCAO ####
		$qr_sql = "
					SELECT ds_ambiente, 
					       ds_token, 
						   ds_url
                      FROM clicksign.configuracao
					 WHERE ds_ambiente = 'PRODUCAO'
					   AND dt_exclusao IS NULL
				  ";
		$ob_resul = pg_query($db,$qr_sql);		
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$_API_AMBIENTE = trim($ar_reg['ds_ambiente']);		
		$_API_URL      = trim($ar_reg['ds_url']);		
		$_API_TOKEN    = trim($ar_reg['ds_token']);
	}
	else
	{
		#### DESENVOLVIMENTO #####
		$qr_sql = "
					SELECT ds_ambiente, 
					       ds_token, 
						   ds_url
                      FROM clicksign.configuracao
					 WHERE ds_ambiente = 'DESENVOLVIMENTO'
					   AND dt_exclusao IS NULL
				  ";
		$ob_resul = pg_query($db,$qr_sql);		
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$_API_AMBIENTE = trim($ar_reg['ds_ambiente']);	
		$_API_URL      = trim($ar_reg['ds_url']);		
		$_API_TOKEN    = trim($ar_reg['ds_token']);	
	}

	echo ($_FL_DEBUG ? $ip_host."|".$_API_AMBIENTE."|".$_API_URL."|".$_API_TOKEN."<BR>" : "");	

	#293;"AUTORIZAÇÃO PARA DÉBITO EM CONTA"
	$_CD_DOC_ELETRO = 293;
	$_NOME_DOC = "AUTORIZACAO PARA DEBITO EM CONTA";
	$dt_limite = new DateTime('+30 day');
	#echo $date->format('Y-m-d H:i:s');	exit;	
	
	#### CRIA DOCUMENTO ####
	$data_string = '
						{
							"document":{
								"path":"/PARTICIPANTES/'.str_pad($_SESSION['EMP'], 2, '0', STR_PAD_LEFT).'-'.str_pad($_SESSION['RE'], 6, '0', STR_PAD_LEFT).'-'.str_pad($_SESSION['SEQ'], 2, '0', STR_PAD_LEFT).'/'.str_pad($_CD_DOC_ELETRO, 4, '0', STR_PAD_LEFT).'/'.str_replace("-","_",str_replace(" ","_",$_NOME_DOC))."-".str_replace(" ","_",$_SESSION['NOME'])."-".date("YmdHis").'.pdf",
								"content_base64":"data:application/pdf;base64,'.$pdf_64.'",
								"deadline_at":"'.$dt_limite->format('Y-m-d').'T23:59:59-03:00",
								"remind_interval":"2",
								"auto_close":"true",
								"sequence_enabled":"true",
								"signable_group":null,
								"locale":"pt-BR"
							}
						}
	               ';
	echo ($_FL_DEBUG ? "DATA_STRING<br>".$data_string : "");			   
	$ar_doc = execClick($data_string,$_API_URL."/documents?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? "DOCUMENTO<br>" : "");
	echo ($_FL_DEBUG ? print_r($ar_doc,TRUE) : "");
	
	#### INSERE NA TABELA ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital
				     (
						ip, 
						dt_limite,
						cd_empresa, 
						cd_registro_empregado, 
						seq_dependencia,
                        cd_doc,						
						id_doc, 
						json_doc
					 )
				VALUES 
				     (
						'".$_SERVER['REMOTE_ADDR']."',
						TO_TIMESTAMP('".$dt_limite->format('d/m/Y')." 23:59:59','DD/MM/YYYY HH24:MI:SS'),
						".$_SESSION['EMP'].", 
						".$_SESSION['RE'].", 
						".$_SESSION['SEQ'].", 
						".$_CD_DOC_ELETRO.", 
						'".$ar_doc["ARRAY"]['document']['key']."', 
						'".$ar_doc["JSON"]."'					 
					 )
				RETURNING cd_contrato_digital
		      ";
	#echo $qr_sql; 	exit;	  
	$ob_resul = pg_query($db,$qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$_CD_DOC = intval($ar_reg['cd_contrato_digital']);



	echo ($_FL_DEBUG ? "ASSINADOR 1 <BR>" : "");
	#### CRIAR ASSINADOR 1 - PARTICIPANTE ####
	$data_string = '
						{
						  "signer": {
							"email": "'.$_EMAIL.'",
							"phone_number": "'.$_CELULAR.'",
							"auths": ["sms"],
							"name": "'.$_SESSION['NOME'].'",
							"has_documentation": true
						  }
						}	
				   ';
	$ar_ass1 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? print_r($ar_ass1,TRUE) : "");
	
	
	#### ADD ASSINADOR 1 - PARTICIPANTE ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass1["ARRAY"]['signer']['key'].'",
						"sign_as": "sign",
						"group": "1"
					  }
					}	
				   ';	
	$ar_add_ass1 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass1,TRUE) : "");
	
	#### INSERE NA TABELA - ASSINADOR 1 - PARTICIPANTE ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador,
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'P',
						'".$ar_ass1["ARRAY"]['signer']['key']."',
						'".$ar_add_ass1["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass1["ARRAY"]['list']['url']."', 
						'".$ar_add_ass1["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);
	#echo $qr_sql; exit;
	
	######################################################################################################
	if($_REQUEST['fl_conta'] == "T")
	{
		echo ($_FL_DEBUG ? "CORRENTISTA <BR>" : "");
		#### CRIAR CORRENTISTA ####
		$data_string = '
							{
							  "signer": {
								"email": "'.$_EMAIL_CORRENTISTA.'",
								"phone_number": "'.$_CELULAR_CORRENTISTA.'",
								"auths": ["sms"],
								"name": "'.$_REQUEST['nome_correntista'].'",
								"has_documentation": true
							  }
							}	
					   ';
		$ar_ass2 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
		echo ($_FL_DEBUG ? print_r($ar_ass2,TRUE) : "");
		
		#### ADD CORRENTISTA ####
		$data_string = '
						{
						  "list": {
							"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
							"signer_key": "'.$ar_ass2["ARRAY"]['signer']['key'].'",
							"sign_as": "sign",
							"group": "2"
						  }
						}	
					   ';	
		$ar_add_ass2 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
		echo ($_FL_DEBUG ? print_r($ar_add_ass2,TRUE) : "");

		#### INSERE NA TABELA - CORRENTISTA ####
		$qr_sql = "
					INSERT INTO clicksign.contrato_digital_assinatura
						 (
							cd_contrato_digital, 
							tp_assinatura,
							id_assinador, 
							id_assinatura, 
							ds_url_assinatura, 
							json_assinatura
						 )
					VALUES 
						 (
							".$_CD_DOC.",
							'T1',
							'".$ar_ass2["ARRAY"]['signer']['key']."',
							'".$ar_add_ass2["ARRAY"]['list']['request_signature_key']."', 
							'".$ar_add_ass2["ARRAY"]['list']['url']."', 
							'".$ar_add_ass2["JSON"]."'					 
						 )
				  ";
		$ob_resul = pg_query($db,$qr_sql);			
	}
	
	######################################################################################################			   
	echo ($_FL_DEBUG ? print_r("VALIDADOR <BR>",TRUE) : "");
	$email_validador = "ct@familiaprevidencia.com.br";
	
	#### BUSCA SIGNATARIO CADASTRADO ####
	$id_signatario_validador = getSignatario($email_validador, "email");

	if(trim($id_signatario_validador) != "")
	{
		$ar_ass4["ARRAY"]['signer']['key'] = trim($id_signatario_validador);
	}
	else
	{
		#### CRIAR ASSINADOR ####
		$data_string = '
							{
							  "signer": {
								"email": "'.$email_validador.'",
								"auths": ["email"],
								"has_documentation": true
							  }
							}	
					   ';
		$ar_ass4 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);	
	}
	echo ($_FL_DEBUG ? print_r($ar_ass4,TRUE) : "");
	
	#### ADD VALIDADOR - VALIDADOR ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass4["ARRAY"]['signer']['key'].'",
						"sign_as": "validator",
						"group": "3"
					  }
					}	
				   ';				   
	$ar_add_ass4 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass4,TRUE) : "");
	
	#### INSERE NA TABELA - VALIDADOR ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador, 
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'V',
						'".$ar_ass4["ARRAY"]['signer']['key']."',
						'".$ar_add_ass4["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass4["ARRAY"]['list']['url']."', 
						'".$ar_add_ass4["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);		
	
	
	
	#### INSERE NA TABELA ORACLE DE PROTOCOLOS DE DOCUMENTOS DE PARTICIPANTES ####
	$qr_sql = "
				SELECT protocolos_assinatura_docs
				  FROM oracle.protocolos_assinatura_docs(
							".$_SESSION['EMP'].", 
							".$_SESSION['RE'].", 
							".$_SESSION['SEQ'].",
							'".$ar_doc["ARRAY"]['document']['key']."', 
							'".$_NOME_DOC."'
						)				
		      ";
	#echo $qr_sql; 	exit;	  
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		#echo $ds_erro;EXIT;
		echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
				<SCRIPT>
					alert('Ocorreu um erro.\\n\\nEntre em contato pelo 0800 510 2596.');
					document.location.href = 'auto_atendimento_instituidor_forma_pagamento.php';
				</SCRIPT>
			 ";	
		exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
	}	
	
	#### INSERE NA TABELA ORACLE DE CONTROLE DE AUTORIZACAO DE DEBITOS ####
	$qr_sql = "
				SELECT contrib_partic_clicksign
				  FROM oracle.contrib_partic_clicksign(
							".$_SESSION['EMP'].", 
							".$_SESSION['RE'].", 
							".$_SESSION['SEQ'].",
							'".$ar_doc["ARRAY"]['document']['key']."', 
							'BCO',
							".$_REQUEST['vl_contrib_contratada'].",
							41,
							'".$_REQUEST['cd_agencia']."',
							'".$_REQUEST['nr_conta']."',
							'".trim($_REQUEST['fl_conta'])."',
							".(trim($_REQUEST['fl_conta']) == "T" ? str_replace("-","",str_replace(".","",trim($_REQUEST['cpf_correntista']))) : "NULL").",
							".(trim($_REQUEST['fl_conta']) == "T" ? "'".trim($_REQUEST['nome_correntista'])."'" : "NULL")."
						)				
		      ";
	#echo $qr_sql; 	exit;	  
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		#echo $ds_erro;EXIT;
		echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
				<SCRIPT>
					alert('Ocorreu um erro.\\n\\nEntre em contato pelo 0800 510 2596.');
					document.location.href = 'auto_atendimento_instituidor_forma_pagamento.php';
				</SCRIPT>
			 ";	
		exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
	}	
	
	
	#### URL PARA PARTICIPANTE ASSINAR ####
	echo ($_FL_DEBUG ? $ar_add_ass1["ARRAY"]['list']['url']."<br>" : "");
	echo ($_FL_DEBUG ? $ar_add_ass1["ARRAY"]['list']['request_signature_key']."<br>" : "");

	if(!$_FL_DEBUG)
	{
		//ECHO "IR";
		echo '<meta http-equiv="refresh" content="0; url='.$ar_add_ass1["ARRAY"]['list']['url'].'">';
	}

	exit;


	#############################################################################################################################
	function getSignatario($email, $tp_token)
	{
		global $db;
		
		$qr_sql = "
					SELECT ".($tp_token == "email" ? "id_signatario_email": "id_signatario_sms")." AS id_signatario
					  FROM clicksign.signatario
					 WHERE email = TRIM(LOWER('".strtolower(trim($email))."'))
					   AND ".($tp_token == "email" ? "id_signatario_email IS NOT NULL": "id_signatario_sms IS NOT NULL")."
			      ";
		#echo $qr_sql;
		$ob_resul = @pg_query($db, $qr_sql);
		$ar_reg = @pg_fetch_array($ob_resul);
		
		return $ar_reg['id_signatario'];
	}	

?>