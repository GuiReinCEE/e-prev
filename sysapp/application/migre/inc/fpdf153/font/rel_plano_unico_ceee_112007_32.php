<?
	include_once('inc/conexao.php');
	require('inc/fpdf153/fpdf.php');

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
		
		
		
	}

	$ob_pdf = new PDF();

	$qr_select = "
					SELECT c.nome,
					       c.cd_empresa,
					       c.cd_registro_empregado,
					       c.seq_dependencia,
					       c.endereco,
					       c.cep,
					       c.bairro_cidade
					  FROM temporario.carta_32 c
					 ORDER BY c.nome
					 LIMIT 1000
					OFFSET ".$_REQUEST['nr_ini']." 
				 ";

	$ob_res = pg_query($db, $qr_select);
	while($ar_reg = pg_fetch_array($ob_res))
	{	
		$ob_pdf->AddPage();
		
				$ob_pdf->Image('img/marcadagua.jpg', 68, 158, ConvertSize(500,$ob_pdf->pgwidth), ConvertSize(497,$ob_pdf->pgwidth),'','',false);		
				
		$ob_pdf->setX(11);
		$ob_pdf->Image('img/logofundacao_carta.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), ConvertSize(250,$ob_pdf->pgwidth), ConvertSize(54,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->Image('img/plano_unico_carta.jpg', 185, $ob_pdf->GetY(), ConvertSize(65,$ob_pdf->pgwidth), ConvertSize(66,$ob_pdf->pgwidth),'','',false);
		$ob_pdf->SetY($ob_pdf->GetY() + ConvertSize(54,$ob_pdf->pgwidth));
		//$ob_pdf->Line(11,$ob_pdf->GetY(),64,$ob_pdf->GetY());
		
		$ob_pdf->SetXY(10,$ob_pdf->GetY()+5);
		$ob_pdf->SetFont('Arial','',10);
		$ob_pdf->MultiCell(190, 4, "Porto Alegre, ".date("d")." de ".$ar_mes[(date("m") - 1)]." de ".date("Y").".
		
Ilmo(a) Sr(a).
".$ar_reg["nome"]."		
		");
		
		


		$ob_pdf->SetY($ob_pdf->GetY()+7);
		#$ob_pdf->RoundedRect(10, $ob_pdf->GetY(), 190, 88, 3.50);	
		$ob_pdf->SetFont('Arial','B',18);
		$ob_pdf->SetY($ob_pdf->GetY() + 7);
		$ob_pdf->Text(69,$ob_pdf->GetY(), "redução de contribuições");
		$ob_pdf->SetXY(15,$ob_pdf->GetY()+5);
		$ob_pdf->SetFont('Arial','',10);
		$ob_pdf->MultiCell(180, 4, "Informamos que, em novembro de 2007, está sendo aplicada uma redução no valor das contribuições para os mesmos níveis praticados antes do aumento ocorrido em 2003. O índice de redução vai depender do salário de contribuição de cada participante. Esta mudança foi possível devido ao superávit acumulado no plano nos últimos anos, o que permitiu a reavaliação atuarial dos níveis de contribuição dos participantes e da patrocinadora Grupo CEEE.");

		$ob_pdf->SetXY(10,$ob_pdf->GetY()+5);
		$ob_pdf->SetFont('Arial','B',10);
		$ob_pdf->MultiCell(190, 4, "Tabela de Contribuição - Plano Único CEEE - Vigente em Novembro 2007",0,"C");

		
		$ob_pdf->SetXY(10,$ob_pdf->GetY()+3);
		
		##### HORIZONTAL #####
		$ob_pdf->Line(25,$ob_pdf->GetY(),185,$ob_pdf->GetY());
		$ob_pdf->Line(25,$ob_pdf->GetY()+14,185,$ob_pdf->GetY()+14);
		$ob_pdf->Line(25,$ob_pdf->GetY()+21,185,$ob_pdf->GetY()+21);
		$ob_pdf->Line(25,$ob_pdf->GetY()+28,185,$ob_pdf->GetY()+28);
		$ob_pdf->Line(25,$ob_pdf->GetY()+36,185,$ob_pdf->GetY()+36);
		$ob_pdf->Line(25,$ob_pdf->GetY()+44,185,$ob_pdf->GetY()+44);
		
		##### VERTICAL #####
		$ob_pdf->Line(25,$ob_pdf->GetY(),25,$ob_pdf->GetY()+44);
		$ob_pdf->Line(67,$ob_pdf->GetY(),67,$ob_pdf->GetY()+44);
		$ob_pdf->Line(107,$ob_pdf->GetY(),107,$ob_pdf->GetY()+44);
		#$ob_pdf->Line(147,$ob_pdf->GetY(),147,$ob_pdf->GetY()+44);
		$ob_pdf->Line(185,$ob_pdf->GetY(),185,$ob_pdf->GetY()+44);
		
		$ob_pdf->SetXY(10,$ob_pdf->GetY()+5);
		$ob_pdf->Text(32,$ob_pdf->GetY()+1, "Salário Real de");
		$ob_pdf->Text(34,$ob_pdf->GetY()+6, "Contribuição");
		
		$ob_pdf->Text(70,$ob_pdf->GetY(), "EX-AUTÁRQUICO");
		$ob_pdf->SetFont('Arial','B',9);
		$ob_pdf->Text(109,$ob_pdf->GetY(), "APOSENTADO AO INGRESSAR NA FUNDAÇÃO");

		$ob_pdf->SetFont('Arial','B',10);
		$ob_pdf->Text(70,$ob_pdf->GetY()+7, "TAXA");
		$ob_pdf->Text(85,$ob_pdf->GetY()+7, "DEDUÇÃO");
	
		$ob_pdf->Text(125,$ob_pdf->GetY()+7, "TAXA");
		$ob_pdf->Text(155,$ob_pdf->GetY()+7, "DEDUÇÃO");

		
		$ob_pdf->SetFont('Arial','',10);
		###
		$ob_pdf->Text(35,$ob_pdf->GetY()+14, "Até 1.110,92");
		$ob_pdf->Text(83 - $ob_pdf->GetStringWidth("1,50%"),$ob_pdf->GetY()+14, "1,50%");	
		$ob_pdf->Text(103 - $ob_pdf->GetStringWidth("0,00"),$ob_pdf->GetY()+14, "0,00");	
		$ob_pdf->Text(135 - $ob_pdf->GetStringWidth("4,50%"),$ob_pdf->GetY()+14, "4,50%");	
		$ob_pdf->Text(173 - $ob_pdf->GetStringWidth("0,00"),$ob_pdf->GetY()+14, "0,00");

		
		###
		$ob_pdf->Text(30,$ob_pdf->GetY()+21, "1.110,93 a 2.221,83");
		$ob_pdf->Text(83 - $ob_pdf->GetStringWidth("2,50%"),$ob_pdf->GetY()+21, "2,50%");	
		$ob_pdf->Text(103 - $ob_pdf->GetStringWidth("11,11"),$ob_pdf->GetY()+21, "11,11");		
		$ob_pdf->Text(135 - $ob_pdf->GetStringWidth("6,25%"),$ob_pdf->GetY()+21, "6,25%");	
		$ob_pdf->Text(173 - $ob_pdf->GetStringWidth("19,44"),$ob_pdf->GetY()+21, "19,44");

		###
		$ob_pdf->Text(29,$ob_pdf->GetY()+28, "2.221,84 a 14.448,33");
		$ob_pdf->Text(83 - $ob_pdf->GetStringWidth("5,70%"),$ob_pdf->GetY()+28, "5,70%");	
		$ob_pdf->Text(103 - $ob_pdf->GetStringWidth("82,21"),$ob_pdf->GetY()+28, "82,21");			
		$ob_pdf->Text(135 - $ob_pdf->GetStringWidth("14,28%"),$ob_pdf->GetY()+28, "14,28%");	
		$ob_pdf->Text(173 - $ob_pdf->GetStringWidth("197,80"),$ob_pdf->GetY()+28, "197,85");		

		
		###
		$ob_pdf->Text(28,$ob_pdf->GetY()+36, "14.448,34 a 29.162,63");
		$ob_pdf->Text(83 - $ob_pdf->GetStringWidth("12,38%"),$ob_pdf->GetY()+36, "12,38%");	
		$ob_pdf->Text(103 - $ob_pdf->GetStringWidth("1.047,36"),$ob_pdf->GetY()+36, "1.047,36");				
		$ob_pdf->Text(135 - $ob_pdf->GetStringWidth("22,88%"),$ob_pdf->GetY()+36, "22,88%");	
		$ob_pdf->Text(173 - $ob_pdf->GetStringWidth("1.440,41"),$ob_pdf->GetY()+36, "1.440,41");
		
		
		$ob_pdf->SetXY(10,$ob_pdf->GetY()+2);
		$ob_pdf->Line(67,$ob_pdf->GetY(),185,$ob_pdf->GetY());

		$ob_pdf->SetY($ob_pdf->GetY()+40);
		$parte1 = "Para mais informações acesse o site";
		$parte2 = "www.fundacaoceee.com.br";
		$parte3 = "ou ligue para";
		$parte4 = "0800 51 2596.";
		$ob_pdf->SetFont('Arial','',12);
		$ob_pdf->SetY($ob_pdf->GetY() + 10);
		$ob_pdf->Text(10,$ob_pdf->GetY(), $parte1);
		$ob_pdf->SetFont('Arial','B',12);
		$ob_pdf->Text($ob_pdf->GetStringWidth($parte1) + 7,$ob_pdf->GetY(), $parte2);
		$ob_pdf->SetFont('Arial','',12);
		$ob_pdf->Text($ob_pdf->GetStringWidth($parte1.$parte2) + 17,$ob_pdf->GetY(), $parte3);
		$ob_pdf->SetFont('Arial','B',12);
		$ob_pdf->Text($ob_pdf->GetStringWidth($parte1.$parte2.$parte3) + 8,$ob_pdf->GetY(), $parte4);

		$ob_pdf->SetFont('Arial','',10);
		$ob_pdf->SetY($ob_pdf->GetY() + 10);
		$ob_pdf->Text(10,$ob_pdf->GetY(), "Diretoria Executiva");
		$ob_pdf->SetY($ob_pdf->GetY() + 5);
		$ob_pdf->Text(10,$ob_pdf->GetY(), "Fundação CEEE");
		
		endereco($ar_reg["cd_empresa"],$ar_reg["cd_registro_empregado"],$ar_reg["seq_dependencia"],$ar_reg["nome"], $ar_reg["endereco"], $ar_reg["bairro_cidade"], $ar_reg["cep"]);
		
	}



	$ob_pdf->Output();

	
	function endereco($cd_empresa, $cd_re, $seq, $nome, $endereco, $bairro_cidade, $cep)
	{
		global $ob_pdf;
		
		$ob_pdf->AddPage();
		$ob_pdf->SetY(94);
		$ob_pdf->Line(15,$ob_pdf->GetY(),25,$ob_pdf->GetY());
		$ob_pdf->Line(190,$ob_pdf->GetY(),200,$ob_pdf->GetY());
		$ob_pdf->SetY($ob_pdf->GetY() + 17);
		
		$ob_pdf->setX(16);
		$ob_pdf->Image('img/logofundacao_carta.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), ConvertSize(250,$ob_pdf->pgwidth), ConvertSize(54,$ob_pdf->pgwidth),'','',false);

		$ob_pdf->RoundedRect(105, $ob_pdf->GetY(), 90, 22, 3.50);		
		$ob_pdf->SetXY(10,$ob_pdf->GetY() + 22);
		$ob_pdf->RoundedRect(15, $ob_pdf->GetY(), 180,56, 3.50);
		
		$i = 0;
		$background = "";
		while($i < 98)
		{
			$background.= " FUNDAÇÃO CEEE";
			$i++;
		}
		$ob_pdf->SetX(10,$ob_pdf->GetY() + 4);
		$ob_pdf->SetFont('Arial','',8);
		$ob_pdf->SetTextColor(160,160,160);
		$ob_pdf->MultiCell(190, 4, $background,0,"C");
		
		$ob_pdf->SetY($ob_pdf->GetY() - 42);
		$style6 = (array( 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ob_pdf->RoundedRect(32, $ob_pdf->GetY(), 150,29, 3.50,'1111', 'DF',$style6, array(200, 200, 200));

		$ob_pdf->SetY($ob_pdf->GetY() + 5);
		$ob_pdf->SetTextColor(0,0,0);
		$ob_pdf->SetFont('Arial','',8);
		$ob_pdf->Text(38,$ob_pdf->GetY() , "DESTINATÁRIO");
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Text(38,$ob_pdf->GetY()+5 , $nome);
		$ob_pdf->Text(38,$ob_pdf->GetY()+9 , "RE: ".$cd_empresa."/".$cd_re."/".$seq);
		$ob_pdf->Text(38,$ob_pdf->GetY()+13 , "Endereço: ".$endereco);
		$ob_pdf->Text(38,$ob_pdf->GetY()+17 , "Bairro: ".$bairro_cidade);
		$ob_pdf->Text(38,$ob_pdf->GetY()+21 , "CEP: ".$cep);
		
		
		$ob_pdf->SetY(196);
		$ob_pdf->Line(15,$ob_pdf->GetY(),25,$ob_pdf->GetY());
		$ob_pdf->Line(190,$ob_pdf->GetY(),200,$ob_pdf->GetY());
		
		#########################
		$ob_pdf->SetXY(10,$ob_pdf->GetY() + 18);
		$ob_pdf->RoundedRect(15, $ob_pdf->GetY(), 180,60, 3.50);
		
		$i = 0;
		$background = "";
		while($i < 105)
		{
			$background.= " FUNDAÇÃO CEEE";
			$i++;
		}
		$ob_pdf->SetX(10,$ob_pdf->GetY() + 4);
		$ob_pdf->SetFont('Arial','',8);
		$ob_pdf->SetTextColor(160,160,160);
		$ob_pdf->MultiCell(190, 4, $background,0,"C");
		
		$ob_pdf->SetY($ob_pdf->GetY() - 58);
		$style6 = (array( 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ob_pdf->RoundedRect(19, $ob_pdf->GetY(), 172,29, 3.50,'1111', 'DF',$style6, array(200, 200, 200));

		$ob_pdf->SetY($ob_pdf->GetY() + 5);
		$ob_pdf->SetTextColor(0,0,0);
		$ob_pdf->SetFont('Courier','',8);
		$ob_pdf->Text(22,$ob_pdf->GetY() , "PARA USO DO CORREIO");
		$ob_pdf->Text(22,$ob_pdf->GetY()+5 , "( ) Mudou-se");
		$ob_pdf->Text(22,$ob_pdf->GetY()+9 , "( ) Endereço insuficiente");
		$ob_pdf->Text(22,$ob_pdf->GetY()+13 ,"( ) Não existe nº indicado");
		$ob_pdf->Text(22,$ob_pdf->GetY()+17 ,"( ) Desconhecido");
		$ob_pdf->Text(22,$ob_pdf->GetY()+21 ,"( ) Inf. escrita pelo Porteiro ou Síndico");		
		$ob_pdf->Text(95,$ob_pdf->GetY()+5 , "( ) Não procurado");
		$ob_pdf->Text(95,$ob_pdf->GetY()+9 , "( ) Ausente");
		$ob_pdf->Text(95,$ob_pdf->GetY()+13 ,"( ) Falecido");
		$ob_pdf->Text(95,$ob_pdf->GetY()+17 ,"( ) Recusado");
		$ob_pdf->Text(95,$ob_pdf->GetY()+21 ,"( ) ______________________");			
		$ob_pdf->Text(135,$ob_pdf->GetY(), "Reintegrado ao serviço Postal em:");
		$ob_pdf->Text(150,$ob_pdf->GetY()+5 ,"____/____/______");
		$ob_pdf->Text(147,$ob_pdf->GetY()+17 ,"______________________");
		$ob_pdf->Text(152,$ob_pdf->GetY()+21 ,"Ass. Responsável");	



		$ob_pdf->SetY($ob_pdf->GetY() + 27);
		$style6 = (array( 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ob_pdf->RoundedRect(19, $ob_pdf->GetY(), 172,24, 3.50,'1111', 'DF',$style6, array(200, 200, 200));
		$ob_pdf->SetY($ob_pdf->GetY() + 5);
		$ob_pdf->Text(22,$ob_pdf->GetY() , "REMETENTE");	
		$ob_pdf->SetFont('Courier','',10);
		$ob_pdf->Text(72,$ob_pdf->GetY()+3 , "Rua dos Andradas, 702 - Centro");		
		$ob_pdf->Text(72,$ob_pdf->GetY()+8 , "0800512596 - www.fundacaoceee.com.br");		
		$ob_pdf->Text(72,$ob_pdf->GetY()+13 , "CEP 90000-004   Porto Alegre - RS");		
		
		
	}	
	
function ConvertSize($size=5,$maxsize=0){
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
?>