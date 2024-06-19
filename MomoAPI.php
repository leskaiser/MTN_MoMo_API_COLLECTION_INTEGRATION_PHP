<?php

namespace MoMoAPICollection;

class MomoAPI{

  private $apikey;
  protected $providerCallbackHost;
  protected $primary_key;
  protected $targetEnvironment;
  protected $reference_id;
  private $access_token;
  private $external_id;
  private $momo_pay_host;

    /**
     * @return mixed
     */
    public function getMomoPayHost()
    {
        return $this->momo_pay_host;
    }

    /**
     * @param mixed $momo_pay_host
     */
    public function setMomoPayHost($momo_pay_host)
    {
        $this->momo_pay_host = $momo_pay_host;
    }

    /**
     * @return string
     */
    public function getProviderCallbackHost(): string
    {
        return $this->providerCallbackHost;
    }

    /**
     * @param string $providerCallbackHost
     */
    public function setProviderCallbackHost(string $providerCallbackHost)
    {
        $this->providerCallbackHost = $providerCallbackHost;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * @param mixed $primary_key
     */
    public function setPrimaryKey($primary_key)
    {
        $this->primary_key = $primary_key;
    }

    /**
     * @return String
     */
    public function getTargetEnvironment(): string
    {
        return $this->targetEnvironment;
    }

    /**
     * @param String $targetEnvironment
     */
    public function setTargetEnvironment(string $targetEnvironment)
    {
        $this->targetEnvironment = $targetEnvironment;
    }


  public function __construct() {
    return $this;
  }


  private function generate_uuid(): string
  {
    return sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );
  }

  private function createAPIUser()
  {
    $reference_id = $this->generate_uuid();

    // Set the request URL and data
    $url = 'https://'. $this->momo_pay_host .'/v1_0/apiuser';
    $data = array(
      'providerCallbackHost' => 'callbacks-do-not-work-in-sandbox.com'
    );

    // Set the headers
    $headers = array(
      'Content-Type: application/json',
      'X-Reference-Id: ' . $reference_id,
      'Ocp-Apim-Subscription-Key: ' . $this->primary_key
    );

    // Initialize cURL
    $curl = curl_init();

    // Set the cURL options
    curl_setopt_array($curl, array(
      CURLOPT_URL =>$url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => $headers
    ));

    // Execute the cURL request
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
      curl_close($curl);
    } else {
      //Get http status code
      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      // Close the cURL session
      curl_close($curl);
      // Output the response status
      if ($httpcode != 201) {
        echo 'API user creation failed, Response status code is : ' . $httpcode;
        echo "<br>";
        echo "Error : " . $response;
        die();
      }
    }
    $this->reference_id = $reference_id;
  }

  private function createApikey()
  {
    $this->createAPIUser();
    $secodary_key = $this->reference_id;

    //GET API USER CREATED
    $url = "https://". $this->momo_pay_host ."/v1_0/apiuser/" . $secodary_key . "/apikey";

    $headers = array(
      'Content-Type: application/json',
      'Ocp-Apim-Subscription-Key: ' . $this->primary_key
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => ''
    ));

    curl_setopt($curl, CURLOPT_USERPWD, $this->primary_key . ':');
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
    }
    curl_close($curl);

    // Parse the JSON response
    $data = json_decode($response);

    // Check if the API key was generated successfully
    if ($data->apiKey) {
        $this->apikey =  $data->apiKey;
    } else {
        $this->apikey =  '';
      echo "Failed to generate API key";
      die();
    }
  }

  private function createAccessToken()
  {
    $this->createApikey();

    $url = "https://". $this->momo_pay_host ."/collection/token/";
    $headers = array(
      'Authorization: Basic ' . base64_encode($this->reference_id.':'.$this->apikey),
      'Ocp-Apim-Subscription-Key: ' . $this->primary_key
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => $headers
    ));
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
    }
    curl_close($curl);
    $data = json_decode($response);

    if ($data->access_token) {
        $this->access_token = $data->access_token;
    } else {
        $this->access_token = '';
        die();
    }
  }

  public function requestToPay($phone = '237679465319', $amount = 100, $currency = 'EUR')
  {
    $this->createAccessToken();

    // Set the request URL
    $url = "https://". $this->momo_pay_host ."/collection/v1_0/requesttopay";

    //GENRATE AN EXTERNAL ID 8 DIGITS
    $external_id = $this->generate_uuid();

    //HEADERS
    $headers = array(
      'Authorization: Bearer ' . $this->access_token,
      'X-Reference-Id: ' . $external_id,
      'X-Target-Environment: ' . $this->targetEnvironment,
      'Content-Type: application/json',
      'Ocp-Apim-Subscription-Key: ' . $this->primary_key
    );

    // Set the request body
    $body = array(
      'amount' => $amount,
      'currency' => $currency,
      "externalId" => $external_id,
      'payer' => array(
        'partyIdType' => 'MSISDN',
        'partyId' => $phone
      ),
      'payerMessage' => 'DigitStore MTN MoMo Payment',
      'payeeNote' => 'Thank you for using DigitStore MTN Payment'
    );

    // Encode the request body as JSON
    $json_body = json_encode($body);


    // Initialize cURL
    $curl = curl_init();
    // Set the cURL options
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $json_body
    ));
    // Execute the cURL request
    $response = curl_exec($curl);
    // Check for errors
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
    }

    // Output the response
    // Check for errors
    if (curl_errno($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
    } else {

      //Get http status code
      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      // Close the cURL session
      curl_close($curl);

      // Output the response status
      if ($httpcode == 202) {
        $this->external_id = $external_id;
      }

      return $httpcode;
    }
  }

  public function requestToPayTransactionStatus()
  {
      $url = 'https://'. $this->momo_pay_host .'/collection/v1_0/requesttopay/' . $this->external_id;
      $headers = array(
          "Authorization: Bearer " . $this->access_token,
          "X-Target-Environment: sandbox",
          "Ocp-Apim-Subscription-Key: " . $this->primary_key
      );

      $curl = curl_init();
      curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => $headers
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)) {
          $error_msg = curl_error($curl);
          echo "cURL Error: " . $error_msg;
      }
      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      if ($httpcode != '200'){
          return 0;
      }
      return json_decode($response);
  }

  public function getCurrentBalance()
  {
      if ($this->access_token == null || $this->access_token == ''){
        $this->createAccessToken();
      }

      $headers = array(
          'Authorization: Bearer ' . $this->access_token,
          'X-Target-Environment: ' . $this->targetEnvironment,
          'Ocp-Apim-Subscription-Key: ' . $this->primary_key
      );

      $curl = curl_init();

      $url = 'https://'. $this->momo_pay_host .'/collection/v1_0/account/balance';
      curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => $headers
      ));

      $response = curl_exec($curl);
      if (curl_errno($curl)) {
          $error_msg = curl_error($curl);
          echo "cURL Error: " . $error_msg;
      }

      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      return json_decode($response);

  }

}

