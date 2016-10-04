<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_order.php');
require(ROOT_PATH . 'includes/cls_json.php');
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
            $item->setName(str_replace("Refurbished Original","Used",$good['goods_name']))
                ->setCurrency('USD')
                ->setQuantity($good['goods_number'])
                ->setSku($good['goods_sn']) // Similar to `item_number` in Classic API
                ->setPrice($good['goods_price']);

            if(isset($_SESSION['currency']) || $_SESSION['currency'] != 0){
                if($_SESSION['currency'] == 2){
                    $item->setName(str_replace("Refurbished Original","Used",$good['goods_name']))
                        ->setCurrency('EUR')
                        ->setQuantity($good['goods_number'])
                        ->setSku($good['goods_sn']) // Similar to `item_number` in Classic API
                        ->setPrice(price_currency($good['goods_price']));
                }else{
                    $item->setName(str_replace("Refurbished Original","Used",$good['goods_name']))
                        ->setCurrency('USD')
                        ->setQuantity($good['goods_number'])
                        ->setSku($good['goods_sn']) // Similar to `item_number` in Classic API
                        ->setPrice($good['goods_price']);
                }
            }else{
                $item->setName(str_replace("Refurbished Original","Used",$good['goods_name']))
                    ->setCurrency('USD')
                    ->setQuantity($good['goods_number'])
                    ->setSku($good['goods_sn']) // Similar to `item_number` in Classic API
                    ->setPrice($good['goods_price']);
            }


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

        if(isset($_SESSION['currency']) || $_SESSION['currency'] != 0){
            if($_SESSION['currency'] == 2){
                $details->setShipping(price_currency($order['shipping_fee']))
                    ->setTax(0)
                    ->setSubtotal(price_currency($order['goods_amount']));
                $amount = new Amount();
                $amount->setCurrency("EUR")
                    ->setTotal(price_currency($order['shipping_fee']) + price_currency($order['goods_amount']))
                    ->setDetails($details);
            }else{
                $details->setShipping($order['shipping_fee'])
                    ->setTax(0)
                    ->setSubtotal($order['goods_amount']);
                $amount = new Amount();
                $amount->setCurrency("USD")
                    ->setTotal($order['order_amount'])
                    ->setDetails($details);
            }
        }else{
            $details->setShipping($order['shipping_fee'])
                ->setTax(0)
                ->setSubtotal($order['goods_amount']);
            $amount = new Amount();
            $amount->setCurrency("USD")
                ->setTotal($order['order_amount'])
                ->setDetails($details);
        }

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
            $payment->create($apiContext);
        } catch (Exception $ex) {
            $errorMsg = '';
            if ($ex instanceof \PayPal\Exception\PayPalConnectionException) {
                $msg = $ex->getData();
                $msgObj = json_decode($msg);
                foreach($msgObj->details as $key=>$val){
                    $errorMsg .= $val->field.':'.$val->issue;
                    if($key != count($msgObj->details) - 1){
                        $errorMsg .= ' or ';
                    }
                }
            }else{
                $errorMsg = $ex->getMessage();
            }

            assign_template();
            $position = assign_ur_here();
            $smarty->assign('page_title', $position['title']);   // 页面标题
            $smarty->assign('ur_here',    $position['ur_here']); // 当前位置
            $smarty->assign('page_title', $position['title']);   // 页面标题
            $smarty->assign('ur_here',    $position['ur_here']); // 当前位置
            $smarty->assign('helps',      get_shop_help());      // 网店帮助

            $smarty->assign('message',    $errorMsg);
            $smarty->assign('shop_url',   $ecs->url());

            $smarty->display('respond.dwt');
            exit(1);

        }
        $approvalUrl = $payment->getApprovalLink();
        header("Location:" . $approvalUrl);
    }
}

