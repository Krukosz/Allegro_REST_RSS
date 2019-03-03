<?php
namespace Allegro\REST;

class Api extends Resource
{

    const API_URI = 'https://api.allegro.pl';

    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';

    const AUTHORIZATION_URI = 'https://allegro.pl/auth/oauth/authorize';

    /**
     * Api constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $apiKey
     * @param string $redirectUri
     * @param null|string $accessToken
     * @param null|string $refreshToken
     */
    public function __construct($clientId, $clientSecret, $apiKey, $redirectUri,
                                $accessToken = null, $refreshToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->apiKey = $apiKey;
        $this->redirectUri = $redirectUri;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return static::API_URI;
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    
    /**
     * @return null|string
     */
    public function getExpiryTimeToken()
    {
        return $this->expiryTimeToken;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getAuthorizationUri()
    {
        $data = array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'api-key' => $this->apiKey,
            'redirect_uri' => $this->redirectUri
        );

        return static::AUTHORIZATION_URI . '?' . http_build_query($data);
    }

    /**
     * @param string $code
     * @return object
     */
    public function getNewAccessToken($code)
    {
        $data = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'api-key' => $this->apiKey,
            'redirect_uri' => $this->redirectUri
        );

        return $this->requestAccessToken($data);
    }
    
    /**
     * @return object
     */
    public function getNewAccessTokenDevice()
    {
        $data = array(
            'grant_type' => 'client_credentials'
        );
        
        return $this->requestAccessTokenDevice($data);
    }
    
    public function checkAccessTokenDevice($json_location)
    {   
        $responseAuth = array(
            'headers' => null,
            'content' => null
        );
        if(file_exists($json_location)) {
            $tokenReadData = json_decode(file_get_contents($json_location), true);
            if (array_key_exists('token_string', $tokenReadData) && array_key_exists('token_expiry', $tokenReadData)) {
                $tokenString = $tokenReadData['token_string'];
                $tokenExpiry = $tokenReadData['token_expiry'];
                if ($tokenExpiry > time()-600) {
                    //token ok
                    $this->accessToken = $tokenString;
                    $this->expiryTimeToken = $tokenExpiry;
                }
                else {
                    //new token needed
                   
                    $responseAuth = $this->getNewAccessTokenDevice();
                    $this->saveTokenData($json_location);
                }
            }
            else {
                $responseAuth = $this->getNewAccessTokenDevice();
                $this->saveTokenData($json_location);
            }
        }
        else {
            /// new file & token needed
            if (fopen($json_location, 'w')) {
                chmod($json_location, 0600);
                $this->getNewAccessTokenDevice();
                $this->saveTokenData($json_location);
            }
            else {
                die("Unable to create file <b>" . $json_location . ".</b> Check write permissions.");
            }
        }
        return $responseAuth;

    }
    
    /**
     * @return object
     */
    public function refreshAccessToken()
    {
        $data = array(
            'grant_type' => 'refresh_token',
            'api-key' => $this->apiKey,
            'refresh_token' => $this->refreshToken,
            'redirect_uri' => $this->redirectUri
        );

        return $this->requestAccessToken($data);
    }
    
    /**
     * @param string $file
     * @return bool
     */
    
    private function saveTokenData($file) {
        if (is_writeable($file)) {
            $tokenNewData = array('token_string' => $this->accessToken, 'token_expiry' => $this->expiryTimeToken);
            $tokenNewJson = json_encode($tokenNewData);
            file_put_contents($file, $tokenNewJson);
        }
        else {
            die("File <b>" . $file . "</b> has no write permissions. Unable to save accessToken.");
        }
    }
    
    /**
     * @param array $data
     * @return object
     */
    
    private function requestAccessTokenDevice($data)
    {
        $authorization = base64_encode($this->clientId . ':' . $this->clientSecret);
        
        $headers = array(
            "Authorization: Basic $authorization",
            "Content-Type: application/x-www-form-urlencoded"
        );
        
        $data = http_build_query($data);
        
        $response = $this->sendHttpRequest(static::TOKEN_URI, 'POST', $headers, $data);
        
        $data = json_decode($response['content']);
        
        if (isset($data->access_token))
        {
            $this->accessToken = $data->access_token;
            $this->expiryTimeToken = time () + $data->expires_in;
        }
        return $response;
    }
    /**
     * @param array $data
     * @return object
     */
    private function requestAccessToken($data)
    {
        $authorization = base64_encode($this->clientId . ':' . $this->clientSecret);

        $headers = array(
            "Authorization: Basic $authorization",
            "Content-Type: application/x-www-form-urlencoded"
        );

        $data = http_build_query($data);

        $response = $this->sendHttpRequest(static::TOKEN_URI, 'POST', $headers, $data);
        
        $data = json_decode($response['content']);

        if (isset($data->access_token) && isset($data->refresh_token))
        {
            $this->accessToken = $data->access_token;
            $this->refreshToken = $data->refresh_token;
        }

        return $response;
    }

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var string
     */
    protected $accessToken;
    
    /**
     * @var integer
     */
    protected $expiryTimeToken;

    /**
     * @var string
     */
    protected $refreshToken;
}
