<?php

namespace VandersonRamos\FMTransportes\Carriers;

use VandersonRamos\FMTransportes\Services\CarrierHttpClient;
use VandersonRamos\FMTransportes\Helper\Data;
use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Checkout\Facades\Cart;


class Carrier extends AbstractShipping
{

    protected $code = 'vandersonramos_fmtransportes';
    private $is_centimeters = false;
    private $is_grams = false;
    protected $service;


    /**
     * Check some data before starting
     * Carrier constructor.
     */
    public function __construct()
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($this->getConfigData('dimension_type') === 'cm') {
            $this->is_centimeters = true;
        }

        if ($this->getConfigData('weight_type') === 'gr') {
            $this->is_grams = true;
        }

        $this->service = new CarrierHttpClient();
    }

    /**
     * @return array
     */
    public function calculate(): array
    {

        /** @var \Webkul\Checkout\Models\Cart $cart */
        $cart = Cart::getCart();
        $quoteOptions = [];

        $shippingData = [
            'zipCodeOrigin' => Data::cleanZipCode(core()->getConfigData('sales.shipping.origin.zipcode')),
            'zipCodeDestination' => Data::cleanZipCode($cart->shipping_address->postcode),
            'clientCode' => null,
            'totalValue' => (float) number_format($cart->grand_total, 2,'.', ''),
            'totalWeight' => $this->getTotalPackageWeight($cart->items->sum('total_weight')),
            'itens' => []
        ];

        $items = $cart->items()->get()->filter(function($item) {
            return $item->type === 'simple';
        })->values();

        $rates = [];

        foreach ($items as $item) {

            $length = $item->product->depth;
            $height = $item->product->height;
            $width = $item->product->width;

            if ($this->is_centimeters) {

                $shippingData['itens'][] = [
                    'length' => (float) ($length ?: Data::DEFAULT_LENGTH),
                    'height' => (float) ($height ?: Data::DEFAULT_HEIGHT),
                    'width'  => (float) ($width  ?: Data::DEFAULT_WIDTH),
                ];

            } else {
                $shippingData['itens'][] = [
                    'length' => (float) ($length ? $length * 100 : Data::DEFAULT_LENGTH * 100),
                    'height' => (float) ($height ? $height * 100 : Data::DEFAULT_HEIGHT * 100),
                    'width'  => (float) ($width  ? $width * 100  : Data::DEFAULT_WIDTH * 100),
                ];
            }
        }

        if ($this->getConfigData('active_standard')) {

            $shippingData['clientCode'] = $this->getConfigData('standard_client_code');
            $standardResponse = $this->service->requestQuote($shippingData);

            if ($standardResponse['status']) {
                $carrier = $standardResponse['response_message'];
                $carrier->typeZipCode = 'Standard';
                $carrier->code = $this->code . '_standard';
                $quoteOptions[] = $carrier;
            }
        }

        if ($this->getConfigData('active_express')) {

            $shippingData['clientCode'] = $this->getConfigData('express_client_code');
            $expressResponse = $this->service->requestQuote($shippingData);

            if ($expressResponse['status']) {
                $carrier = $expressResponse['response_message'];
                $carrier->typeZipCode = 'Express';
                $carrier->code = $this->code . '_express';
                $quoteOptions[] = $carrier;
            }
        }

        if ($this->getConfigData('rodo_express')) {

            $shippingData['clientCode'] = $this->getConfigData('rodo_client_code');
            $rodoResponse = $this->service->requestQuote($shippingData);

            if ($rodoResponse['status']) {
                $carrier = $rodoResponse['response_message'];
                $carrier->code = $this->code . '_rodo';
                $carrier->typeZipCode = 'Rodo';
                $quoteOptions[] = $carrier;
            }
        }

        if (count($quoteOptions) === 0) {
            return $rates;
        }

        foreach ($quoteOptions as $quoteOption) {
            $rates[] = $this->appendShippingReturn($quoteOption);
        }

        return $rates;
    }

    /**
     * Append shipping value to return
     * @param object $carrier
     * @return object
     */
    protected function appendShippingReturn(object $carrier): object
    {
        $shippingRate = new CartShippingRate;
        $shippingRate->carrier = $this->code;
        $shippingRate->carrier_title = $this->getConfigData('title');
        $shippingRate->method = $carrier->code;
        $title = Data::formatTitle($this->getConfigData('title'), $carrier->typeZipCode, $carrier->deliveryTime);
        $shippingRate->method_title = $title;
        $shippingRate->price = $carrier->value;
        $shippingRate->base_price = $carrier->value;

        return $shippingRate;
    }

    /**
     * @param $weight
     * @return float
     */
    protected function getTotalPackageWeight($weight): float
    {
        if ($this->is_grams) {
            return Data::fixPackageWeight($weight);
        }

        return floatval($weight);
    }
}