<?php



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



function unsigned_or32($a, $b)
{
	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 || $b3) ? 0x80000000 : 0;

	return (($a1 | $b1) | ($a2 | $b2)) + $c;
}
function unsigned_xor32($a, $b)//do we need to break it up into 16 bits? Can we do 31 + 1 bit?
{
	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 != $b3) ? 0x80000000 : 0;

	return (($a1 ^ $b1) | ($a2 ^ $b2)) + $c;
}
function unsigned_and32($a, $b)
{
	$a1 = $a & 0x7FFF0000;
	$a2 = $a & 0x0000FFFF;
	$a3 = $a & 0x80000000;
	$b1 = $b & 0x7FFF0000;
	$b2 = $b & 0x0000FFFF;
	$b3 = $b & 0x80000000;

	$c = ($a3 && $b3) ? 0x80000000 : 0;

	return (($a1 & $b1) | ($a2 & $b2)) + $c;
}
function unsigned_rshift32($a, $b)
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
function unsigned_lshift32($a, $b)
{
	$a1 = $a << $b;
//	$c = ($a & (0x40000000 >> ($b - 1))) ? 0x80000000 : 0;
//	return $a1 + ($c * 2);//Why?
	return bindec(decbin($a1));//Better way than bindec(decbin, is it needed at all?
}




echo unsigned_lshift32(0x6D472CEA, 1)." = 3666762196<br>";//1	0xDA8E59D4 3666762196
echo unsigned_lshift32(0x6D472CEA, 2)." = 3038557096<br>";//2	0xB51Cb3A8 3038557096
echo unsigned_lshift32(0x6D472CEA, 3)." = 1782146896<br>";//3	0x6A396750 1782146896
echo unsigned_lshift32(0x6D472CEA, 4)." = 3564293792<br>";//4	0xD472CEA0 3564293792
echo unsigned_lshift32(0x6D472CEA, 5)." = 2833620288<br>";//5	0xA8E59D40 2833620288



die();

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


?>