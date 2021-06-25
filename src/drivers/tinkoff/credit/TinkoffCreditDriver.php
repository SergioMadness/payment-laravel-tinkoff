<?php namespace professionalweb\payment\drivers\tinkoff\credit;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use professionalweb\payment\contracts\Form;
use professionalweb\payment\Form as FormModel;
use professionalweb\payment\contracts\Receipt;
use professionalweb\payment\contracts\PayService;
use professionalweb\payment\contracts\PayProtocol;
use professionalweb\payment\models\PayServiceOption;
use professionalweb\payment\contracts\PaymentApprove;
use professionalweb\payment\interfaces\models\Credit;
use professionalweb\payment\interfaces\TinkoffProtocol;
use professionalweb\payment\interfaces\TinkoffCreditService;

/**
 * Driver to work with credits
 * @package professionalweb\payment\drivers\tinkoff\credit
 */
class TinkoffCreditDriver implements PayService, TinkoffCreditService, PaymentApprove
{
    /**
     * @var TinkoffProtocol
     */
    private $tinkoffCreditProtocol;

    /**
     * TinkoffMerchantAPI object
     *
     * @var PayProtocol
     */
    private $transport;

    /** @var array */
    private $response;

    /**
     * Get name of payment service
     *
     * @return string
     */
    public function getName(): string
    {
        return self::PAYMENT_TINKOFF_CREDIT;
    }

    /**
     * Pay
     *
     * @param mixed   $orderId
     * @param mixed   $paymentId
     * @param float   $amount
     * @param string  $currency
     * @param string  $paymentType
     * @param string  $successReturnUrl
     * @param string  $failReturnUrl
     * @param string  $description
     * @param array   $extraParams
     * @param Receipt $receipt
     *
     * @return string
     */
    public function getPaymentLink($orderId, $paymentId, float $amount, string $currency = self::CURRENCY_RUR,
                                   string $paymentType = self::PAYMENT_TYPE_CARD, string $successReturnUrl = '',
                                   string $failReturnUrl = '', string $description = '', array $extraParams = [],
                                   Receipt $receipt = null): string
    {
        $result = $this->getTinkoffProtocol()->createCredit([
            'sum'         => $amount,
            'items'       => $extraParams['products'] ?? [],
            'orderNumber' => $orderId,
            'failURL'     => $failReturnUrl,
            'successURL'  => $successReturnUrl,
            'returnURL'   => $failReturnUrl,
            'webhookURL'  => $extraParams['webhookURL'] ?? null,
            'values'      => [
                'contact' => [
                    'fio'         => [
                        'lastName'   => $extraParams['lastName'] ?? '',
                        'firstName'  => $extraParams['firstName'] ?? '',
                        'middleName' => $extraParams['middleName'] ?? '',
                    ],
                    'mobilePhone' => $extraParams['phone'] ?? '',
                    'email'       => $extraParams['email'] ?? '',
                ],
            ],
        ], config('payment.tinkoff-credit.isDemo', false));

        return $result->getLink() ?? $failReturnUrl;
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
     * @param mixed   $orderId
     * @param mixed   $paymentId
     * @param float   $amount
     * @param string  $currency
     * @param string  $paymentType
     * @param string  $successReturnUrl
     * @param string  $failReturnUrl
     * @param string  $description
     * @param array   $extraParams
     * @param Receipt $receipt
     *
     * @return Form
     */
    public function getPaymentForm($orderId, $paymentId, float $amount, string $currency = self::CURRENCY_RUR,
                                   string $paymentType = self::PAYMENT_TYPE_CARD, string $successReturnUrl = '',
                                   string $failReturnUrl = '', string $description = '', array $extraParams = [],
                                   Receipt $receipt = null): Form
    {
        return new FormModel();
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
        return true;
//        return $this->getTinkoffProtocol()->validate($data);
    }

    /**
     * Parse notification
     *
     * @param array $data
     *
     * @return $this
     */
    public function setResponse(array $data): PayService
    {
        $this->response = $data;

        return $this;
    }

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->getParam('id');
    }

    /**
     * Get payment id
     *
     * @return string
     */
    public function getPaymentId(): string
    {
        return '';//$this->getParam('id');
    }

    /**
     * Get operation status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->getParam('status');
    }

    /**
     * Is payment succeed
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->getParam('status') === Credit::STATUS_SIGNED;
    }

    /**
     * Get transaction ID
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->getParam('id');
    }

    /**
     * Get transaction amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->getParam('credit_amount');
    }

    /**
     * Get error code
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return '';
    }

    /**
     * Get payment provider
     *
     * @return string
     */
    public function getProvider(): string
    {
        return self::PAYMENT_TINKOFF_CREDIT;
    }

    /**
     * Get PAN
     *
     * @return string
     */
    public function getPan(): string
    {
        return '';
    }

    /**
     * Get payment datetime
     *
     * @return string
     */
    public function getDateTime(): string
    {
        return $this->getParam('created_at');
    }

    /**
     * Get payment currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return self::CURRENCY_RUR;
    }

    /**
     * Get card type. Visa, MC etc
     *
     * @return string
     */
    public function getCardType(): string
    {
        return '';
    }

    /**
     * Get card expiration date
     *
     * @return string
     */
    public function getCardExpDate(): string
    {
        return '';
    }

    /**
     * Get cardholder name
     *
     * @return string
     */
    public function getCardUserName(): string
    {
        return '';
    }

    /**
     * Get card issuer
     *
     * @return string
     */
    public function getIssuer(): string
    {
        return '';
    }

    /**
     * Get e-mail
     *
     * @return string
     */
    public function getEmail(): string
    {
        return '';
    }

    /**
     * Get payment type. "GooglePay" for example
     *
     * @return string
     */
    public function getPaymentType(): string
    {
        return '';
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
        return \response('');
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
        return \response('');
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
        return Arr::get($this->response, $name, '');
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
            (new PayServiceOption())->setType(PayServiceOption::TYPE_BOOL)->setLabel('Is demo')->setAlias('isDemo'),
        ];
    }

    /**
     * @param TinkoffProtocol $protocol
     *
     * @return $this
     */
    public function setTinkoffProtocol(TinkoffProtocol $protocol): self
    {
        $this->tinkoffCreditProtocol = $protocol;

        return $this->setTransport($protocol);
    }

    /**
     * @return TinkoffProtocol
     */
    public function getTinkoffProtocol(): TinkoffProtocol
    {
        return $this->tinkoffCreditProtocol;
    }

    /**
     * Approve transaction by id
     *
     * @param string $id
     *
     * @return bool
     */
    public function approveTransaction($id): bool
    {
        $this->getTinkoffProtocol()->commitCredit($id);

        return true;
    }

    /**
     * Get transaction status
     *
     * @param string $id
     *
     * @return string
     */
    public function getTransactionStatus($id): string
    {
        $credit = $this->getTinkoffProtocol()->getCreditInfo($id);

        return $credit->getStatus();
    }
}