<?php

     //Para enviar la notificaciÃ³n push necesitamos hacer la solicitud http al servidor firebase. 
    //Curl es una librerÃ­a de funciones para conectar con servidores
class Firebase {
 
    public function send($registration_ids, $message) {
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
            'priority'=>"high",
            'ttl'=>"15"
        );
        return $this->sendPushNotification($fields);
    }
    
    
   // Esta funciÃ³n harÃ¡ que la solicitud curl real al servidor firebase  y luego el mensaje sea enviado 

    private function sendPushNotification($fields) {
         
        //importing  constant files
        
        
        //firebase server url to send the curl request
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        //building headers for the request
        $headers = array(
            'Authorization: key= AAAAyYGjJ1k:APA91bHa-1GFjuI8u7V64uUG47Ekjfi-wvvNguooJR3yepaKpMYD5jUdoUoMLfiLheqzy9CsiAJ4IfIiKLuYov98V-ryiuCXosX_3q986mprSYLl_acLniFGlKU3D2UcHUcxPV8THJm1',
            'Content-Type: application/json'
        );
 
        //Initializing curl to open a connection
        $ch = curl_init();
 
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);
 
        //adding headers 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //adding the fields in json format 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        //finally executing the curl request 
        $result = curl_exec($ch);
        if ($result === FALSE) {
           // die('Curl failed: ' . curl_error($ch));
        }
 
        //Now close the connection
        curl_close($ch);
 
        //and return the result 
        return $result;
    }
}
?>