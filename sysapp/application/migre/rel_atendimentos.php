<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/tabela_cor.php');	
	require('inc/fpdf153/fpdf.php');
	
	
	
	//echo "<PRE>";	print_r($ar_tabela_cor); 	exit;
	
	function ConvertSize($size=5,$maxsize=0)
	{
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

	class PDF extends FPDF
	{
		var $widths;
		var $aligns;
		var $legends;
		var $wLegend;
		var $sum;
		var $NbVal;		
		var $ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		function novaPagina()
		{
			$this->AddPage();	
			$this->SetTopMargin(15);
			$ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,102,0)));
			$this->RoundedRect(10, 10, 190, 277, 3.50,'1111', 'DF',$ar_estilo, array(255, 255, 255));	
			$this->Image('img/marcadagua.jpg', 63, 153, ConvertSize(500,$ob_pdf->pgwidth), ConvertSize(497,$ob_pdf->pgwidth),'','',false);
			$this->SetX(15);
			$this->SetLineWidth(0);
			$this->SetDrawColor(0,0,0);				
		}		

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
		    $h=5*$nb;
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
		        # function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
				$this->Rect($x,$y,$w,$h);
		        //Print the text
		        $this->MultiCell($w,5,$data[$i],0,$a);
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
		        #$this->AddPage($this->CurOrientation); 
				$this->novaPagina();
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
	                if(ereg('^.+,', $dash))
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
			$this->SetFont('Courier','',10);
			$this->MultiCell($nr_largura, $nr_espaco, date('d')." de ".$this->ar_mes[date('m') -1]." de ".date('Y'), 0, "C");		
			
			//if($this->PageNo() > 1)
			//{
			    $this->SetY(-12);
			    //Arial italic 8
			    $this->SetFont('Courier','I',8);
			    //Page number
			    $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');	
			//}
		}
		
		
		
		function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
		{
			if($cw){
				$d = $b;
				$b = $o - $a;
				$a = $o - $d;
			}else{
				$b += $o;
				$a += $o;
			}
			$a = ($a%360)+360;
			$b = ($b%360)+360;
			if ($a > $b)
				$b +=360;
			$b = $b/360*2*M_PI;
			$a = $a/360*2*M_PI;
			$d = $b-$a;
			if ($d == 0 )
				$d =2*M_PI;
			$k = $this->k;
			$hp = $this->h;
			if($style=='F')
				$op='f';
			elseif($style=='FD' or $style=='DF')
				$op='b';
			else
				$op='s';
			if (sin($d/2))
				$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
			//first put the center
			$this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
			//put the first point
			$this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
			//draw the arc
			if ($d < M_PI/2){
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
			}else{
				$b = $a + $d/4;
				$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
			}
			//terminate drawing
			$this->_out($op);
		}

		function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
		{
			$h = $this->h;
			$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
				$x1*$this->k,
				($h-$y1)*$this->k,
				$x2*$this->k,
				($h-$y2)*$this->k,
				$x3*$this->k,
				($h-$y3)*$this->k));
		}			
		
		function PieChart($w, $h, $data, $format, $colors=null)
		{
			$this->SetFont('Courier', '', 10);
			$this->SetLegends($data,$format);

			$XPage = $this->GetX();
			$YPage = $this->GetY();
			$margin = 2;
			$hLegend = 5;
			$radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
			$radius = floor($radius / 2);
			$XDiag = $XPage + $margin + $radius;
			$YDiag = $YPage + $margin + $radius;
			if($colors == null) {
				for($i = 0;$i < $this->NbVal; $i++) {
					$gray = $i * intval(255 / $this->NbVal);
					$colors[$i] = array($gray,$gray,$gray);
				}
			}

			//Sectors
			$this->SetLineWidth(0.2);
			$angleStart = 0;
			$angleEnd = 0;
			$i = 0;
			foreach($data as $val) {
				$angle = floor(($val * 360) / doubleval($this->sum));
				if ($angle != 0) {
					$angleEnd = $angleStart + $angle;
					$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
					$this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
					$angleStart += $angle;
				}
				$i++;
			}
			if ($angleEnd != 360) {
				$this->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
			}

			//Legends
			$this->SetFont('Courier', '', 10);
			$x1 = $XPage + 2 * $radius + 4 * $margin;
			$x2 = $x1 + $hLegend + $margin;
			$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
			for($i=0; $i<$this->NbVal; $i++) {
				$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
				$this->SetXY($x2,$y1);
				$this->Cell(0,$hLegend,$this->legends[$i]);
				$y1+=$hLegend + $margin;
			}
		}

		function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
		{
			$this->SetFont('Courier', '', 10);
			$this->SetLegends($data,$format);

			$XPage = $this->GetX();
			$YPage = $this->GetY();
			$margin = 2;
			$YDiag = $YPage + $margin;
			$hDiag = floor($h - $margin * 2);
			$XDiag = $XPage + $margin * 2 + $this->wLegend;
			$lDiag = floor($w - $margin * 3 - $this->wLegend);
			if($color == null)
				$color=array(155,155,155);
			if ($maxVal == 0) {
				$maxVal = max($data);
			}
			$valIndRepere = ceil($maxVal / $nbDiv);
			$maxVal = $valIndRepere * $nbDiv;
			$lRepere = floor($lDiag / $nbDiv);
			$lDiag = $lRepere * $nbDiv;
			$unit = $lDiag / $maxVal;
			$hBar = floor($hDiag / ($this->NbVal + 1));
			$hDiag = $hBar * ($this->NbVal + 1);
			$eBaton = floor($hBar * 80 / 100);

			$this->SetLineWidth(0.2);
			$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

			$this->SetFont('Courier', '', 10);
			$this->SetFillColor($color[0],$color[1],$color[2]);
			$i=0;
			foreach($data as $val) {
				//Bar
				$xval = $XDiag;
				$lval = (int)($val * $unit);
				$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
				$hval = $eBaton;
				$this->Rect($xval, $yval, $lval, $hval, 'DF');
				//Legend
				$this->SetXY(0, $yval);
				$this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
				$i++;
			}

			//Scales
			for ($i = 0; $i <= $nbDiv; $i++) {
				$xpos = $XDiag + $lRepere * $i;
				$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
				$val = $i * $valIndRepere;
				$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
				$ypos = $YDiag + $hDiag - $margin;
				$this->Text($xpos, $ypos, $val);
			}
		}

		function SetLegends($data, $format)
		{
			$this->legends=array();
			$this->wLegend=0;
			$this->sum=array_sum($data);
			$this->NbVal=count($data);
			foreach($data as $l=>$val)
			{
				$p=sprintf('%.2f',$val/$this->sum*100).'%';
				$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
				$this->legends[]=$legend;
				$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
			}
		}		

	}


	$nr_largura = 190;
	$nr_espaco = 4;
	$ds_tema = 'pastel';
	
	$ob_pdf = new PDF();
	$ob_pdf->AliasNbPages();
	
	$ob_pdf->novaPagina();
	################################################## CAPA ##################################################
	$qr_sql = "
				SELECT TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS') AS hr_media,
                       COUNT(*) AS qt_atendimento
				  FROM projetos.atendimento a, 
				       projetos.usuarios_controledi u
				 WHERE a.id_atendente = u.codigo 
				   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.indic_ativo IN ('T','P','E')
	          ";	
	$ob_resul = pg_query($db, $qr_sql);
	$ar_capa  = pg_fetch_array($ob_resul);	
	
	#### TEMPO DE ESPERA ####
	$qr_sql = "
				SELECT TO_CHAR(AVG((TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP((a.hora_senha), 'HH24:MI'))),'HH24:MI:SS') AS hr_media,
				       funcoes.converte_segundo_hora(TRUNC(STDDEV(funcoes.converte_hora_segundo((TO_CHAR((TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP((a.hora_senha), 'HH24:MI')),'HH24:MI:SS')))))) AS hr_desvio
				  FROM projetos.atendimento a
				 WHERE DATE_TRUNC('day',dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.hora_senha       <> '00:00' 
				   AND a.indic_ativo      = 'P'
				   AND TRIM(a.hora_senha) <> ''
				   AND a.hora_senha       IS NOT NULL
				   AND TO_TIMESTAMP(TO_CHAR(a.dt_hora_inicio_atendimento,'HH24:MI'), 'HH24:MI') - TO_TIMESTAMP(a.hora_senha, 'HH24:MI') > '00:00:00'::INTERVAL
	          ";	
	$ob_resul = pg_query($db, $qr_sql);
	$ar_tempo_espera  = pg_fetch_array($ob_resul);		
	
	$ob_pdf->SetX(10);
	$ob_pdf->Image('img/img_logo_fundacao_prev7.jpg', 45, 25, ConvertSize(440,$ob_pdf->pgwidth), ConvertSize(95,$ob_pdf->pgwidth),'','',false);
	
	$ob_pdf->SetY(100);
	$ob_pdf->SetFont('Courier','B',22);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Relatório de Atendimentos",0,"C");
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell(190, 4, "____________________________",0,"C");
	
	$ob_pdf->SetFont('Courier','',14);
	$ob_pdf->SetY($ob_pdf->GetY() + 10);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'], 0, "C");	
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Número total de atendimento: ".$ar_capa['qt_atendimento'], 0, "C");
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Média de tempo de atendimento: ".$ar_capa['hr_media'], 0, "C");
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Média de tempo de espera na central: ".$ar_tempo_espera['hr_media'], 0, "C");	
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Desvio padrão de espera na central: ".$ar_tempo_espera['hr_desvio'], 0, "C");	

	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->SetY($ob_pdf->GetY() + 20);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Relatório de responsabilidade da", 0, "C");	
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Gerência de Atendimento ao Participante", 0, "C");	

/*
	$ob_pdf->novaPagina();
	################################################## PAGINA 2 ##################################################
	$ob_pdf->SetXY(10,100);
	$ob_pdf->SetFont('Courier','B',22);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Parte 1",0,"C");
	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Atendimentos pessoais e 0800",0,"C");
	$ob_pdf->SetY($ob_pdf->GetY() + 2);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "_______________________________",0,"C");
*/

	$ob_pdf->novaPagina();
	################################################## ATENDENTE ##################################################
	$qr_sql = "
				SELECT u.id_atendente, 
					   u.guerra,
					   SUM(COALESCE(qt.qt_telefone,0)) AS qt_telefone,
					   SUM(COALESCE(qp.qt_pessoal,0)) AS qt_pessoal,
					   SUM(COALESCE(qe.qt_email,0)) AS qt_email,
					   (SUM(COALESCE(qt.qt_telefone,0)) + SUM(COALESCE(qp.qt_pessoal,0)) + SUM(COALESCE(qe.qt_email,0))) AS qt_total,
					   TO_CHAR(AVG(ht.hr_media_telefone),'HH24:MI:SS') AS hr_media_telefone,
					   TO_CHAR(AVG(hp.hr_media_pessoal),'HH24:MI:SS') AS hr_media_pessoal,
					   TO_CHAR(AVG(he.hr_media_email),'HH24:MI:SS') AS hr_media_email
				  FROM (SELECT a.id_atendente, 
							   u.guerra
						  FROM projetos.atendimento a, 
							   projetos.usuarios_controledi u
						 WHERE a.id_atendente = u.codigo 
						   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						 GROUP BY a.id_atendente, 
								  u.guerra) u
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra,
									COUNT(a.id_atendente) AS qt_telefone
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
							  GROUP BY a.id_atendente, 
									   u.guerra) qt
					ON qt.id_atendente = u.id_atendente		 
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra,
									COUNT(a.id_atendente) AS qt_pessoal
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo = 'P' 				   
							  GROUP BY a.id_atendente, 
									   u.guerra) qp
					ON qp.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra,
									COUNT(a.id_atendente) AS qt_email
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo = 'E' 				   
							  GROUP BY a.id_atendente, 
									   u.guerra) qe
					ON qe.id_atendente = u.id_atendente					
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra, 
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) AS hr_media_telefone
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007)
							  GROUP BY a.id_atendente, 
									   u.guerra) ht
					ON ht.id_atendente = u.id_atendente
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra,
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)  AS hr_media_pessoal
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                = 'P' 
							  GROUP BY a.id_atendente, 
									   u.guerra) hp	
					ON hp.id_atendente = u.id_atendente		 
				  LEFT JOIN (SELECT a.id_atendente, 
									u.guerra,
									AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento)  AS hr_media_email
							   FROM projetos.atendimento a, 
									projetos.usuarios_controledi u
							  WHERE a.id_atendente               = u.codigo 
								AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
								AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
								AND a.indic_ativo                = 'E' 
							  GROUP BY a.id_atendente, 
									   u.guerra) he	
					ON he.id_atendente = u.id_atendente						
				GROUP BY u.id_atendente, 
						 u.guerra
				ORDER BY qt_total DESC
	       ";
	$ob_resul = pg_query($db, $qr_sql);
	$qt_telefone = 0;
	$qt_pessoal  = 0;
	$qt_email    = 0;
	$qt_total    = 0;	
	$ar_media_telefone = Array();
	$ar_media_pessoal  = Array();		
	$ar_media_email    = Array();	
	$ar_grafico = Array();

	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Atendimento por atendente",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");	

	#### CABEÇALHO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(40,20,20,20,20,20,20,20));
	$ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Nome do atendente", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));		
	$ob_pdf->SetFont('Courier','',10);
	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['guerra']] = $ar_reg['qt_total'];
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array($ar_reg['guerra'], 
		                   number_format($ar_reg['qt_total'],0,',','.'), 
						   number_format($ar_reg['qt_telefone'],0,',','.'), 
						   number_format($ar_reg['qt_pessoal'],0,',','.'), 
						   number_format($ar_reg['qt_email'],0,',','.'), 
						   $ar_reg['hr_media_telefone'], 
						   $ar_reg['hr_media_pessoal'], 
						   $ar_reg['hr_media_email']));	
						   
		$qt_telefone += $ar_reg['qt_telefone'];
		$qt_pessoal  += $ar_reg['qt_pessoal'];
		$qt_email    += $ar_reg['qt_email'];
		$qt_total    += $ar_reg['qt_total'];
		if(trim($ar_reg['hr_media_telefone']) != "")
		{
			$ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
		}
		if(trim($ar_reg['hr_media_pessoal']) != "")
		{
			$ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
		}				   
		if(trim($ar_reg['hr_media_email']) != "")
		{
			$ar_media_email[] = strtotime($ar_reg['hr_media_email']);
		}	
	}

	#### TOTALIZADOR ####
	$hr_media_telefone = 0;
	if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
	{
		$hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
	}

	$hr_media_pessoal = 0;
	if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
	{
		$hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
	}

	$hr_media_email = 0;
	if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
	{
		$hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
	}

	$ob_pdf->SetX(15);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Total", 
					   number_format($qt_total,0,',','.'), 
					   number_format($qt_telefone,0,',','.'), 
					   number_format($qt_pessoal,0,',','.'), 
					   number_format($qt_email,0,',','.'), 
					   $hr_media_telefone, 
					   $hr_media_pessoal, 
					   $hr_media_email));	

	#### GRAFICO ####
	$ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);	
	$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);		


	$ob_pdf->novaPagina();
	################################################## HORÁRIO ##################################################
	$qr_sql = "
				SELECT h.hr_ini,
					   (SUM(h.qt_pessoal) + SUM(h.qt_telefone) + SUM(h.qt_email)) AS qt_total,
				       SUM(h.qt_pessoal) AS qt_pessoal,
				       SUM(h.hr_media_pessoal) AS hr_media_pessoal,
				       SUM(h.qt_telefone) AS qt_telefone,
				       SUM(h.hr_media_telefone) AS hr_media_telefone,
				       SUM(h.qt_email) AS qt_email,
				       SUM(h.hr_media_email) AS hr_media_email       
				  FROM (
						-- ATENDIMENTOS PESSOAL
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       COUNT(a.id_atendente) AS qt_pessoal,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::TIME AS hr_media_pessoal,
						       0 AS qt_telefone,
						       '00:00:00'::TIME AS hr_media_telefone,
						       0 AS qt_email,
						       '00:00:00'::TIME AS hr_media_email 
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'P' 				   
						 GROUP BY hr_ini

						 UNION

						-- ATENDIMENTO TELEFONE
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       0 AS qt_pessoal,
						       '00:00:00'::TIME AS hr_media_pessoal,
						       COUNT(a.id_atendente) AS qt_telefone,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::TIME AS hr_media_telefone,
						       0 AS qt_email,
						       '00:00:00'::TIME AS hr_media_email   
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL						   
						   AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007) 				   
						 GROUP BY hr_ini

						UNION

						-- ATENDIMENTOS EMAIL
						SELECT TO_CHAR(DATE_TRUNC('hour',a.dt_hora_inicio_atendimento),'HH24:MI') AS hr_ini,
						       0 AS qt_pessoal,
						       '00:00:00'::TIME AS hr_media_pessoal,
						       0 AS qt_telefone,
						       '00:00:00'::TIME AS hr_media_telefone,
						       COUNT(a.id_atendente) AS qt_email,
						       TO_CHAR(AVG(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento),'HH24:MI:SS')::TIME AS hr_media_email     
						  FROM projetos.atendimento a
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'E' 				   
						 GROUP BY hr_ini
				       ) h
				 GROUP BY h.hr_ini
				 ORDER BY h.hr_ini
	       ";
	$ob_resul = pg_query($db, $qr_sql);
	$qt_telefone = 0;
	$qt_pessoal  = 0;
	$qt_email    = 0;
	$qt_total    = 0;	
	$ar_media_telefone = Array();
	$ar_media_pessoal  = Array();		
	$ar_media_email    = Array();	
	$ar_grafico = Array();

	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Atendimento por horário",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");	

	#### CABEÇALHO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(40,20,20,20,20,20,20,20));
	$ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Horário", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));		
	$ob_pdf->SetFont('Courier','',10);
	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['hr_ini']] = $ar_reg['qt_total'];
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array($ar_reg['hr_ini'], 
		                   number_format($ar_reg['qt_total'],0,',','.'), 
						   number_format($ar_reg['qt_telefone'],0,',','.'), 
						   number_format($ar_reg['qt_pessoal'],0,',','.'), 
						   number_format($ar_reg['qt_email'],0,',','.'), 
						   ($ar_reg['hr_media_telefone'] == "00:00:00" ? "" : $ar_reg['hr_media_telefone']), 
						   ($ar_reg['hr_media_pessoal'] == "00:00:00" ? "" : $ar_reg['hr_media_pessoal']), 
						   ($ar_reg['hr_media_email'] == "00:00:00" ? "" : $ar_reg['hr_media_email'])));	
						   
		$qt_telefone += $ar_reg['qt_telefone'];
		$qt_pessoal  += $ar_reg['qt_pessoal'];
		$qt_email    += $ar_reg['qt_email'];
		$qt_total    += $ar_reg['qt_total'];
		if(trim($ar_reg['hr_media_telefone']) != "00:00:00")
		{
			$ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
		}
		if(trim($ar_reg['hr_media_pessoal']) != "00:00:00")
		{
			$ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
		}						   
		if(trim($ar_reg['hr_media_email']) != "00:00:00")
		{
			$ar_media_email[] = strtotime($ar_reg['hr_media_email']);
		}		
	}
	
	#### TOTALIZADOR ####
	$hr_media_telefone = 0;
	if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
	{
		$hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
	}

	$hr_media_pessoal = 0;
	if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
	{
		$hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
	}	

	$hr_media_email = 0;
	if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
	{
		$hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
	}
	
	$ob_pdf->SetX(15);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Total", 
					   number_format($qt_total,0,',','.'), 
					   number_format($qt_telefone,0,',','.'), 
					   number_format($qt_pessoal,0,',','.'), 
					   number_format($qt_email,0,',','.'), 
					   $hr_media_telefone, 
					   $hr_media_pessoal, 
					   $hr_media_email));	

	#### GRAFICO ####
	$ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);	
	$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);	

	$ob_pdf->novaPagina();
	################################################## TIPO DE ATENDIMENTO ##################################################
	$ar_grafico = Array();
	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Resultados por tipo de Atendimento",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		

	$qr_sql = "
				SELECT a1.indic_ativo,
				       CASE WHEN (a1.indic_ativo = 'T') THEN 'Telefônico'
					        WHEN (a1.indic_ativo = 'P') THEN 'Pessoal'
							WHEN (a1.indic_ativo = 'C') THEN 'Administrativo'
							WHEN (a1.indic_ativo = 'E') THEN 'E-mail'
				       END AS ds_tipo_atendimento,
				       COALESCE(ta.qt_total_avulso,0) AS qt_avulso,
				       COALESCE(tp.qt_total_normal,0) AS qt_normal,
				       COALESCE(tn.qt_total_nao_partipante,0) AS qt_nao_partipante
				  FROM (SELECT DISTINCT(a.indic_ativo) AS indic_ativo
				          FROM projetos.atendimento a
				         WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				           AND a.indic_ativo IN ('T','P','C','E')) a1
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_avulso
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'A'
					     GROUP BY a.indic_ativo) ta
				    ON ta.indic_ativo = a1.indic_ativo
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_normal
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'P'
					     GROUP BY a.indic_ativo) tp
				    ON tp.indic_ativo = a1.indic_ativo  
				  LEFT JOIN(SELECT a.indic_ativo,
					               COUNT(*) AS qt_total_nao_partipante
					      FROM projetos.atendimento a
					     WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
					       AND a.origem_atendimento = 'N'
					     GROUP BY a.indic_ativo) tn
				    ON tn.indic_ativo = a1.indic_ativo  
                 ORDER BY ds_tipo_atendimento ASC
	          ";	
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(50,28,28,28,40));
	$ob_pdf->SetAligns(array('L','C','C','C','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Tipo de atendimento", "Total", "Avulso", "Normal", "Não Participante"));		
	$ob_pdf->SetFont('Courier','',10);
	$qt_avulso         = 0;
	$qt_normal         = 0;
	$qt_nao_partipante = 0;	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['ds_tipo_atendimento']] = $ar_reg['qt_avulso'] + $ar_reg['qt_normal'] + $ar_reg['qt_nao_partipante'];
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array($ar_reg['ds_tipo_atendimento'], 
		                   number_format($ar_reg['qt_avulso'] + $ar_reg['qt_normal'] + $ar_reg['qt_nao_partipante'],0,',','.'), 
						   number_format($ar_reg['qt_avulso'],0,',','.'), 
						   number_format($ar_reg['qt_normal'],0,',','.'), 
						   number_format($ar_reg['qt_nao_partipante'],0,',','.')));	
						   
		$qt_avulso         += $ar_reg['qt_avulso'];
		$qt_normal         += $ar_reg['qt_normal'];
		$qt_nao_partipante += $ar_reg['qt_nao_partipante'];
	}
	
	#### TOTALIZADOR ####
	$ob_pdf->SetX(15);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('Total', 
					   number_format($qt_avulso + $qt_normal + $qt_nao_partipante,0,',','.'), 
					   number_format($qt_avulso,0,',','.'), 
					   number_format($qt_normal,0,',','.'), 
					   number_format($qt_nao_partipante,0,',','.')));	
	#### GRAFICO ####
	$ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);	
	$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);	
					   
				   

	$ob_pdf->novaPagina();
	################################################## PROGRAMAS ##################################################
	$ar_grafico = Array();
	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Resultados por Programa",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		

	$qr_sql = " 
				SELECT lt.descricao AS tp_programa,
				       COUNT(*) AS qt_programa,
				       TO_CHAR(AVG(hr_tempo),'HH24:MI:SS') AS qt_tempo
				  FROM projetos.atendimento_tela_capturada atc	
				  JOIN projetos.atendimento a
			            ON a.cd_atendimento = atc.cd_atendimento
				  JOIN projetos.telas_programas tp
			            ON tp.cd_tela = atc.cd_tela
				  JOIN public.listas lt
				    ON lt.codigo = tp.cd_programa_fceee
				   AND lt.categoria = 'PRFC'			  
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.indic_ativo IN ('T','P','E')
				 GROUP BY tp_programa
				 ORDER BY qt_programa DESC				 
		      ";	
	
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(45, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(50,28,40));
	$ob_pdf->SetAligns(array('L','C','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Programa", "Total de acesso", "Tempo Médio por Atendimento"));		
	$ob_pdf->SetFont('Courier','',10);
	$qt_programa         = 0;
	$ar_media_tempo = Array();
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico['TOTAL'][$ar_reg['tp_programa']] = $ar_reg['qt_programa'];
		
		if($ar_reg['tp_programa'] != "Cadastro")
		{
			$ar_grafico['OUTROS'][$ar_reg['tp_programa']] = $ar_reg['qt_programa'];
		}
		
		#### LINHAS ####
		$ob_pdf->SetX(45);
		$ob_pdf->Row(array($ar_reg['tp_programa'], 
						   number_format($ar_reg['qt_programa'],0,',','.'), 
						   $ar_reg['qt_tempo']));	
						   
		$qt_programa         += $ar_reg['qt_programa'];
		if(trim($ar_reg['qt_tempo']) != "")
		{
			$ar_media_tempo[] = strtotime($ar_reg['qt_tempo']);
		}		
		
	}
	
	#### TOTALIZADOR ####
	$hr_media_tempo = 0;
	if((count($ar_media_tempo) > 0) and (array_sum($ar_media_tempo) > 0))
	{
		$hr_media_tempo = date("H:i:s", array_sum($ar_media_tempo)/count($ar_media_tempo));
	}	
	$ob_pdf->SetX(45);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('Total', 
					   number_format($qt_programa ,0,',','.'), 
					   $hr_media_tempo));		
	
	$ob_pdf->SetFont('Courier','',10);
	
	#### GRAFICO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 10);	
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Com cadastro:",0);
	$ob_pdf->SetXY(50, $ob_pdf->GetY() );	
	$ob_pdf->PieChart(150, 60, $ar_grafico['TOTAL'], '%l (%p)', $AR_THEMA[$ds_tema]);				   

	#### GRAFICO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 20);	
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Sem cadastro:",0);
	$ob_pdf->SetXY(50, $ob_pdf->GetY());	
	$ob_pdf->PieChart(150, 60, $ar_grafico['OUTROS'], '%l (%p)', $AR_THEMA[$ds_tema]);	
	
	$ob_pdf->novaPagina();
	################################################## PROGRAMA / TIPO ATENDIMENTO ##################################################
	$qr_sql = "
				SELECT h.tp_programa,
					   (SUM(h.qt_pessoal) + SUM(h.qt_telefone) + SUM(h.qt_email)) AS qt_total,
				       SUM(h.qt_pessoal) AS qt_pessoal,
				       SUM(h.hr_media_pessoal) AS hr_media_pessoal,
				       SUM(h.qt_telefone) AS qt_telefone,
				       SUM(h.hr_media_telefone) AS hr_media_telefone,
				       SUM(h.qt_email) AS qt_email,
				       SUM(h.hr_media_email) AS hr_media_email       
				  FROM (
						-- ATENDIMENTOS PESSOAL
						 SELECT lt.descricao AS tp_programa,
								COUNT(*) AS qt_pessoal,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_pessoal,
								0 AS qt_telefone,
								'00:00:00'::TIME AS hr_media_telefone,
								0 AS qt_email,
								'00:00:00'::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'P'
						 GROUP BY tp_programa

						UNION

						-- ATENDIMENTOS TELEFONE
						 SELECT lt.descricao AS tp_programa,
								0 AS qt_pessoal,
								'00:00:00'::TIME AS hr_media_pessoal,
								COUNT(*) AS qt_telefone,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_telefone,
								0 AS qt_email,
								'00:00:00'::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo IN ('a','T') -- contar Avulsos como telefone (MARCUS/GAP - 09/07/2007) 
						 GROUP BY tp_programa

						UNION

						-- ATENDIMENTOS EMAIL
						 SELECT lt.descricao AS tp_programa,
								0 AS qt_pessoal,
								'00:00:00'::TIME AS hr_media_pessoal,
								0 AS qt_telefone,
								'00:00:00'::TIME AS hr_media_telefone,
								COUNT(*) AS qt_email,
								TO_CHAR(AVG(hr_tempo),'HH24:MI:SS')::TIME AS hr_media_email 
						  FROM projetos.atendimento_tela_capturada atc	
						  JOIN projetos.atendimento a
							ON a.cd_atendimento = atc.cd_atendimento
						  JOIN projetos.telas_programas tp
							ON tp.cd_tela = atc.cd_tela
						  JOIN public.listas lt
							ON lt.codigo = tp.cd_programa_fceee
						   AND lt.categoria = 'PRFC'			  
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
						   AND a.indic_ativo = 'E'
						 GROUP BY tp_programa
				       ) h
				 GROUP BY h.tp_programa
				 ORDER BY h.tp_programa	
	       ";
	$ob_resul = pg_query($db, $qr_sql);
	$qt_telefone = 0;
	$qt_pessoal  = 0;
	$qt_email    = 0;
	$qt_total    = 0;	
	$ar_media_telefone = Array();
	$ar_media_pessoal  = Array();		
	$ar_media_email    = Array();	
	$ar_grafico       = Array();

	
	$ob_pdf->SetXY(10,25);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Atendimento por Programa",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");	

	#### CABEÇALHO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(40,20,20,20,20,20,20,20));
	$ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Horário", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));		
	$ob_pdf->SetFont('Courier','',10);
	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico['TELEFONE'][$ar_reg['tp_programa']] = $ar_reg['qt_telefone'];
		$ar_grafico['PESSOAL'][$ar_reg['tp_programa']] = $ar_reg['qt_pessoal'];
		$ar_grafico['EMAIL'][$ar_reg['tp_programa']] = $ar_reg['qt_email'];
		
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array($ar_reg['tp_programa'], 
		                   number_format($ar_reg['qt_total'],0,',','.'), 
						   number_format($ar_reg['qt_telefone'],0,',','.'), 
						   number_format($ar_reg['qt_pessoal'],0,',','.'), 
						   number_format($ar_reg['qt_email'],0,',','.'), 
						   ($ar_reg['hr_media_telefone'] == "00:00:00" ? "" : $ar_reg['hr_media_telefone']), 
						   ($ar_reg['hr_media_pessoal'] == "00:00:00" ? "" : $ar_reg['hr_media_pessoal']), 
						   ($ar_reg['hr_media_email'] == "00:00:00" ? "" : $ar_reg['hr_media_email'])));	
						   
		$qt_telefone += $ar_reg['qt_telefone'];
		$qt_pessoal  += $ar_reg['qt_pessoal'];
		$qt_email    += $ar_reg['qt_email'];
		$qt_total    += $ar_reg['qt_total'];
		if(trim($ar_reg['hr_media_telefone']) != "00:00:00")
		{
			$ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
		}
		if(trim($ar_reg['hr_media_pessoal']) != "00:00:00")
		{
			$ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
		}						   
		if(trim($ar_reg['hr_media_email']) != "00:00:00")
		{
			$ar_media_email[] = strtotime($ar_reg['hr_media_email']);
		}		
	}
	
	#### TOTALIZADOR ####
	$hr_media_telefone = 0;
	if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
	{
		$hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
	}

	$hr_media_pessoal = 0;
	if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
	{
		$hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
	}	

	$hr_media_email = 0;
	if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
	{
		$hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
	}
	
	$ob_pdf->SetX(15);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Total", 
					   number_format($qt_total,0,',','.'), 
					   number_format($qt_telefone,0,',','.'), 
					   number_format($qt_pessoal,0,',','.'), 
					   number_format($qt_email,0,',','.'), 
					   $hr_media_telefone, 
					   $hr_media_pessoal, 
					   $hr_media_email));	

				 
	$ob_pdf->SetFont('Courier','',10);
					 
	#### GRAFICO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 10);	
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Telefone:",0);
	$ob_pdf->SetXY(50, $ob_pdf->GetY() );	
	$ob_pdf->PieChart(150, 60, $ar_grafico['TELEFONE'], '%l (%p)', $AR_THEMA[$ds_tema]);				   

	#### GRAFICO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 20);	
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Pessoal:",0);
	$ob_pdf->SetXY(50, $ob_pdf->GetY());	
	$ob_pdf->PieChart(150, 60, $ar_grafico['PESSOAL'], '%l (%p)', $AR_THEMA[$ds_tema]);				   	

	#### GRAFICO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 20);	
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Email:",0);
	$ob_pdf->SetXY(50, $ob_pdf->GetY());	
	$ob_pdf->PieChart(150, 60, $ar_grafico['EMAIL'], '%l (%p)', $AR_THEMA[$ds_tema]);	
	
	$ob_pdf->novaPagina();	
	################################################## EMPRESAS/PLANOS ##################################################
	$ar_grafico = Array();
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Resultados por Empresas/Planos",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");

	$qr_sql = "

		SELECT

			patroc.sigla, ultimo_plano.descricao, count(*) as total

		FROM

			projetos.atendimento a

			JOIN public.participantes p 
			ON a.cd_empresa=p.cd_empresa 
			AND a.cd_registro_empregado=p.cd_registro_empregado 
			AND a.seq_dependencia=p.seq_dependencia

			JOIN 
			(
				SELECT planos.cd_plano, planos.descricao, tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia
				FROM public.titulares_planos tp JOIN public.planos planos ON tp.cd_plano=planos.cd_plano
				WHERE 
				tp.dt_ingresso_plano = ( SELECT MAX(dt_ingresso_plano) FROM public.titulares_planos WHERE cd_empresa=tp.cd_empresa AND cd_registro_empregado=tp.cd_registro_empregado AND seq_dependencia=tp.seq_dependencia )
			) AS ultimo_plano
			ON ultimo_plano.cd_empresa=a.cd_empresa and ultimo_plano.cd_registro_empregado=a.cd_registro_empregado and ultimo_plano.seq_dependencia=a.seq_dependencia

			JOIN public.patrocinadoras patroc ON patroc.cd_empresa=ultimo_plano.cd_empresa

		WHERE

			DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
			AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
			AND a.indic_ativo IN ('T','P','E')

		GROUP BY patroc.sigla, ultimo_plano.descricao

		ORDER BY patroc.sigla;

	";
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(40, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(52,50,28));
	$ob_pdf->SetAligns(array('L','L', 'R'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Empresa", "Plano", "Total"));		
	$ob_pdf->SetFont('Courier','',10);
	$qt_total = 0;
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['sigla'] . '-' . $ar_reg['descricao'] ] = $ar_reg['total'];
		#### LINHAS ####
		$ob_pdf->SetX(40);
		$ob_pdf->Row(array($ar_reg['sigla'], $ar_reg['descricao'], 
						   number_format($ar_reg['total'],0,',','.')));	
						   
		$qt_total         += $ar_reg['total'];
	}
	#### TOTALIZADOR ####
	$ob_pdf->SetX(40);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('', 'Total', 
					   $qt_total));
	
	if(pg_num_rows($ob_resul) > 1)
	{
		#### GRAFICO ####
		$ob_pdf->SetXY(30, $ob_pdf->GetY() + 30);	
		$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);						   
	}

	$ob_pdf->novaPagina();
	################################################## RECLAMAÇÕES #############################################
	$ar_grafico = Array();
	
	$ob_pdf->SetXY(10,15);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Reclamações",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		
	
	#### TABELA ####
	$qr_sql = " 
				SELECT t.ds_programa,
					   SUM(t.qt_total) AS qt_total
				  FROM (SELECT l.descricao AS ds_programa,
							   COUNT(*) AS qt_total
						  FROM projetos.atendimento a, 
							   projetos.atendimento_reclamacao ar,
							   public.listas l
						 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND a.cd_atendimento = ar.cd_atendimento
						   AND l.codigo         = ar.cd_programa_institucional
						   AND l.categoria      = 'PRFC'
						 GROUP BY ds_programa

						 UNION

						SELECT rp.ds_reclamacao_programa AS ds_programa,
							   COUNT(*) AS qt_total
						  FROM projetos.reclamacao r
						  JOIN projetos.reclamacao_programa rp
							ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
						 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
						   AND r.dt_exclusao IS NULL
						   AND r.tipo        = 'R'
						 GROUP BY rp.ds_reclamacao_programa) AS t
					 GROUP BY ds_programa
					 ORDER BY qt_total DESC				 
		      ";	
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(52,28));
	$ob_pdf->SetAligns(array('L','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Programa", "Total"));		
	$ob_pdf->SetFont('Courier','',10);
	$qt_total         = 0;
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['ds_programa']] = $ar_reg['qt_total'];
		#### LINHAS ####
		$ob_pdf->SetX(65);
		$ob_pdf->Row(array($ar_reg['ds_programa'], 
						   number_format($ar_reg['qt_total'],0,',','.')));	
						   
		$qt_total         += $ar_reg['qt_total'];
	}	
	#### TOTALIZADOR ####
	$ob_pdf->SetX(65);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('Total', 
					   $qt_total));		
	
	if(pg_num_rows($ob_resul) > 1)
	{
		#### GRAFICO ####
		$ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);	
		$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);						   
	}
	
	#### LISTA ####
	$qr_sql = " 
				SELECT a.cd_atendimento, 
				       a.cd_empresa, 
				       a.cd_registro_empregado, 
				       a.seq_dependencia, 
				       ar.texto_reclamacao AS obs,
				       l.descricao AS ds_programa,
				       TO_CHAR(arr.dt_retorno,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.atendimento a 
				  JOIN projetos.atendimento_reclamacao ar
				    ON ar.cd_atendimento = a.cd_atendimento
				  JOIN public.listas l
				    ON l.codigo         = ar.cd_programa_institucional
				   AND l.categoria      = 'PRFC'
				  LEFT JOIN projetos.atendimento_retorno arr
				    ON arr.cd_atendimento = a.cd_atendimento
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')

				 UNION

				SELECT r.cd_atendimento,
				       r.cd_empresa,
				       r.cd_registro_empregado,
				       r.seq_dependencia,
				       TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo || ': ' || r.descricao,
				       rp.ds_reclamacao_programa AS ds_programa,
				       TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
				    ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
			      LEFT JOIN projetos.reclamacao_andamento ran
				    ON ran.numero                  = r.numero
				   AND ran.ano                     = r.ano
				   AND ran.tipo                    = r.tipo
				   AND ran.tp_reclamacao_andamento = 'R'
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo = 'R'
				 ORDER BY ds_programa ASC,
                          cd_atendimento ASC				 
		      ";	
	$ob_resul = pg_query($db, $qr_sql);	
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 30);
	$ds_programa = "";
	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);
		$ob_pdf->SetWidths(array(28,28,96,28));
		$ob_pdf->SetAligns(array('L','C','J','C'));
		
		if($ds_programa != $ar_reg['ds_programa'])
		{
			#### TITULO ####
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->SetXY(15, $ob_pdf->GetY() + 2);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, $ar_reg['ds_programa'],0,"C");
			#### CABEÇALHO ####
			$ob_pdf->SetFont('Courier','B',10);
			$ob_pdf->SetX(15);
			$ob_pdf->Row(array("Atendimento", "Participante", "Reclamação", "Retorno"));	
			$ds_programa = $ar_reg['ds_programa'];			
		}
		
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);		
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Row(array($ar_reg['cd_atendimento'], 
						   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
						   $ar_reg['obs'],
						   $ar_reg['dt_retorno']));	
	}	
	
	$ob_pdf->novaPagina();
	################################################## SUGESTOES #############################################
	$ar_grafico = Array();
	
	$ob_pdf->SetXY(10,15);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Sugestões",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		
	
	#### TABELA ####
	$qr_sql = " 
				SELECT rp.ds_reclamacao_programa AS ds_programa,
					   COUNT(*) AS qt_total
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
					ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo        = 'S'
				 GROUP BY rp.ds_reclamacao_programa
				 ORDER BY qt_total DESC				 
		      ";	
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(52,28));
	$ob_pdf->SetAligns(array('L','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Programa", "Total"));		
	$ob_pdf->SetFont('Courier','',10);
	$qt_total         = 0;
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['ds_programa']] = $ar_reg['qt_total'];
		#### LINHAS ####
		$ob_pdf->SetX(65);
		$ob_pdf->Row(array($ar_reg['ds_programa'], 
						   number_format($ar_reg['qt_total'],0,',','.')));	
						   
		$qt_total         += $ar_reg['qt_total'];
	}	
	#### TOTALIZADOR ####
	$ob_pdf->SetX(65);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('Total', 
					   $qt_total));		
	
	if(pg_num_rows($ob_resul) > 1)
	{
		#### GRAFICO ####
		$ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);	
		$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);						   
	}
	
	#### LISTA ####
	$qr_sql = " 
				SELECT r.cd_atendimento,
				       r.cd_empresa,
				       r.cd_registro_empregado,
				       r.seq_dependencia,
				       TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo || ': ' || r.descricao,
				       rp.ds_reclamacao_programa AS ds_programa,
				       TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno
				  FROM projetos.reclamacao r
				  JOIN projetos.reclamacao_programa rp
				    ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
			      LEFT JOIN projetos.reclamacao_andamento ran
				    ON ran.numero                  = r.numero
				   AND ran.ano                     = r.ano
				   AND ran.tipo                    = r.tipo
				   AND ran.tp_reclamacao_andamento = 'S'
				 WHERE DATE_TRUNC('day',r.dt_inclusao) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND r.dt_exclusao IS NULL
				   AND r.tipo = 'S'
				 ORDER BY ds_programa ASC,
                          cd_atendimento ASC				 
		      ";	
	$ob_resul = pg_query($db, $qr_sql);	
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 30);
	$ds_programa = "";
	
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);
		$ob_pdf->SetWidths(array(28,28,96,28));
		$ob_pdf->SetAligns(array('L','C','J','C'));
		
		if($ds_programa != $ar_reg['ds_programa'])
		{
			#### TITULO ####
			$ob_pdf->SetFont('Courier','B',12);
			$ob_pdf->SetXY(15, $ob_pdf->GetY() + 2);
			$ob_pdf->MultiCell($nr_largura, $nr_espaco, $ar_reg['ds_programa'],0,"C");
			#### CABEÇALHO ####
			$ob_pdf->SetFont('Courier','B',10);
			$ob_pdf->SetX(15);
			$ob_pdf->Row(array("Atendimento", "Participante", "Sugestão", "Retorno"));	
			$ds_programa = $ar_reg['ds_programa'];			
		}
		
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);		
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Row(array($ar_reg['cd_atendimento'], 
						   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
						   $ar_reg['obs'],
						   $ar_reg['dt_retorno']));	
	}		
	
	$ob_pdf->novaPagina();
	################################################## ENCAMINHAMENTOS ##################################################
	$ob_pdf->SetXY(10,15);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Encaminhamentos",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		
	
	#### LISTA ####
	$qr_sql = " 
				SELECT a.cd_atendimento, 
				       a.cd_empresa, 
				       a.cd_registro_empregado, 
				       a.seq_dependencia, 
				       ae.texto_encaminhamento AS obs
				  FROM projetos.atendimento a,  
				       projetos.atendimento_encaminhamento ae
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND a.cd_atendimento = ae.cd_atendimento
				ORDER BY a.cd_atendimento
		      ";	
	$ob_resul = pg_query($db, $qr_sql);	

	#### TITULO ####
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 2);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Total de encaminhamentos: ".pg_num_rows($ob_resul),0,"C");	
	
	#### CABEÇALHO ####
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(28,28,124));
	$ob_pdf->SetAligns(array('L','C','J'));	
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->Row(array("Atendimento", "Participante", "Encaminhamento"));	
	$ds_programa = $ar_reg['ds_programa'];		
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);		
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Row(array($ar_reg['cd_atendimento'], 
						   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
						   $ar_reg['obs']));	
	}
	
	$ob_pdf->novaPagina();
	################################################## ENCAMINHAMENTOS PARA GERENCIAS ##################################################
	$ob_pdf->SetXY(10,15);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Encaminhamentos para Gerências",0,"C");
	$ob_pdf->SetFont('Courier','',10);
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "(Período entre ".$_REQUEST['dt_inicial']." e ".$_REQUEST['dt_final'].")", 0, "C");		
	
	#### TABELA ####
	$qr_sql = " 
				SELECT a.area,
				       COUNT(*) AS qt_area
				  FROM projetos.atividades a, 
				       listas l
				 WHERE DATE_TRUNC('day',dt_cad) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND a.divisao = 'GAP'
				   AND a.cd_registro_empregado IS NOT NULL
				   AND a.cd_registro_empregado > 0
				   AND a.status_atual = l.codigo
				   AND l.categoria = 'STAT'
				 GROUP BY a.area
				 ORDER BY qt_area DESC
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(52,28));
	$ob_pdf->SetAligns(array('L','C'));
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array("Gerência", "Total"));
	$ob_pdf->SetFont('Courier','',10);
	$qt_total         = 0;
	$ar_grafico       = Array();
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		$ar_grafico[$ar_reg['area']] = $ar_reg['qt_area'];
		#### LINHAS ####
		$ob_pdf->SetX(65);
		$ob_pdf->Row(array($ar_reg['area'],
						   number_format($ar_reg['qt_area'],0,',','.')));

		$qt_total         += $ar_reg['qt_area'];
	}	
	#### TOTALIZADOR ####
	$ob_pdf->SetX(65);
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->Row(array('Total', 
					   $qt_total));

	if(pg_num_rows($ob_resul) > 1)
	{
		#### GRAFICO ####
		$ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);	
		$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);						   
	}

	#### LISTA ####
	$qr_sql = " 
				SELECT a.numero,
				       TO_CHAR(a.dt_cad,'DD/MM/YYYY HH24:MI') AS dt_cadastro,
				       TRIM(a.descricao) AS descricao,
				       l.descricao AS status,
				       a.cd_empresa,
				       a.cd_registro_empregado,
				       a.cd_sequencia,
					   a.area
				  FROM projetos.atividades a, 
				       listas l
				 WHERE DATE_TRUNC('day',dt_cad) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND a.divisao = 'GAP'
				   AND a.cd_registro_empregado IS NOT NULL
				   AND a.cd_registro_empregado > 0
				   AND a.status_atual = l.codigo
				   AND l.categoria = 'STAT'
				 ORDER BY numero
		      ";	
	$ob_resul = pg_query($db, $qr_sql);	
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 25);

	#### CABEÇALHO ####
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(28,28,20,104));
	$ob_pdf->SetAligns(array('L','C','C','J'));	
	$ob_pdf->SetFont('Courier','B',10);
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 5);
	$ob_pdf->Row(array("Atividade", "Participante", "Gerência", "Encaminhamento"));	
	$ds_programa = $ar_reg['ds_programa'];		
	while ($ar_reg = pg_fetch_array($ob_resul))
	{
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0,0,0);		
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Row(array($ar_reg['numero'], 
						   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
						   $ar_reg['area'],
						   $ar_reg['descricao']));	
	}			
	
	$ob_pdf->Output();

?>