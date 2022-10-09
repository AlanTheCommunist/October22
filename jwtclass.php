<?php
class myJWT {
    private $senha = "SenhaSecreta";
    public function criaToken($payload){
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
         ];
         
         $header = json_encode($header);
         $header = base64_encode($header);
        
         $payload = json_encode($payload);
         $payload = base64_encode($payload);
        
         $signature = hash_hmac('sha256',"$header.$payload",$this->senha,true);
         $signature = base64_encode($signature);
        
         return "$header.$payload.$signature";
    }
    public function validaToken($jwt){
         $part = explode(".",$jwt);
         $header = $part[0];
         $payload = $part[1];
         $signature = $part[2];
        
         $signatureCheck = hash_hmac('sha256',"$header.$payload",$this->senha,true);
         $signatureCheck = base64_encode($signatureCheck);
         if ($signature == $signatureCheck){
            $retorno = true;
         }else {
            $retorno = false;
         }
        
         return $retorno;
    }

    public function expiraToken($jwt){
        $part = explode(".",$jwt);
        $header = $part[0];
        $payload = $part[1];
        $signature = $part[2];
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
  
        if ($payload['exp'] >= time()){
           $retorno = true;
        }else {
           $retorno = false;
        }
       
        return $retorno;
  
    }

    public function utilizaRefresh($conn, $token){
        $part = explode(".",$token);
        $header = $part[0];
        $payload = $part[1];
        $signature = $part[2];
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
  
        if($payload['token'] == 'refresh'){
           $sql = "INSERT INTO blacklist(refreshtoken) VALUES ('".$token."')";
           mysqli_query($conn, $sql);
  
           $newAccess = $payload;
           $newRefresh = $payload;
           $newAccess['exp'] = time() + (60*1);
           $newAccess['token'] = 'access';
           $newRefresh['exp'] = time() + (60*5);
           $accessToken = $this->criaToken($newAccess);
           $refreshToken = $this->criaToken($newRefresh);
  
           $newTokens = [
              $accessToken,
              $refreshToken
           ];
  
           return $newTokens;
  
        }else{
           echo "Error, invalid refresh token.";
           return 0;
        }  
    }

    public function blacklist($conn, $token){
        $sql = "SELECT * FROM blacklist WHERE refreshtoken = '".$token."'";
        $resultQuery = mysqli_query($conn, $sql);
  
        return $resultQuery->num_rows;
        
      }
}
?>