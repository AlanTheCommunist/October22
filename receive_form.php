<?php
$_SESSION['rt'] = $_POST['rt'];
require 'jwtclass.php';

$user = "adminprogweb";
$pass = "ProgWeb3";
$db = "progweb3";
$server = "127.0.0.1";
$conn = mysqli_connect($server, $user, $pass, $db);

if($conn->connect_errno)
{
    die("Connection error: " . $conn->connect_error);
}else{
    $idUsuario = $_POST['usuario'];
    $senhaUsuario = $_POST['senha'];
    $sql = "select * from usuarios WHERE idusuario = '".$idUsuario."' and senhausuario = '".$senhaUsuario ."'";
    $resultQuery = mysqli_query($conn, $sql);

    if($resultQuery->num_rows == 0){
        die("Incorrect username, password or token");
    };

    $arrayQuery = $resultQuery-> fetch_assoc();
    echo 'User: '.$arrayQuery['idusuario'].'Password: '.$arrayQuery['senhausuario'];
    $expAccess = time() + (60*1);
    $payloadAccess = [
        'usuario' => $arrayQuery['nomeusuario'],
        'email' => $arrayQuery['email'],
        'exp' => $expAccess,
        'token' => 'access'
    ];

    $expRefresh = time() + (60*5);
    $payloadRefresh = [
        'usuario' => $arrayQuery['nomeusuario'],
        'email' => $arrayQuery['email'],
        'exp' => $expRefresh,
        'token' => 'refresh'
    ];

    $JWT = new myJWT;
    $accessToken = $JWT->criaToken($payloadAccess);
    $refreshToken = $JWT->criaToken($payloadRefresh);

    echo 'Token: '.$accessToken.'Refresh Token: '.$refreshToken;

    if($JWT->validaToken($accessToken)){
        if($JWT->expiraToken($accessToken)){
            echo "Sim, é válido";    
        }else{
            echo "Nao, ele esta expirado";
        }
    }else{
        echo "Nao, é inválido";
    }

    echo "<br>";
    echo "Validade do refresh token: ";

    if($JWT->validaToken($refreshToken)){
        if($JWT->expiraToken($accessToken)){
            echo "Sim, é válido";    
        }else{
            echo "Nao, ele esta expirado";
        }
    }else{
        echo "Nao, é inválido";
    }

    $accessToken = base64_decode($accessToken);
    $accessToken = json_decode($accessToken, true);
    echo $accessToken;
}


?>