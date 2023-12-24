<?php

namespace Omnipay\MercadoPago\Message;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class PurchaseRequest extends AbstractRequest
{
    public function sendData($data)
    {
        MercadoPagoConfig::setAccessToken($this->getAccessToken());
        $client = new PaymentClient();
        $response = [];
        try {
            $request = [
                "transaction_amount" => 100,
                "token" => "YOUR_CARD_TOKEN",
                "description" => "description",
                "installments" => 1,
                "payment_method_id" => "visa",
                "payer" => [
                    "email" => "user@test.com",
                ]
            ];

            // Step 5: Make the request
            $payment = $client->create($request);
            $response['error'] = false;
            $response['payment'] = $payment;
            $response['msg'] = 'Pagamento realizado';
        } catch (MPApiException $e) {
            $response['error'] = true;
            $response['msg'] = $e->getApiResponse()->getContent();
        } catch (\Exception $e) {
            $response['error'] = true;
            $response['msg'] = $e->getMessage();
        }
        return new PurchaseResponse($this, $response);
    }

    public function getItemData()
    {
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {

                $item_array = [];
                $item_array['title'] = $item->getName();
                $item_array['description'] = $item->getDescription();
                //                $item_array['category_id'] = $item->getCategoryId();
                $item_array['quantity'] = (int)$item->getQuantity();
                $item_array['currency_id'] = $this->getCurrency();
                $item_array['unit_price'] = (float)($this->formatCurrency($item->getPrice()));

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    public function getData()
    {
        // $data = array(
        //     "items" => array(
        //         array(
        //             'title'       => 'PurchaseTest',
        //             'quantity'    => 1,
        //             'category_id' => 'tickets',
        //             'currency_id' => 'BRL',
        //             'unit_price'  => 10.0
        //         )
        //     )
        // );

        $this->validate('amount', 'description', 'cardReference', 'card');

        $items = $this->getItemData();
        $purchaseObject = [
            'additional_info' => [
                'items' => $items,
            ],

            'transaction_amount' => $this->getAmount(),
            'token' => $this->getCardReference(),
            'description' => $this->getDescription(),
            'installments' => 1, // ????
            'payment_method_id' =>  $this->getCard()->getBrand(),
            'payer' => [
                'name' => $this->getCard()->getName(),
                'email' => $this->getCard()->getEmail()
            ]

        ];
        return $purchaseObject;
    }
}
