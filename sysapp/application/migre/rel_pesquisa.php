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
		
		function Header()
		{
			$this->SetTopMargin(15);
			$ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,102,0)));
			$this->RoundedRect(10, 10, 190, 277, 3.50,'1111', 'DF',$ar_estilo, array(255, 255, 255));	
			$this->Image('img/marcadagua.jpg', 63, 153, ConvertSize(500,$ob_pdf->pgwidth), ConvertSize(497,$ob_pdf->pgwidth),'','',false);
			$this->SetX(15);
			$this->SetLineWidth(0);
			$this->SetDrawColor(0,0,0);		
		}
		
		function novaPagina()
		{
			
			$this->AddPage();	
		
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
		    if($this->GetY()+$h > $this->PageBreakTrigger)
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
		    //Select Arial italic 8
		    $this->SetFont('Courier','I',8);
		    //Print current and total page numbers
		    //$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
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

	$ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$nr_largura = 190;
	$nr_espaco = 4;
	$ds_tema = 'pastel';
	
	$ob_pdf = new PDF();
	
	$ob_pdf->novaPagina();
	################################################## CAPA ##################################################
	$qr_sql = "
				SELECT e.cd_enquete, 
				       e.titulo, 
					   e.texto_abertura, 
					   TO_CHAR(e.dt_inicio, 'DD/MM/YYYY HH24:MI') AS dt_inicio, 
					   TO_CHAR(e.dt_fim, 'DD/MM/YYYY HH24:MI') AS dt_final, 
					   e.cd_site, 
					   e.cd_responsavel, 
					   e.cd_evento_institucional, 
					   e.cd_publicacao, 
					   e.imagem, 
					   e.controle_respostas,
					   u.guerra, 
					   u.divisao, 
					   d.nome AS nome_divisao, 
					   e.nr_publico_total,
					   (

                        SELECT COUNT(DISTINCT(er.ip)) 
					      FROM projetos.enquete_resultados er
 					     WHERE er.cd_enquete = e.cd_enquete 
						   --AND er.ip not like ('%.%')
                    {ANDWHERE}
                       ) AS qt_resposta
			      FROM projetos.enquetes e, 
				       projetos.usuarios_controledi u, 
					   projetos.divisoes d
			     WHERE e.cd_enquete             = ".$_REQUEST['c']." 
				   AND e.cd_responsavel         = u.codigo 
				   AND e.cd_divisao_responsavel = d.codigo
	          ";

    $where = "";
    if ($_SESSION["filtro_data_inicio"]!="")
    {
        $where = "
                   AND DATE_TRUNC('day', er.dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                          AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
        ";
    }
    $qr_sql = str_replace( "{ANDWHERE}", $where, $qr_sql );

    $ob_resul = pg_query($db, $qr_sql);
	$ar_capa  = pg_fetch_array($ob_resul);	
	
	$ob_pdf->SetX(10);
	$ob_pdf->Image('img/img_logo_fundacao_prev7.jpg', 45, 25, ConvertSize(440,$ob_pdf->pgwidth), ConvertSize(95,$ob_pdf->pgwidth),'','',false);
	
	$ob_pdf->SetY(100);
	$ob_pdf->SetFont('Courier','B',22);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Resultado da Pesquisa",0,"C");
	$ob_pdf->SetY($ob_pdf->GetY() + 15);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco + 4, $ar_capa['titulo'],0,"C");
	$ob_pdf->SetY($ob_pdf->GetY());
	$ob_pdf->MultiCell(190, 4, "____________________________",0,"C");
	
	$ob_pdf->SetFont('Courier','',14);
	$ob_pdf->SetY($ob_pdf->GetY() + 10);
    if ($_SESSION["filtro_data_inicio"]!="") {
		$periodo_leitura = "

Período de Amostragem " . $_SESSION["filtro_data_inicio"] . " e " . $_SESSION["filtro_data_fim"] . "";
	}
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Período de realização ".$ar_capa['dt_inicio']." e ".$ar_capa['dt_final'] . $periodo_leitura, 0, "C");	
	
	
	if ($ar_capa['nr_publico_total']!="0")
    {
        $ob_pdf->SetY($ob_pdf->GetY() + 10);
    	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Público total: " . $ar_capa['nr_publico_total'], 0, "C");
	}

	$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Número de respondentes: ".$ar_capa['qt_resposta'], 0, "C");


	$ob_pdf->SetFont('Courier','B',20);
	$ob_pdf->SetY($ob_pdf->GetY() + 20);
	//$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Pesquisa elaborada pela", 0, "C");	
	//$ob_pdf->SetY($ob_pdf->GetY() + 5);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, $ar_capa['nome_divisao'], 0, "C");	
	
	$ob_pdf->SetFont('Courier','',12);
	$ob_pdf->SetY(272);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, date('d')." de ".$ar_mes[date('m') -1]." de ".date('Y'), 0, "C");		
	

	$ob_pdf->novaPagina();
	################################################## RESULTADOS ##################################################
	$ob_pdf->SetXY(10,15);
              
    $qr_sql = "
				SELECT DISTINCT p.cd_enquete, a.cd_agrupamento,a.ordem,a.nome AS nome_agrupamento, p.cd_pergunta, p.texto, count(r.valor) AS soma, avg(r.valor) AS media
				  FROM projetos.enquete_resultados r, 
				       projetos.enquete_perguntas p, 
				       projetos.enquete_agrupamentos a
				 WHERE r.questao::text = ('R_'::text || p.cd_pergunta::text)
				   AND (r.valor <> 6::numeric AND a.indic_escala = 'S'::bpchar OR a.indic_escala = 'N'::bpchar)
				   AND r.cd_enquete = p.cd_enquete
				   AND a.cd_agrupamento::numeric = r.cd_agrupamento  
				   AND a.cd_enquete = r.cd_enquete
				   AND a.cd_enquete = " . $_REQUEST["c"] . "
				   {ANDWHERE}
			     GROUP BY p.cd_enquete, a.cd_agrupamento, a.ordem, a.nome, p.cd_pergunta, p.texto
			     ORDER BY a.ordem, p.cd_pergunta, p.texto, soma, media;
              ";
    $where = "";
    if ($_SESSION["filtro_data_inicio"]!="")
    {
        $where = "
              AND DATE_TRUNC('day', r.dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                       AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
        ";
    }
    $qr_sql = str_replace( "{ANDWHERE}", $where, $qr_sql );
	$ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 0;
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		
		#if($ob_pdf->GetY() > 220)
		#{
		#	$ob_pdf->novaPagina();
		#}
		
		if($nr_conta > 0)
		{
			$ob_pdf->novaPagina();
		}
		$nr_conta++;
		#$ob_pdf->SetXY(10,15);		
		
		
		##### AGRUPAMENTO #####
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->SetXY(15,$ob_pdf->GetY() + 5);
		$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, "Grupo: ", 0, "J");		
		$ob_pdf->SetFont('Courier','I',14);
		$ob_pdf->SetXY(15,$ob_pdf->GetY()+2);
		$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, $ar_reg['nome_agrupamento'], 0, "J");	
		
		##### PERGUNTA #####
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->SetXY(15,$ob_pdf->GetY() + 5);
		$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco, "Pergunta: ", 0, "J");			
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->SetXY(15,$ob_pdf->GetY()+2);
		$ob_pdf->MultiCell($nr_largura - 15, $nr_espaco,$ar_reg['texto'], 0, "J");		
		
		##### DADOS PARA GRAFICO #####
		$qr_sql = " 
					SELECT valor,
					       COUNT(*) AS qt_total
 					  FROM projetos.enquete_resultados
					 WHERE cd_enquete = ".$_REQUEST['c']." 
					   AND questao = 'R_".$ar_reg['cd_pergunta']."'
                {ANDWHERE} 
					 GROUP BY valor 
					 ORDER BY qt_total DESC
				  ";
        $where = "";
        if ($_SESSION["filtro_data_inicio"]!="")
        {
            $where = "
                       AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                              AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
            ";
        }
        $qr_sql = str_replace( "{ANDWHERE}", $where, $qr_sql );

		$ob_resul_graf = pg_query($db, $qr_sql);	
		$ar_grafico     = Array();
		$ar_resp_complemento = Array();
		$ar_complemento = Array();

		while($ar_reg_graf = pg_fetch_array($ob_resul_graf))
		{
			if($ar_reg_graf['valor'] == 0)
			{
				$ar_reg_graf_nome['nome'] = "(outros)";
			}
			else
			{
				$qr_sql = "
							SELECT CASE WHEN legenda".number_format($ar_reg_graf['valor'])." IS NOT NULL AND TRIM(legenda".number_format($ar_reg_graf['valor']).") <> ''
							                  THEN legenda".number_format($ar_reg_graf['valor'])."
	                                    WHEN rotulo".number_format($ar_reg_graf['valor'])." IS NOT NULL AND TRIM(rotulo".number_format($ar_reg_graf['valor']).") <> ''
	                                          THEN rotulo".number_format($ar_reg_graf['valor'])."
							            ELSE '".number_format($ar_reg_graf['valor'])."'
							        END AS nome
							  FROM projetos.enquete_perguntas 
							 WHERE cd_enquete  = ".$_REQUEST['c']." 
							   AND cd_pergunta = ".$ar_reg['cd_pergunta']."			
	                      ";
				$ob_resul_graf_nome = pg_query($db, $qr_sql);
				$ar_reg_graf_nome = pg_fetch_array($ob_resul_graf_nome);				
			}
			$ar_grafico[$ar_reg_graf_nome['nome']] = $ar_reg_graf['qt_total'];
			
			
			#### MONTA COMPLEMENTO ####
			$qr_sql = "
						SELECT complemento
						  FROM projetos.enquete_resultados	
						 WHERE cd_enquete = ".$_REQUEST['c']."
						   AND questao    = 'R_".$ar_reg['cd_pergunta']."'
						   AND valor      = ".$ar_reg_graf['valor']."
					   {WHERE} 
						   AND complemento IS NOT NULL	
						 ORDER BY dt_resposta  
					  ";
			$where = "";
			if ($_REQUEST['dt_ini'] != "") 
			{
				$where = "
				   AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('".$_SESSION["filtro_data_inicio"]."', 'DD/MM/YYYY')
														  AND TO_DATE('".$_SESSION["filtro_data_fim"]."', 'DD/MM/YYYY')
				";
			}
			$qr_sql = str_replace("{WHERE}", $where, $qr_sql);
			$ob_resul_complemento = pg_query($db, $qr_sql);	

			if(pg_num_rows($ob_resul_complemento) > 0)
			{
				$ar_resp_complemento[] = array($ar_reg['cd_pergunta'].'_'.$ar_reg_graf['valor'], $ar_reg_graf_nome['nome']);
			}
			
			while ($ar_reg_complemento = pg_fetch_array($ob_resul_complemento)) 
			{
				$ar_complemento[$ar_reg['cd_pergunta'].'_'.$ar_reg_graf['valor']][] = $ar_reg_complemento['complemento'];
			}			
		}
        
        if(count($ar_grafico) > 0)
		{
			#### GRAFICO ####
			$ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);	
			$ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);				
		}
		
		
		
		#### EXIBE COMPLEMENTO DAS RESPOSTAS ####
		/*echo "<PRE>"; 
		print_r($ar_resp_complemento);
		print_r($ar_complemento);*/
		$nr_conta_complemento = 0;
		while($nr_conta_complemento < count($ar_resp_complemento))
		{
			#### CABEÇALHO ####
			$ob_pdf->SetXY(15,$ob_pdf->GetY() + 40);
			$ob_pdf->SetLineWidth(0);
			$ob_pdf->SetDrawColor(0,0,0);
			$ob_pdf->SetWidths(array(180));
			$ob_pdf->SetAligns(array('J'));
			$ob_pdf->SetFont('Courier','B',10);
			$ob_pdf->Row(array("Complemento da resposta ".$ar_resp_complemento[$nr_conta_complemento][1]));		
			$ob_pdf->SetFont('Courier','',10);
			
			$cd_comp = $ar_resp_complemento[$nr_conta_complemento][0];
			$ar_comp = $ar_complemento[$cd_comp];
			$nr_conta_comp_resp = 0;
			while($nr_conta_comp_resp < count($ar_comp))
			{	
				#### LINHAS ####
				$ob_pdf->SetX(15);
				$ob_pdf->Row(array($ar_comp[$nr_conta_comp_resp]));	
				$nr_conta_comp_resp++;
			}		
			$nr_conta_complemento++;
		}
		if(count($ar_resp_complemento) > 0)
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 10);
		}
		else
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 30);
		}
		#### COMENTÁRIOS DA PERGUNTA ####
		$qr_sql = " 
					SELECT descricao 
					  FROM projetos.enquete_resultados 
					 WHERE cd_enquete      = ".$_REQUEST['c']." 
					   AND questao         = 'R_".$ar_reg['cd_pergunta']."' 
					   AND descricao       IS NOT NULL
					   AND TRIM(descricao) <> ''
                {ANDWHERE}
					 ORDER BY descricao		
				  ";
        $where = "";
        if ($_SESSION["filtro_data_inicio"]!="")
        {
            $where = "
                       AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                              AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
            ";
        }
        $qr_sql = str_replace( "{ANDWHERE}", $where, $qr_sql );
		$ob_resul_comenta = pg_query($db, $qr_sql);			
		if(pg_num_rows($ob_resul_comenta) > 0)
		{
			#### CABEÇALHO ####
			$ob_pdf->SetXY(15,$ob_pdf->GetY() + 15);
			$ob_pdf->SetLineWidth(0);
			$ob_pdf->SetDrawColor(0,0,0);
			$ob_pdf->SetWidths(array(180));
			$ob_pdf->SetAligns(array('J'));
			$ob_pdf->SetFont('Courier','B',10);
			$ob_pdf->Row(array("Comentários da Questão (total de comentários: ".pg_num_rows($ob_resul_comenta).")"));		
			$ob_pdf->SetFont('Courier','',10);					
			while($ar_reg_comenta = pg_fetch_array($ob_resul_comenta))
			{
				#### LINHAS ####
				$ob_pdf->SetX(15);
				$ob_pdf->Row(array($ar_reg_comenta['descricao']));	
			}
		}
		else
		{
			$ob_pdf->SetY($ob_pdf->GetY());
		}
		
	}
	

	$ob_pdf->novaPagina();
	################################################## QUESTÃO DISSERTATIVA ##################################################	
	$ob_pdf->SetXY(10,15);
	$ob_pdf->SetFont('Courier','B',16);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Comentários por questões",0,"C");

	$qr_sql = " 
				SELECT pergunta_texto 
			      FROM projetos.enquete_perguntas  
			     WHERE cd_enquete = ".$_REQUEST['c']." 
				   AND texto IS NULL 
				   AND pergunta_texto <> ''				
		      ";	
	$ob_resul = pg_query($db, $qr_sql);			  
	$ar_reg = pg_fetch_array($ob_resul);
	#### CABEÇALHO ####
	$ob_pdf->SetXY(15,$ob_pdf->GetY() + 7);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(180));
	$ob_pdf->SetAligns(array('J'));
	$ob_pdf->SetFont('Courier','B',10);
			  
	$qr_sql = " 
				SELECT descricao 
				  FROM projetos.enquete_resultados 
				 WHERE cd_enquete = ".$_REQUEST['c']."  
				   AND questao = 'Texto'
            {ANDWHERE} 
		      ";
    $where = "";
    if ($_SESSION["filtro_data_inicio"]!="")
    {
        $where = "
                   AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                          AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
        ";
    }
    $qr_sql = str_replace( "{ANDWHERE}", $where, $qr_sql );
	$ob_resul = pg_query($db, $qr_sql);	
	
	$ob_pdf->Row(array($ar_reg['pergunta_texto']." (total de respostas: ".pg_num_rows($ob_resul).")"));		
	$ob_pdf->SetFont('Courier','',10);				  
			  	
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array($ar_reg['descricao']));	
	}

	
	
	$ob_pdf->Output();
	

?>