<?php
function idtag_des_decode($key,$encrypted)
{
    $encrypted = base64_decode($encrypted);

    $td = mcrypt_module_open(MCRYPT_DES,'',MCRYPT_MODE_CBC,''); //使用MCRYPT_DES算法,cbc模式
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    mcrypt_generic_init($td, $key, $key);       //初始处理

    $decrypted = mdecrypt_generic($td, $encrypted);       //解密

    mcrypt_generic_deinit($td);       //结束
    mcrypt_module_close($td);

    $y=pkcs5_unpad($decrypted);
    return $y;
}

function idtag_des_encode($key,$text)
{

    $y=pkcs5_pad($text);

    $td = mcrypt_module_open(MCRYPT_DES,'',MCRYPT_MODE_CBC,''); //使用MCRYPT_DES算法,cbc模式
   /// $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    mcrypt_generic_init($td, $key, $key);       //初始处理
    $encrypted = mcrypt_generic($td, $y);       //解密
    mcrypt_generic_deinit($td);       //结束
    mcrypt_module_close($td);

    return base64_encode($encrypted);
}

function pkcs5_pad($text,$block=8)
{
        $pad = $block - (strlen($text) % $block);
        return $text . str_repeat(chr($pad), $pad);
}


function pkcs5_unpad($text)
{
   $pad = ord($text{strlen($text)-1});
   if ($pad > strlen($text)) return $text;
   if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return $text;
   return substr($text, 0, -1 * $pad);
}
if(file_get_contents("php://input")==null){
echo '��Ȩ��emsneiwang.com���У�';
}else{
$str=substr(idtag_des_decode('20140406',file_get_contents("php://input")),6);
$key = substr(sprintf("%1.0f", ($_COOKIE['Query-Time'])+2014),-8);

$express=idtag_des_decode($key,$str);
$url='http://www.emsneiwang.com/api/forwxems.php?order='.$express;  
$fp = fopen($url, 'r');  
stream_get_meta_data($fp);  
while(!feof($fp)) {
$result .= fgets($fp, 1024);
}
echo $result;
fclose($fp);
}
?>