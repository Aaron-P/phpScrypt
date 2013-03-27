<?php

/*

function lshiftright($var, $amt)
{
    $mask = 0x40000000;
    if($var < 0)
    {
        $var &= 0x7FFFFFFF;
        $mask = $mask >> ($amt-1);
        return ($var >> $amt) | $mask;
    }
    return $var >> $amt;
}
*/
function chbo($num) {
    $data = dechex($num);
    if (strlen($data) <= 2) {
        return $num;
    }
    $u = unpack("H*", strrev(pack("H*", $data)));
    $f = hexdec($u[1]);
    return $f;
}
/*
function unsigned_xor32 ($a, $b)
{
        $a1 = $a & 0x7FFF0000;
        $a2 = $a & 0x0000FFFF;
        $a3 = $a & 0x80000000;
        $b1 = $b & 0x7FFF0000;
        $b2 = $b & 0x0000FFFF;
        $b3 = $b & 0x80000000;

        $c = ($a3 != $b3) ? 0x80000000 : 0;

        return (($a1 ^ $b1) |($a2 ^ $b2)) + $c;
}
*/
function R($a, $b)
{
//	return (($a) << ($b)) | (lshiftright($a, (32 - ($b))));
	return (($a) << ($b)) | (($a) >> (32 - ($b)));
}

function salsa208_word_specification(&$out = array(), $in = array())
{
	if (count($in) !== 16)//Should $out be length checked?
		return false;//throw here instead
	$out = array();//should we really do this?

	$x = array();
	for ($i = 0; $i < 16; ++$i)
		$x[$i] = $in[$i];
	for ($i = 8; $i > 0; $i -= 2)
	{
/*		$x[ 4] = unsigned_xor32($x[ 4], R($x[ 0] + $x[12],  7));
		$x[ 8] = unsigned_xor32($x[ 8], R($x[ 4] + $x[ 0],  9));
		$x[12] = unsigned_xor32($x[12], R($x[ 8] + $x[ 4], 13));
		$x[ 0] = unsigned_xor32($x[ 0], R($x[12] + $x[ 8], 18));
		$x[ 9] = unsigned_xor32($x[ 9], R($x[ 5] + $x[ 1],  7));
		$x[13] = unsigned_xor32($x[13], R($x[ 9] + $x[ 5],  9));
		$x[ 1] = unsigned_xor32($x[ 1], R($x[13] + $x[ 9], 13));
		$x[ 5] = unsigned_xor32($x[ 5], R($x[ 1] + $x[13], 18));
		$x[14] = unsigned_xor32($x[14], R($x[10] + $x[ 6],  7));
		$x[ 2] = unsigned_xor32($x[ 2], R($x[14] + $x[10],  9));
		$x[ 6] = unsigned_xor32($x[ 6], R($x[ 2] + $x[14], 13));
		$x[10] = unsigned_xor32($x[10], R($x[ 6] + $x[ 2], 18));
		$x[ 3] = unsigned_xor32($x[ 3], R($x[15] + $x[11],  7));
		$x[ 7] = unsigned_xor32($x[ 7], R($x[ 3] + $x[15],  9));
		$x[11] = unsigned_xor32($x[11], R($x[ 7] + $x[ 3], 13));
		$x[15] = unsigned_xor32($x[15], R($x[11] + $x[ 7], 18));
		$x[ 1] = unsigned_xor32($x[ 1], R($x[ 0] + $x[ 3],  7));
		$x[ 2] = unsigned_xor32($x[ 2], R($x[ 1] + $x[ 0],  9));
		$x[ 3] = unsigned_xor32($x[ 3], R($x[ 2] + $x[ 1], 13));
		$x[ 0] = unsigned_xor32($x[ 0], R($x[ 3] + $x[ 2], 18));
		$x[ 6] = unsigned_xor32($x[ 6], R($x[ 5] + $x[ 4],  7));
		$x[ 7] = unsigned_xor32($x[ 7], R($x[ 6] + $x[ 5],  9));
		$x[ 4] = unsigned_xor32($x[ 4], R($x[ 7] + $x[ 6], 13));
		$x[ 5] = unsigned_xor32($x[ 5], R($x[ 4] + $x[ 7], 18));
		$x[11] = unsigned_xor32($x[11], R($x[10] + $x[ 9],  7));
		$x[ 8] = unsigned_xor32($x[ 8], R($x[11] + $x[10],  9));
		$x[ 9] = unsigned_xor32($x[ 9], R($x[ 8] + $x[11], 13));
		$x[10] = unsigned_xor32($x[10], R($x[ 9] + $x[ 8], 18));
		$x[12] = unsigned_xor32($x[12], R($x[15] + $x[14],  7));
		$x[13] = unsigned_xor32($x[13], R($x[12] + $x[15],  9));
		$x[14] = unsigned_xor32($x[14], R($x[13] + $x[12], 13));
		$x[15] = unsigned_xor32($x[15], R($x[14] + $x[13], 18));*/
		$x[ 4] ^= R($x[ 0] + $x[12],  7);
		$x[ 8] ^= R($x[ 4] + $x[ 0],  9);
		$x[12] ^= R($x[ 8] + $x[ 4], 13);
		$x[ 0] ^= R($x[12] + $x[ 8], 18);
		$x[ 9] ^= R($x[ 5] + $x[ 1],  7);
		$x[13] ^= R($x[ 9] + $x[ 5],  9);
		$x[ 1] ^= R($x[13] + $x[ 9], 13);
		$x[ 5] ^= R($x[ 1] + $x[13], 18);
		$x[14] ^= R($x[10] + $x[ 6],  7);
		$x[ 2] ^= R($x[14] + $x[10],  9);
		$x[ 6] ^= R($x[ 2] + $x[14], 13);
		$x[10] ^= R($x[ 6] + $x[ 2], 18);
		$x[ 3] ^= R($x[15] + $x[11],  7);
		$x[ 7] ^= R($x[ 3] + $x[15],  9);
		$x[11] ^= R($x[ 7] + $x[ 3], 13);
		$x[15] ^= R($x[11] + $x[ 7], 18);
		$x[ 1] ^= R($x[ 0] + $x[ 3],  7);
		$x[ 2] ^= R($x[ 1] + $x[ 0],  9);
		$x[ 3] ^= R($x[ 2] + $x[ 1], 13);
		$x[ 0] ^= R($x[ 3] + $x[ 2], 18);
		$x[ 6] ^= R($x[ 5] + $x[ 4],  7);
		$x[ 7] ^= R($x[ 6] + $x[ 5],  9);
		$x[ 4] ^= R($x[ 7] + $x[ 6], 13);
		$x[ 5] ^= R($x[ 4] + $x[ 7], 18);
		$x[11] ^= R($x[10] + $x[ 9],  7);
		$x[ 8] ^= R($x[11] + $x[10],  9);
		$x[ 9] ^= R($x[ 8] + $x[11], 13);
		$x[10] ^= R($x[ 9] + $x[ 8], 18);
		$x[12] ^= R($x[15] + $x[14],  7);
		$x[13] ^= R($x[12] + $x[15],  9);
		$x[14] ^= R($x[13] + $x[12], 13);
		$x[15] ^= R($x[14] + $x[13], 18);
	}
	for ($i = 0; $i < 16; ++$i)
		$out[$i] = $x[$i] + $in[$i];
}


$out = array();
$in = array();
$in[0] = chbo(0x7e879a21);
$in[1] = chbo(0x4f3ec986);
$in[2] = chbo(0x7ca940e6);
$in[3] = chbo(0x41718f26);
$in[4] = chbo(0xbaee555b);
$in[5] = chbo(0x8c61c1b5);
$in[6] = chbo(0x0df84611);
$in[7] = chbo(0x6dcd3b1d);
$in[8] = chbo(0xee24f319);
$in[9] = chbo(0xdf9b3d85);
$in[10] = chbo(0x14121e4b);
$in[11] = chbo(0x5ac5aa32);
$in[12] = chbo(0x76021d29);
$in[13] = chbo(0x09c74829);
$in[14] = chbo(0xedebc68d);
$in[15] = chbo(0xb8b8c25e);

/*$in[0] = 0x7e879a21;
$in[1] = 0x4f3ec986;
$in[2] = 0x7ca940e6;
$in[3] = 0x41718f26;
$in[4] = 0xbaee555b;
$in[5] = 0x8c61c1b5;
$in[6] = 0x0df84611;
$in[7] = 0x6dcd3b1d;
$in[8] = 0xee24f319;
$in[9] = 0xdf9b3d85;
$in[10] = 0x14121e4b;
$in[11] = 0x5ac5aa32;
$in[12] = 0x76021d29;
$in[13] = 0x09c74829;
$in[14] = 0xedebc68d;
$in[15] = 0xb8b8c25e;*/

salsa208_word_specification($out, $in);

for ($i = 0; $i < 16; $i++)
{
	$out[$i] = sprintf('%u', $out[$i]);
//	$temp = dechex($out[$i]);
//	$out[$i] = str_pad($temp, 8, "0", STR_PAD_LEFT);
}

echo "<pre>";
//print_r($out);


function thirtyTwoBitIntval($value)
{
    if ($value < -2147483648)
    {
        return -(-($value) & 0xffffffff);
    }
    elseif ($value > 2147483647)
    {
        return ($value & 0xffffffff);
    }
    return $value;
}

//$str1 = "\x7E\x87\x9A\x21";
//$str2 = "\x4F\x3E\xC9\x86";

$str1 = 0x7E879A21;
$str2 = 0x4F3EC986;

//$str1 = 2122816033.0;
//$str2 = 1329514886.0;

$xxx = R($str1, 3);
$yyy = $str1 + $str2;
$zzz = $str1 ^ $str2;






function lshiftright($var, $amt)
{
    $mask = 0x40000000;
    if($var < 0)
    {
        $var &= 0x7FFFFFFF;
        $mask = $mask >> ($amt-1);
        return ($var >> $amt) | $mask;
    }
    return $var >> $amt;
}



/*function unsigned_xor32($a, $b)
{
        $a1 = $a & 0x7FFF0000;
        $a2 = $a & 0x0000FFFF;
        $a3 = $a & 0x80000000;
        $b1 = $b & 0x7FFF0000;
        $b2 = $b & 0x0000FFFF;
        $b3 = $b & 0x80000000;

        $c = ($a3 != $b3) ? 0x80000000 : 0;

        return (($a1 ^ $b1) | ($a2 ^ $b2)) + $c;
}*/






function unsigned_or32($a, $b)
{
	return bindec(decbin($a) | decbin($b));
}
function unsigned_xor32($a, $b)
{
	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 != $b3) ? 0x80000000 : 0;

	return (($a1 ^ $b1) | ($a2 ^ $b2)) + $c;

	return bindec(decbin($a) ^ decbin($b));
}
function unsigned_and32($a, $b)
{
	return bindec(decbin($a) & decbin($b));
}
function unsigned_rshift32($a, $b)//BROKEN AS HELL
{
	if ($a > 0x7FFFFFFF)
		return bindec(decbin(decbin($a) >> $b - 1));
	return lshiftright($a, $b);

//	echo decbin($a)."\n";
//	echo lshiftright($a, $b)."\n";
//	echo decbin(decbin($a) >> $b)."\n\n";
//	return bindec(decbin(decbin($a) >> $b));
}
function unsigned_lshift32($a, $b)
{
	return bindec(decbin(decbin($a) << $b));
}

echo (0xFFFFFFFF | 0x00000000)." = ".sprintf('%u', 0xFFFFFFFF | 0x00000000);
echo "\n";
echo unsigned_or32(0xFFFFFFFF, 0x00000000)." = ".sprintf('%u', 0xFFFFFFFF | 0x00000000);
echo "\n\n";
echo (0xFFFFFFFF ^ 0x00000000)." = ".sprintf('%u', 0xFFFFFFFF ^ 0x00000000);
echo "\n";
echo unsigned_xor32(0xFFFFFFFF, 0x00000000)." = ".sprintf('%u', 0xFFFFFFFF ^ 0x00000000);
echo "\n\n";
echo (0xFFFFFFFF & 0xFFFFFFFF)." = ".sprintf('%u', 0xFFFFFFFF & 0xFFFFFFFF);
echo "\n";
echo unsigned_and32(0xFFFFFFFF, 0xFFFFFFFF)." = ".sprintf('%u', 0xFFFFFFFF & 0xFFFFFFFF);
echo "\n\n";
echo (0x80000001 >> 0x00000001);//." = ".sprintf('%u', 0xFFFFFFFF >> 0x00000001);//WRONG
echo " = 2147483647";
echo "\n";
echo unsigned_rshift32(0x80000001, 0x00000001);//." = ".sprintf('%u', 0xFFFFFFFF >> 0x00000001);//WRONG
echo " = 2147483647";
echo "\n\n";
echo (0xFFFFFFFF << 0x00000001)." = ".sprintf('%u', 0xFFFFFFFF << 0x00000001);
echo "\n";
echo unsigned_lshift32(0xFFFFFFFF, 0x00000001)." = ".sprintf('%u', 0xFFFFFFFF << 0x00000001);
echo "\n\n";


echo "\n\n==============================================\n\n";


echo (0x80000001 >> 0x00000001);//." = ".sprintf('%u', 0xFFFFFFFF >> 0x00000001);//WRONG
echo " = 2147483647";
echo "\n";
echo unsigned_rshift32(0x80000001, 0x00000001);//." = ".sprintf('%u', 0xFFFFFFFF >> 0x00000001);//WRONG
echo " = 2147483647";

echo "\n\n==============================================\n\n";






//$str1_ = unpack("N", $str1);
//$str2_ = unpack("N", $str2);

//echo dechex(sprintf('%u', $str1_[1] + $str2_[1]));

echo "\n\n";

//xxx	0xF43CD10B = 4097626379
//yyy	0xCDC663A7 = 3452330919
//zzz	0x31B953A7 = 834229159
echo ($xxx)." = 4097626379\n".($yyy)." = 3452330919\n".($zzz)." = 834229159\n\n";
echo (sprintf('%u', $xxx))." = 4097626379\n".(sprintf('%u', $yyy))." = 3452330919\n".(sprintf('%u', $zzz))." = 834229159";


?>


INPUT:
7e879a21 4f3ec986 7ca940e6 41718f26
baee555b 8c61c1b5 0df84611 6dcd3b1d
ee24f319 df9b3d85 14121e4b 5ac5aa32
76021d29 09c74829 edebc68d b8b8c25e

OUTPUT:
a41f859c 6608cc99 3b81cacb 020cef05
044b2181 a2fd337d fd7b1c63 96682f29
b4393168 e3c9e6bc fe6bc5b7 a06d96ba
e424cc10 2c91745c 24ad673d c7618f81
<?php
echo "</pre>";



?>