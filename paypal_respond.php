<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/4
 * Time: 0:05
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
require(ROOT_PATH . 'includes/cls_json.php');

require 'bootstrap.php';
use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// ### Approval Status
// Determine if the user approved the payment or not
if (isset($_GET['success']) && $_GET['success'] == 'true') {

    // Get the payment Object by passing paymentId
    // payment id was previously stored in session in
    // CreatePaymentUsingPayPal.php
    $paymentId = $_GET['paymentId'];
    $payment = Payment::get($paymentId, $apiContext);
    // ### Payment Execute
    // PaymentExecution object includes information necessary
    // to execute a PayPal account payment.
    // The payer_id is added to the request query parameters
    // when the user is redirected from paypal back to your site

    $execution = new PaymentExecution();
    $execution->setPayerId($_GET['PayerID']);

    try {
        // Execute the payment
        // (See bootstrap.php for more on `ApiContext`)
        $msg = '';
        $result = $payment->execute($execution, $apiContext);

        if($result->getState() == 'approved'){
            $transactions = $result->getTransactions();
            foreach($transactions as $transaction){
                $order_sn = $transaction->getInvoiceNumber();
                $amount = $transaction->getAmount();
                $msg = $_LANG['pay_success'];
                $relate_resources = $transaction->getRelatedResources();
                foreach($relate_resources as $relate_resource){
                    $sale = $relate_resource->getSale();
                    $sale_id = $sale->getId();
                }
                $sql = "SELECT l.log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " as l left join ". $GLOBALS['ecs']->table('order_info') ." as o on l.order_id = o.order_id WHERE o.order_sn = '$order_sn'";
                $log_id = $GLOBALS['db']->getOne($sql);
            }
        }else{
            $msg = $_LANG['pay_fail'];
        }
        if(isset($log_id) && isset($sale_id)){
            $action_note = 'PayPal交易号：' . $sale_id;
            order_paid($log_id, PS_PAYED, $action_note);
            $msg = $_LANG['pay_success'];
        }else{
            $msg = $_LANG['pay_fail'];
        }

    } catch (Exception $ex) {
        $json = new JSON();
        $error_msg = $json->decode($ex->getData());
        $msg = $error_msg->message;
    }
} else {
    $msg = "User Cancelled the Approval";
}

assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('helps',      get_shop_help());      // 网店帮助

$smarty->assign('message',    $msg);
$smarty->assign('shop_url',   $ecs->url());

$smarty->display('respond.dwt');