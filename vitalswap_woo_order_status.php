<?php
  
 $apiUrl =   $_GET['returnUrl'];
 $apiUsername = $_GET['ck'];           
 $apiPassword = $_GET['cs'];
 $order_id = $_GET['oid'];
 
 $url = $apiUrl . '/wp-json/wc/v3/orders/' . $order_id;
 $updateData = [
    
     'status' => 'completed'
    
 ];
 
 
  $result = updateOrderStatus($url, $apiUsername, $apiPassword, $updateData);
  return  $result;
 
     function updateOrderStatus(string $url, string $username, string $password, $data, array $options = []): array
 {
   
     $ch = curl_init();
     if ($ch === false) {
         return [
             'response_body' => null,
             'http_code' => 0,
             'error' => 'Failed to initialize cURL session.',
         ];
     }
 
    
     $payload = '';
     $contentType = 'application/json';
 
     if (is_array($data)) {
         // Encode array data as JSON
         $payload = json_encode($data);
         if ($payload === false) {
             curl_close($ch);
             return [
                 'response_body' => null,
                 'http_code' => 0,
                 'error' => 'Failed to encode data as JSON: ' . json_last_error_msg(),
             ];
         }
     } elseif (is_string($data)) {
       
         $payload = $data;
        
     } else {
          curl_close($ch);
          return [
             'response_body' => null,
             'http_code' => 0,
             'error' => 'Invalid data type provided. Must be array or string.',
          ];
     }
 
    
     // Target URL
     curl_setopt($ch, CURLOPT_URL, $url);
 
     // Specify PUT request method
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
 
     // Set the data payload for the PUT request
     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
     // Return the response instead of outputting it directly
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
     // Follow redirects (optional, adjust as needed)
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 
     // Set a timeout (optional, recommended)
     curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 seconds
 

  
     curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

     curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
 
    
     $headers = [
         'Content-Type: ' . $contentType,
         'Content-Length: ' . strlen($payload),
        
     ];
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 
   
     // --- Apply any additional options passed to the function ---
     if (!empty($options)) {
         curl_setopt_array($ch, $options);
     }
 
 
     $responseBody = curl_exec($ch);
 
     //  Check for Errors & Get Info ---
     $error = null;
     $httpCode = 0;
 
     if ($responseBody === false) {
         // cURL execution failed
         $error = curl_error($ch);
     } else {
         // Get the HTTP status code
         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     }
 
     // Close cURL Session ---
     curl_close($ch);
 
     // Return Results ---
     return [
         'response_body' => $responseBody,
         'http_code' => $httpCode,
         'error' => $error,
     ];
 }
