<?php

require 'JWT.php';

$user ="adminprogweb";
$pass ="ProgWeb3";
$db ="progweb3";
$server ="127.0.0.1";
$conn = mysqli_connect($server, $user, $pass, $db);

if ($conn->connect_errno){
    die("Houve um erro na conexao" . $conn->connect_error);
}else {
    $JWT = new myJWT;

    if($JWT->validaToken($_POST['token']) && $JWT->expiraToken($_POST['token']) && $JWT->blacklist($conn, $_POST['token']) == 0){
        echo "token is valid";
        $newTokens = $JWT->utilizaRefresh($conn, $token);
            if($newTokens != 0){
                echo "<br>Token Expired<br>";

                $acessToken = $newTokens[0];
                $curRefreshToken = $newTokens[1];
            }
    }
}else{
    echo "Access denied, invalid token"
}
?>