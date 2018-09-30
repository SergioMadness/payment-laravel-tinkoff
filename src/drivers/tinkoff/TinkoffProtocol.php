<?php namespace professionalweb\payment\drivers\tinkoff;

use professionalweb\payment\contracts\PayProtocol;

require_once 'TinkoffMerchantAPI.php';

/**
 * Wrapper for Tinkoff protocol
 * @package professionalweb\payment\drivers\tinkoff
 */
class TinkoffProtocol extends \TinkoffMerchantAPI implements PayProtocol
{

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
}