<?php

/**
 * Sistema E-SIC - OAuth Handler
 * 
 * Gerenciamento de autenticação OAuth2 (Google e Gov.br)
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class OAuthHandler
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'google' => [
                'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
                'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
                'redirect_uri' => url('/auth/google/callback'),
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'user_info_url' => 'https://www.googleapis.com/oauth2/v2/userinfo',
                'scope' => 'openid email profile'
            ],
            'govbr' => [
                'client_id' => $_ENV['GOVBR_CLIENT_ID'] ?? '',
                'client_secret' => $_ENV['GOVBR_CLIENT_SECRET'] ?? '',
                'redirect_uri' => url('/auth/govbr/callback'),
                'auth_url' => 'https://sso.staging.acesso.gov.br/authorize',
                'token_url' => 'https://sso.staging.acesso.gov.br/token',
                'user_info_url' => 'https://sso.staging.acesso.gov.br/userinfo',
                'scope' => 'openid email profile govbr_empresa cpf'
            ]
        ];
    }

    /**
     * Obter URL de autorização do Google
     */
    public function getGoogleAuthUrl()
    {
        return $this->getAuthUrl('google');
    }

    /**
     * Obter URL de autorização do Gov.br
     */
    public function getGovBrAuthUrl()
    {
        return $this->getAuthUrl('govbr');
    }

    /**
     * Processar callback do Google
     */
    public function handleGoogleCallback($code)
    {
        return $this->handleCallback('google', $code);
    }

    /**
     * Processar callback do Gov.br
     */
    public function handleGovBrCallback($code)
    {
        return $this->handleCallback('govbr', $code);
    }

    /**
     * Gerar URL de autorização
     */
    private function getAuthUrl($provider)
    {
        if (!isset($this->config[$provider])) {
            throw new Exception("Provider OAuth não configurado: {$provider}");
        }

        $config = $this->config[$provider];
        
        if (empty($config['client_id'])) {
            throw new Exception("Client ID não configurado para {$provider}");
        }

        // Gerar state para segurança
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $_SESSION['oauth_provider'] = $provider;

        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => $config['scope'],
            'response_type' => 'code',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return $config['auth_url'] . '?' . http_build_query($params);
    }

    /**
     * Processar callback OAuth
     */
    private function handleCallback($provider, $code)
    {
        if (!isset($this->config[$provider])) {
            throw new Exception("Provider OAuth não configurado: {$provider}");
        }

        // Verificar state
        $state = $_GET['state'] ?? '';
        if (empty($state) || $state !== ($_SESSION['oauth_state'] ?? '')) {
            throw new Exception('Estado OAuth inválido');
        }

        // Limpar state da sessão
        unset($_SESSION['oauth_state']);
        unset($_SESSION['oauth_provider']);

        // Trocar código por token
        $tokenData = $this->exchangeCodeForToken($provider, $code);
        
        if (!$tokenData || !isset($tokenData['access_token'])) {
            throw new Exception('Falha ao obter token de acesso');
        }

        // Obter informações do usuário
        $userInfo = $this->getUserInfo($provider, $tokenData['access_token']);
        
        if (!$userInfo) {
            throw new Exception('Falha ao obter informações do usuário');
        }

        return $userInfo;
    }

    /**
     * Trocar código por token de acesso
     */
    private function exchangeCodeForToken($provider, $code)
    {
        $config = $this->config[$provider];

        $data = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['redirect_uri']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['token_url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Erro ao trocar código por token: HTTP {$httpCode}");
        }

        return json_decode($response, true);
    }

    /**
     * Obter informações do usuário
     */
    private function getUserInfo($provider, $accessToken)
    {
        $config = $this->config[$provider];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['user_info_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Erro ao obter informações do usuário: HTTP {$httpCode}");
        }

        $userInfo = json_decode($response, true);
        
        // Normalizar dados entre provedores
        return $this->normalizeUserInfo($provider, $userInfo);
    }

    /**
     * Normalizar informações do usuário entre diferentes provedores
     */
    private function normalizeUserInfo($provider, $userInfo)
    {
        $normalized = [];

        switch ($provider) {
            case 'google':
                $normalized = [
                    'id' => $userInfo['id'] ?? '',
                    'email' => $userInfo['email'] ?? '',
                    'name' => $userInfo['name'] ?? '',
                    'given_name' => $userInfo['given_name'] ?? '',
                    'family_name' => $userInfo['family_name'] ?? '',
                    'picture' => $userInfo['picture'] ?? '',
                    'verified_email' => $userInfo['verified_email'] ?? false
                ];
                break;

            case 'govbr':
                $normalized = [
                    'sub' => $userInfo['sub'] ?? '',
                    'email' => $userInfo['email'] ?? '',
                    'name' => $userInfo['name'] ?? '',
                    'given_name' => $userInfo['given_name'] ?? '',
                    'family_name' => $userInfo['family_name'] ?? '',
                    'cpf' => $userInfo['cpf'] ?? '',
                    'phone_number' => $userInfo['phone_number'] ?? ''
                ];
                break;

            default:
                throw new Exception("Provider não suportado: {$provider}");
        }

        return $normalized;
    }

    /**
     * Verificar se OAuth está configurado para um provider
     */
    public function isConfigured($provider)
    {
        if (!isset($this->config[$provider])) {
            return false;
        }

        $config = $this->config[$provider];
        
        return !empty($config['client_id']) && !empty($config['client_secret']);
    }

    /**
     * Obter lista de providers configurados
     */
    public function getConfiguredProviders()
    {
        $configured = [];
        
        foreach ($this->config as $provider => $config) {
            if ($this->isConfigured($provider)) {
                $configured[] = $provider;
            }
        }
        
        return $configured;
    }
}