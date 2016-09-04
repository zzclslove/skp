<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_order.php');
require 'bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ShippingAddress;

$order_id = $_REQUEST['orderid'];
$sql = 'select * from '.$GLOBALS['ecs']->table('order_info').' where order_id='.$order_id;
$res = $GLOBALS['db']->getAll($sql);
print_r($sql);
if (!empty($res))
{
    foreach ($res AS $row){
        $order = $row;
    }
}

if(!empty($order))
{
    $sql = 'select region_code from '.$GLOBALS['ecs']->table('region').' where region_id = '.$order['country'];
    $res = $GLOBALS['db']->getOne($sql);
    $order['country_code'] = $res;
    $items = array();
    $sql = 'select * from ' . $GLOBALS['ecs']->table('order_goods') . ' where order_id=' . $order_id;
    $res = $GLOBALS['db']->getAll($sql);
    if (!empty($res)) {
        foreach ($res AS $row) {
            $goods[] = $row;
        }
    }

    if(!empty($goods))
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        $itemList = new ItemList();
        $items = array();
        foreach ($goods AS $good) {
            $item = new Item();
            $item->setName($good['goods_name'])
                ->setCurrency('USD')
                ->setQuantity($good['goods_number'])
                ->setSku($good['goods_sn']) // Similar to `item_number` in Classic API
                ->setPrice($good['goods_price']);
            $items[] = $item;
        }
        $itemList->setItems($items);
        $address = new ShippingAddress();
        $address->setRecipientName($order['consignee'])
            ->setLine1($order['address'])
            ->setCity($order['city'])
            ->setState($order['states'])
            ->setPhone($order['tel'])
            ->setPostalCode($order['zipcode'])
            ->setCountryCode($order['country_code']);

        $itemList->setShippingAddress($address);
        $details = new Details();
        $details->setShipping($order['shipping_fee'])
            ->setTax(0)
            ->setSubtotal($order['goods_amount']);
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($order['order_amount'])
            ->setDetails($details);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($order['how_oos'])
            ->setInvoiceNumber($_REQUEST['invoice']);
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($_REQUEST['return'])
            ->setCancelUrl($_REQUEST['cancel_return']);

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        $request = clone $payment;

        try {
            $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment') .
                " WHERE pay_id = '".$order['pay_id']."' AND enabled = 1";

            $payment_info = $GLOBALS['db']->getRow($sql);
            $payment_config = unserialize_config($payment_info['pay_config']);
            if($payment_config['sandbox']){
                $apiContext->setConfig(
                    array(
                        'mode' => 'sandbox',
                        'cache.enabled' => true,
                    )
                );
            }else{
                $apiContext->setConfig(
                    array(
                        'mode' => 'live',
                        'cache.enabled' => true,
                    )
                );
            }
            $payment->create($apiContext);
        } catch (Exception $ex) {
            print_r($ex->getData());
            exit(1);
        }
        $approvalUrl = $payment->getApprovalLink();
        header("Location:" . $approvalUrl);
    }
}

