<?php namespace professionalweb\payment\drivers\tinkoff;

use Alcohol\ISO4217;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use professionalweb\payment\Form;
use Illuminate\Contracts\Support\Arrayable;
use professionalweb\payment\contracts\Receipt;
use professionalweb\payment\contracts\PayService;
use professionalweb\payment\contracts\PayProtocol;
use professionalweb\payment\contracts\Form as IForm;
use professionalweb\payment\interfaces\TinkoffProtocol;
use professionalweb\payment\models\PayServiceOption;
use professionalweb\payment\interfaces\TinkoffService;
use professionalweb\payment\contracts\recurring\RecurringPayment;

/**
 * Payment service. Pay, Check, etc
 * @package professionalweb\payment\drivers\tinkoff
 */
class TinkoffDriver implements PayService, TinkoffService, RecurringPayment
{
    /**
     * TinkoffMerchantAPI object
     *
     * @var PayProtocol
     */
    private $transport;

    /**
     * @var TinkoffProtocol
     */
    private $tinkoffProtocol;

    /**
     * Module config
     *
     * @var array
     */
    private $config;

    /**
     * Notification info
     *
     * @var array
     */
    protected $response;

    /**
     * @var bool
     */
    private $needRecurring = false;

    /**
     * @var string
     */
    private $userId;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Pay
     *
     * @param int        $orderId
     * @param int        $paymentId
     * @param float      $amount
     * @param int|string $currency
     * @param string     $paymentType
     * @param string     $successReturnUrl
     * @param string     $failReturnUrl
     * @param string     $description
     * @param array      $extraParams
     * @param Receipt    $receipt
     *
     * @return string
     */
    public function getPaymentLink($orderId,
                                   $paymentId,
                                   float $amount,
                                   string $currency = self::CURRENCY_RUR_ISO,
                                   string $paymentType = self::PAYMENT_TYPE_CARD,
                                   string $successReturnUrl = '',
                                   string $failReturnUrl = '',
                                   string $description = '',
                                   array $extraParams = [],
                                   Receipt $receipt = null): string
    {
        $extraParams['PaymentId'] = $paymentId;
        $DATA = '';
        array_walk($extraParams, function ($val, $key) use (&$DATA) {
            if ($DATA !== '') {
                $DATA .= '|';
            }
            $DATA .= $key . '=' . $val;
        });
        $data = [
            'OrderId'     => $orderId,
            'Amount'      => round($amount * 100),
            'Currency'    => (new ISO4217())->getByAlpha3($currency)['numeric'],
            'Description' => $description,
            'DATA'        => $DATA,
        ];
        if ($receipt instanceof Arrayable) {
            $data['Receipt'] = (string)$receipt;
        }
        if ($this->needRecurring()) {
            $data['Recurrent'] = 'Y';
            $data['CustomerKey'] = $this->getUserId();
        }

        $paymentUrl = $this->getTransport()->getPaymentUrl($data);

        $this->response['PaymentId'] = $this->getTransport()->getPaymentId();

        return $paymentUrl;
    }

    /**
     * Validate request
     *
     * @param array $data
     *
     * @return bool
     */
    public function validate(array $data): bool
    {
        return $this->getTransport()->validate($data);
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Set driver configuration
     *
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Parse notification
     *
     * @param array $data
     *
     * @return PayService
     */
    public function setResponse(array $data): PayService
    {
        $data['DateTime'] = date('Y-m-d H:i:s');
        $this->response = $data;

        return $this;
    }

    /**
     * Get response param by name
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed|string
     */
    public function getResponseParam(string $name, $default = '')
    {
        return Arr::get($this->response, $name, $default);
    }

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->getResponseParam('OrderId');
    }

    /**
     * Get operation status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->getResponseParam('Status');
    }

    /**
     * Is payment succeed
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->getResponseParam('Success', 'false') === 'true';
    }

    /**
     * Get transaction ID
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->getResponseParam('PaymentId');
    }

    /**
     * Get transaction amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return (float)$this->getResponseParam('Amount');
    }

    /**
     * Get error code
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->getResponseParam('ErrorCode');
    }

    /**
     * Get payment provider
     *
     * @return string
     */
    public function getProvider(): string
    {
        return self::PAYMENT_TINKOFF;
    }

    /**
     * Get PAn
     *
     * @return string
     */
    public function getPan(): string
    {
        return $this->getResponseParam('Pan');
    }

    /**
     * Get payment datetime
     *
     * @return string
     */
    public function getDateTime(): string
    {
        return $this->getResponseParam('DateTime');
    }

    /**
     * Set transport/protocol wrapper
     *
     * @param PayProtocol $protocol
     *
     * @return $this
     */
    public function setTransport(PayProtocol $protocol): PayService
    {
        $this->transport = $protocol;

        return $this;
    }

    /**
     * Get transport
     *
     * @return PayProtocol
     */
    public function getTransport(): PayProtocol
    {
        return $this->transport;
    }

    /**
     * Prepare response on notification request
     *
     * @param int $errorCode
     *
     * @return Response
     */
    public function getNotificationResponse(int $errorCode = null): Response
    {
        return response($this->getTransport()->getNotificationResponse($this->response, $this->mapError($errorCode)));
    }

    /**
     * Prepare response on check request
     *
     * @param int $errorCode
     *
     * @return Response
     */
    public function getCheckResponse(int $errorCode = null): Response
    {
        return response($this->getTransport()->getNotificationResponse($this->response, $this->mapError($errorCode)));
    }

    /**
     * Get specific error code
     *
     * @param int $error
     *
     * @return int
     */
    protected function mapError(int $error): int
    {
        $map = [
            self::RESPONSE_SUCCESS => 0,
            self::RESPONSE_ERROR   => 1,
        ];

        return $map[$error] ?? $map[self::RESPONSE_ERROR];
    }

    /**
     * Get last error code
     *
     * @return int
     */
    public function getLastError(): int
    {
        return 0;
    }

    /**
     * Get param by name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParam(string $name)
    {
        return $this->getResponseParam($name);
    }

    /**
     * Get name of payment service
     *
     * @return string
     */
    public function getName(): string
    {
        return self::PAYMENT_TINKOFF;
    }

    /**
     * Get payment id
     *
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->getResponseParam('PaymentId');
    }

    /**
     * Payment system need form
     * You can not get url for redirect
     *
     * @return bool
     */
    public function needForm(): bool
    {
        return false;
    }

    /**
     * Generate payment form
     *
     * @param int     $orderId
     * @param int     $paymentId
     * @param float   $amount
     * @param string  $currency
     * @param string  $paymentType
     * @param string  $successReturnUrl
     * @param string  $failReturnUrl
     * @param string  $description
     * @param array   $extraParams
     * @param Receipt $receipt
     *
     * @return IForm
     */
    public function getPaymentForm($orderId,
                                   $paymentId,
                                   float $amount,
                                   string $currency = self::CURRENCY_RUR,
                                   string $paymentType = self::PAYMENT_TYPE_CARD,
                                   string $successReturnUrl = '',
                                   string $failReturnUrl = '',
                                   string $description = '',
                                   array $extraParams = [],
                                   Receipt $receipt = null): IForm
    {
        return new Form();
    }

    /**
     * Get pay service options
     *
     * @return array
     */
    public static function getOptions(): array
    {
        return [
            (new PayServiceOption())->setType(PayServiceOption::TYPE_STRING)->setLabel('Url')->setAlias('apiUrl'),
            (new PayServiceOption())->setType(PayServiceOption::TYPE_STRING)->setLabel('Merchant Id')->setAlias('merchantId'),
            (new PayServiceOption())->setType(PayServiceOption::TYPE_STRING)->setLabel('Secret key')->setAlias('secretKey'),
        ];
    }

    /**
     * Get payment token
     *
     * @return string
     */
    public function getRecurringPayment(): string
    {
        return $this->getResponseParam('RebillId');
    }

    /**
     * Remember payment fo recurring payments
     *
     * @return RecurringPayment
     */
    public function makeRecurring(): RecurringPayment
    {
        $this->needRecurring = true;

        return $this;
    }

    /**
     * Check payment need to be recurrent
     *
     * @return bool
     */
    public function needRecurring(): bool
    {
        return $this->needRecurring;
    }

    /**
     * Set user id payment will be assigned
     *
     * @param string $id
     *
     * @return RecurringPayment
     */
    public function setUserId(string $id): RecurringPayment
    {
        $this->userId = $id;

        return $this;
    }

    /**
     * Get user id
     *
     * @return null|string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Initialize recurring payment
     *
     * @param string $token
     * @param string $paymentId
     * @param string $orderId
     * @param float  $amount
     * @param string $description
     * @param string $currency
     * @param array  $extraParams
     *
     * @return bool
     */
    public function initPayment(string $token, string $orderId, string $paymentId, float $amount, string $description, string $currency = PayService::CURRENCY_RUR_ISO, array $extraParams = []): bool
    {
        $response = $this->getTinkoffProtocol()->paymentByToken([
            'RebillId' => $token,
        ]);

        return ((int)$response['ErrorCode']) === 0;
    }

    /**
     * @param TinkoffProtocol $protocol
     *
     * @return TinkoffService
     */
    public function setTinkoffProtocol(TinkoffProtocol $protocol): self
    {
        $this->tinkoffProtocol = $protocol;

        return $this->setTransport($protocol);
    }

    /**
     * @return TinkoffProtocol
     */
    public function getTinkoffProtocol(): TinkoffProtocol
    {
        return $this->tinkoffProtocol;
    }
}