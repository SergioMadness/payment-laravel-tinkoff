<?php namespace professionalweb\payment\drivers\tinkoff;

use professionalweb\payment\models\Credit;
use professionalweb\payment\contracts\PayProtocol;
use professionalweb\payment\interfaces\models\Credit as ICredit;
use professionalweb\payment\interfaces\TinkoffProtocol as ITinkoffProtocol;

require_once 'TinkoffMerchantAPI.php';

/**
 * Wrapper for Tinkoff protocol
 * @package professionalweb\payment\drivers\tinkoff
 */
class TinkoffProtocol extends \TinkoffMerchantAPI implements PayProtocol, ITinkoffProtocol
{
    /** @var string */
    private $terminalKey;

    /** @var string */
    private $secretKey;

    public function __construct($terminalKey, $secretKey, $api_url)
    {
        parent::__construct($terminalKey, $secretKey, $api_url);

        $this->terminalKey = $terminalKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Get payment URL
     *
     * @param mixed $params
     *
     * @return string
     * @throws \Exception
     */
    public function getPaymentUrl(array $params): string
    {
        $this->init($params);
        if ($this->error !== '') {
            throw new \Exception($this->error);
        }

        return $this->paymentUrl;
    }

    /**
     * Validate params
     *
     * @param mixed $params
     *
     * @return bool
     */
    public function validate(array $params): bool
    {
        $result = false;

        if (isset($params['Token'])) {
            $token = $params['Token'];
            unset($params['Token']);
            if ($token !== '' && $this->genToken($params) === $token) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get payment ID
     *
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * Prepare response on notification request
     *
     * @param mixed $requestData
     * @param int   $errorCode
     *
     * @return string
     */
    public function getNotificationResponse($requestData, $errorCode): string
    {
        return $errorCode > 0 ? 'ERROR' : 'OK';
    }

    /**
     * Prepare response on check request
     *
     * @param array $requestData
     * @param int   $errorCode
     *
     * @return string
     */
    public function getCheckResponse($requestData, $errorCode): string
    {
        return $errorCode > 0 ? 'ERROR' : 'OK';
    }

    /**
     * Prepare parameters
     *
     * @param array $params
     *
     * @return array
     */
    public function prepareParams(array $params): array
    {
        return $params;
    }

    /**
     * Payment by card token
     *
     * @param array $data
     *
     * @return array
     */
    public function paymentByToken(array $data): array
    {
        return $this->charge($data);
    }

    /**
     * Create credit request
     *
     * @param array $data
     * @param bool  $isDemo
     *
     * @return ICredit
     * @throws \Exception
     */
    public function createCredit(array $data, bool $isDemo = false): ICredit
    {
        $url = $isDemo ? 'https://forma.tinkoff.ru/api/partners/v2/orders/create-demo' : 'https://forma.tinkoff.ru/api/partners/v2/orders/create';

        $data['shopId'] = $this->terminalKey;
        $data['showcaseId'] = $this->secretKey;

        $result = $this->sendRequest($url, $data);

        return new Credit($result['id'], $result['link']);
    }

    /**
     * Send request to cloudpayments
     *
     * @param string $url
     * @param array  $params
     *
     * @return array
     * @throws \Exception
     */
    protected function sendRequest(string $url, array $params = []): array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
//        curl_setopt($curl, CURLOPT_USERPWD, sprintf('%s:%s', $this->getPublicKey(), $this->getPrivateKey()));
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

        $body = curl_exec($curl);

        $curlStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($curlStatus >= 400) {
            throw new \Exception($body);
        }

        return json_decode($body, true);
    }
}