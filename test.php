<?php

function dbg($in)
{
	echo "<pre>\n== DBG ===========================\n";
	print_r($in);
	echo "==================================\n</pre>";
}

//1110100010110010 1000101011010110 = a
//1001111001000111 0010110011001111 = b
//0111011011110101 1010011000011001 = a ^ b //correct
//
//0111111111111111 0000000000000000 = 0x7FFF0000
//0000000000000000 1111111111111111 = 0x0000FFFF
//1000000000000000 0000000000000000 = 0x80000000;
//
//0110100010110010 0000000000000000 = a1
//0000000000000000 1000101011010110 = a2
//1000000000000000 0000000000000000 = a3
//0111111111111111 0000000000000000 = b1
//0000000000000000 0010110011001111 = b2
//1000000000000000 0000000000000000 = b3
//
//0000000000000000 0000000000000000 = c
//
//0001011101001101 0000000000000000 = a1 ^ b1
//0000000000000000 1010011000011001 = a2 ^ b2
//
//0001011101001101 1010011000011001 = (a1 ^ b1) | (a2 ^ b2)
//
//0001011101001101 1010011000011001 = (a1 ^ b1) | (a2 ^ b2) + c //incorrect wtf

//do we need to break it up into 16 bits? Can we do 31 + 1 bit?

function unsigned_or32($a, $b)//NOT NEEDED? | returns signed in php
{
//	return $a | $b;

	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 || $b3) ? 0x80000000 : 0;

	return (($a1 | $b1) | ($a2 | $b2)) + $c;
}
function unsigned_xor32($a, $b)//NOT NEEDED? ^ returns signed in php
{
//	return $a ^ $b;

	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 != $b3) ? 0x80000000 : 0;

	return (($a1 ^ $b1) | ($a2 ^ $b2)) + $c;
}
function unsigned_and32($a, $b)//NOT NEEDED?
{
//	return $a & $b;

	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 && $b3) ? 0x80000000 : 0;

	return (($a1 & $b1) | ($a2 & $b2)) + $c;
}
function unsigned_rshift32($a, $b)//NEEDED
{
	// < 0 error?
	if ($b === 0)
		return $a;
	if ($b >= 32)
		return 0;
	// <= 0x7FFFFFFF?

	$a1 = ($a & 0x7FFFFFFF) >> $b;
	$c = ($a & 0x80000000) ? (0x40000000 >> ($b - 1)) : 0;
	return $a1 + $c;
}
function unsigned_lshift32($a, $b)//NOT NEEDED?
{
//	return $a << $b;

	$a1 = $a << $b;
//	$c = ($a & (0x40000000 >> ($b - 1))) ? 0x80000000 : 0;
//	return $a1 + ($c * 2);//Why?
	return bindec(decbin($a1));//Better way than bindec(decbin, is it needed at all?
}

// proper endian 0xEA2C476D

function unsigned_byteswap32($a)
{
//	return unsigned_rshift32($a, 24) | (($a << 8) & 0x00FF0000) | (unsigned_rshift32($a, 8) & 0x0000FF00) | ($a << 24);
//	return unsigned_or32(unsigned_or32(unsigned_or32(unsigned_rshift32($a, 24), ($a << 8) & 0x00FF0000), unsigned_rshift32($a, 8) & 0x0000FF00), $a << 24);
	return unsigned_or32(unsigned_or32(unsigned_or32(unsigned_rshift32($a, 24), unsigned_and32(unsigned_lshift32($a, 8), 0x00FF0000)), unsigned_and32(unsigned_rshift32($a, 8), 0x0000FF00)), unsigned_lshift32($a, 24));
}

//echo unsigned_byteswap32(0x6D472CEA);

/*
echo unsigned_lshift32(0x6D472CEA, 1)." = 3666762196<br>";//1	0xDA8E59D4 3666762196
echo unsigned_lshift32(0x6D472CEA, 2)." = 3038557096<br>";//2	0xB51Cb3A8 3038557096
echo unsigned_lshift32(0x6D472CEA, 3)." = 1782146896<br>";//3	0x6A396750 1782146896
echo unsigned_lshift32(0x6D472CEA, 4)." = 3564293792<br>";//4	0xD472CEA0 3564293792
echo unsigned_lshift32(0x6D472CEA, 5)." = 2833620288<br>";//5	0xA8E59D40 2833620288

*/

/*
echo unsigned_rshift32(0x80000000, 1);

for ($i = 0; $i < 1000; $i++)
{
	$num1 = bindec(decbin(unpack('L', openssl_random_pseudo_bytes(4))[1]));
	$num2 = bindec(decbin(unpack('L', openssl_random_pseudo_bytes(4))[1]));

	$val1 = unsigned_lshift32($num1, 1);
	$val2 = sprintf('%u', $num1 << 1);

	if ($val1 != $val2)
	{
		echo "Mismatch (i = ".$i.", num1 = ".$num1.", num2 = ".$num2."): ".$val1." !== ".$val2;
		break;
	}
}
echo "Tests finished succesfully.";
*/

function R($a, $b)
{
//	return unsigned_or32($a << $b, unsigned_rshift32($a, 32 - $b));
	return unsigned_or32(unsigned_lshift32($a, $b), unsigned_rshift32($a, 32 - $b));
}

function salsa208_word_specification(&$out = array(), $in = array())//out and in should be 1 parameter
{
	if (count($in) !== 16)//Should $out be length checked?
		return false;//throw here instead
	$out = array();//should we really do this?//NO WE SHOULDNT DO THIS

	for ($i = 0; $i < 16; ++$i)
		$in[$i] = unsigned_byteswap32($in[$i]);

	$x = array();
	for ($i = 0; $i < 16; ++$i)
		$x[$i] = $in[$i];

	for ($i = 8; $i > 0; $i -= 2)
	{
/*		$x[ 4] ^= R($x[ 0] + $x[12],  7); $x[ 8] ^= R($x[ 4] + $x[ 0],  9);
		$x[12] ^= R($x[ 8] + $x[ 4], 13); $x[ 0] ^= R($x[12] + $x[ 8], 18);
		$x[ 9] ^= R($x[ 5] + $x[ 1],  7); $x[13] ^= R($x[ 9] + $x[ 5],  9);
		$x[ 1] ^= R($x[13] + $x[ 9], 13); $x[ 5] ^= R($x[ 1] + $x[13], 18);
		$x[14] ^= R($x[10] + $x[ 6],  7); $x[ 2] ^= R($x[14] + $x[10],  9);
		$x[ 6] ^= R($x[ 2] + $x[14], 13); $x[10] ^= R($x[ 6] + $x[ 2], 18);
		$x[ 3] ^= R($x[15] + $x[11],  7); $x[ 7] ^= R($x[ 3] + $x[15],  9);
		$x[11] ^= R($x[ 7] + $x[ 3], 13); $x[15] ^= R($x[11] + $x[ 7], 18);
		$x[ 1] ^= R($x[ 0] + $x[ 3],  7); $x[ 2] ^= R($x[ 1] + $x[ 0],  9);
		$x[ 3] ^= R($x[ 2] + $x[ 1], 13); $x[ 0] ^= R($x[ 3] + $x[ 2], 18);
		$x[ 6] ^= R($x[ 5] + $x[ 4],  7); $x[ 7] ^= R($x[ 6] + $x[ 5],  9);
		$x[ 4] ^= R($x[ 7] + $x[ 6], 13); $x[ 5] ^= R($x[ 4] + $x[ 7], 18);
		$x[11] ^= R($x[10] + $x[ 9],  7); $x[ 8] ^= R($x[11] + $x[10],  9);
		$x[ 9] ^= R($x[ 8] + $x[11], 13); $x[10] ^= R($x[ 9] + $x[ 8], 18);
		$x[12] ^= R($x[15] + $x[14],  7); $x[13] ^= R($x[12] + $x[15],  9);
		$x[14] ^= R($x[13] + $x[12], 13); $x[15] ^= R($x[14] + $x[13], 18);*/
		$x[ 4] = unsigned_xor32($x[ 4], R($x[ 0] + $x[12],  7)); $x[ 8] = unsigned_xor32($x[ 8], R($x[ 4] + $x[ 0],  9));
		$x[12] = unsigned_xor32($x[12], R($x[ 8] + $x[ 4], 13)); $x[ 0] = unsigned_xor32($x[ 0], R($x[12] + $x[ 8], 18));
		$x[ 9] = unsigned_xor32($x[ 9], R($x[ 5] + $x[ 1],  7)); $x[13] = unsigned_xor32($x[13], R($x[ 9] + $x[ 5],  9));
		$x[ 1] = unsigned_xor32($x[ 1], R($x[13] + $x[ 9], 13)); $x[ 5] = unsigned_xor32($x[ 5], R($x[ 1] + $x[13], 18));
		$x[14] = unsigned_xor32($x[14], R($x[10] + $x[ 6],  7)); $x[ 2] = unsigned_xor32($x[ 2], R($x[14] + $x[10],  9));
		$x[ 6] = unsigned_xor32($x[ 6], R($x[ 2] + $x[14], 13)); $x[10] = unsigned_xor32($x[10], R($x[ 6] + $x[ 2], 18));
		$x[ 3] = unsigned_xor32($x[ 3], R($x[15] + $x[11],  7)); $x[ 7] = unsigned_xor32($x[ 7], R($x[ 3] + $x[15],  9));
		$x[11] = unsigned_xor32($x[11], R($x[ 7] + $x[ 3], 13)); $x[15] = unsigned_xor32($x[15], R($x[11] + $x[ 7], 18));
		$x[ 1] = unsigned_xor32($x[ 1], R($x[ 0] + $x[ 3],  7)); $x[ 2] = unsigned_xor32($x[ 2], R($x[ 1] + $x[ 0],  9));
		$x[ 3] = unsigned_xor32($x[ 3], R($x[ 2] + $x[ 1], 13)); $x[ 0] = unsigned_xor32($x[ 0], R($x[ 3] + $x[ 2], 18));
		$x[ 6] = unsigned_xor32($x[ 6], R($x[ 5] + $x[ 4],  7)); $x[ 7] = unsigned_xor32($x[ 7], R($x[ 6] + $x[ 5],  9));
		$x[ 4] = unsigned_xor32($x[ 4], R($x[ 7] + $x[ 6], 13)); $x[ 5] = unsigned_xor32($x[ 5], R($x[ 4] + $x[ 7], 18));
		$x[11] = unsigned_xor32($x[11], R($x[10] + $x[ 9],  7)); $x[ 8] = unsigned_xor32($x[ 8], R($x[11] + $x[10],  9));
		$x[ 9] = unsigned_xor32($x[ 9], R($x[ 8] + $x[11], 13)); $x[10] = unsigned_xor32($x[10], R($x[ 9] + $x[ 8], 18));
		$x[12] = unsigned_xor32($x[12], R($x[15] + $x[14],  7)); $x[13] = unsigned_xor32($x[13], R($x[12] + $x[15],  9));
		$x[14] = unsigned_xor32($x[14], R($x[13] + $x[12], 13)); $x[15] = unsigned_xor32($x[15], R($x[14] + $x[13], 18));
	}
	for ($i = 0; $i < 16; ++$i)
		$out[$i] = $x[$i] + $in[$i];

	for ($i = 0; $i < 16; ++$i)
		$out[$i] = unsigned_byteswap32($out[$i]);
}

/*
$in[0]  = 0x7E879A21;
$in[1]  = 0x4F3EC986;
$in[2]  = 0x7CA940E6;
$in[3]  = 0x41718F26;
$in[4]  = 0xBAEE555B;
$in[5]  = 0x8C61C1B5;
$in[6]  = 0x0DF84611;
$in[7]  = 0x6DCD3B1D;
$in[8]  = 0xee24f319;
$in[9]  = 0xDF9B3D85;
$in[10] = 0x14121E4B;
$in[11] = 0x5AC5AA32;
$in[12] = 0x76021D29;
$in[13] = 0x09C74829;
$in[14] = 0xEDEBC68D;
$in[15] = 0xB8B8C25E;

salsa208_word_specification($out, $in);

echo "<pre>";
for ($i = 0; $i < 16; $i++)
{
	$in[$i] = sprintf('%X', $in[$i]);
	$in[$i] = str_pad($in[$i], 8, "0", STR_PAD_LEFT);
}
print_r($in);
print_r($out);
for ($i = 0; $i < 16; $i++)
{
	$out[$i] = sprintf('%X', $out[$i]);
	$out[$i] = str_pad($out[$i], 8, "0", STR_PAD_LEFT);
}
print_r($out);
?>


INPUT:
7E879A21 4F3EC986 7CA940E6 41718F26
BAEE555B 8C61C1B5 0DF84611 6DCD3B1D
EE24F319 DF9B3D85 14121E4B 5AC5AA32
76021D29 09C74829 EDEBC68D B8B8C25E

OUTPUT:
A41F859C 6608CC99 3B81CACB 020CEF05
044B2181 A2FD337D FD7B1C63 96682F29
B4393168 E3C9E6BC FE6BC5B7 A06D96BA
E424CC10 2C91745C 24AD673D C7618F81
<?php
echo "</pre>";
*/

















/*
   Algorithm scryptBlockMix

   Parameters:
            r       Block size parameter.
   Input:
            B[0], ..., B[2 * r - 1]
                    Input vector of 2 * r 64-octet blocks.
   Output:
            B'[0], ..., B'[2 * r - 1]
                    Output vector of 2 * r 64-octet blocks.
   Steps:

     1. X = B[2 * r - 1]

     2. for i = 0 to 2 * r - 1 do
          T = X xor B[i]
          X = Salsa (T)
          Y[i] = X
        end for

     3. B' = (Y[0], Y[2], ..., Y[2 * r - 2],
              Y[1], Y[3], ..., Y[2 * r - 1])
*/

//Input vector of 2 * r 64-octet blocks.
//Output vector of 2 * r 64-octet blocks.
//unsigned operators needed?

function scryptBlockXor($a, $b)
{
	if (count($a) !== 16 || count($b) !== 16)
		die("error");//throw here instead

	$temp = array();
	for ($i = 0; $i < 16; $i++)
	{
		$temp[$i] = unsigned_xor32($a[$i], $b[$i]);
	}
	return $temp;
}

function scryptBlockMix(&$out = array(), $in = array(), $r)//in array of arrays of uint values?
{
	$Y = array();

	$X = $in[2 * $r - 1];

	for ($i = 0; $i <= 2 * $r - 1; $i++)// < or <= ??
	{
//          $T = $X ^ $B[$i];//xor blocks together
		$T = scryptBlockXor($X, $in[$i]);
		salsa208_word_specification($X, $T);//Should be one param and output the value? // $X =
		$Y[$i] = $X;
	}

	for ($i = 0; $i <= 2 * $r - 2; $i++)// < or <= ??
		$out[$i] = $Y[$i];
	for ($i = 1; $i <= 2 * $r - 1; $i++)// < or <= ??
		$out[$i] = $Y[$i];

//	B' = (Y[0], Y[2], ..., Y[2 * r - 2],
//	      Y[1], Y[3], ..., Y[2 * r - 1])
}

$B = array(
	array(0xF7CE0B65, 0x3D2D72A4, 0x108CF5AB, 0xE912FFDD,
	      0x777616DB, 0xBB27A70E, 0x8204F3AE, 0x2D0F6FAD,
	      0x89F68F48, 0x11D1E87B, 0xCC3BD740, 0x0A9FFD29,
	      0x094F0184, 0x639574F3, 0x9AE5A131, 0x5217BCD7),
	array(0x89499144, 0x7213BB22, 0x6C25B54D, 0xA86370FB,
	      0xCD984380, 0x374666BB, 0x8FFCB5BF, 0x40C254B0,
	      0x67D27C51, 0xCE4AD5FE, 0xD829C90B, 0x505A571B,
	      0x7F4D1CAD, 0x6A523CDA, 0x770E67BC, 0xEAAF7E89)
);

$OUT = array();

scryptBlockMix($OUT, $B, 1);

echo "<pre>";
print_r($OUT);
?>


INPUT
B[0] =  f7ce0b65 3d2d72a4 108cf5ab e912ffdd
		777616db bb27a70e 8204f3ae 2d0f6fad
		89f68f48 11d1e87b cc3bd740 0a9ffd29
		094f0184 639574f3 9ae5a131 5217bcd7

B[1] =  89499144 7213bb22 6c25b54d a86370fb
		cd984380 374666bb 8ffcb5bf 40c254b0
		67d27c51 ce4ad5fe d829c90b 505a571b
		7f4d1cad 6a523cda 770e67bc eaaf7e89

OUTPUT
B'[0] = a41f859c 6608cc99 3b81cacb 020cef05
		044b2181 a2fd337d fd7b1c63 96682f29
		b4393168 e3c9e6bc fe6bc5b7 a06d96ba
		e424cc10 2c91745c 24ad673d c7618f81

B'[1] = 20edc975 323881a8 0540f64c 162dcd3c
		21077cfe 5f8d5fe2 b1a4168f 953678b7
		7d3b3d80 3b60e4ab 920996e5 9b4d53b6
		5d2a2258 77d5edf5 842cb9f1 4eefe425
<?php
echo "</pre>";
?>