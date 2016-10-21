<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/14
 * Time: 22:59
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/tcpdf/tcpdf.php');

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需
$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
$result = array('status'=>'','data'=>'');
if($act == 'update_remark')
{
    $order_remark = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : '';
    $sql = "update ecs_order_info set order_remark = '" . $order_remark . "' where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_orgnumber')
{
    $orgnumber = isset($_REQUEST['orgnumber']) ? $_REQUEST['orgnumber'] : '';
    $sql = "update ecs_order_info set order_orgnumber = '" . $orgnumber . "' where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_tracknumber')
{
    $trackingnumber = isset($_REQUEST['trackingnumber']) ? $_REQUEST['trackingnumber'] : '';
    $sql = "update ecs_order_info set invoice_no = '" . $trackingnumber . "' where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_chuanma')
{
    $chuanma = isset($_REQUEST['chuanma']) ? $_REQUEST['chuanma'] : '';
    $goods_id = isset($_REQUEST['goods_id']) ? $_REQUEST['goods_id'] : '';
    $sql = "update ecs_order_goods set chuanma = '" . $chuanma . "' where order_id = " . $order_id . " and goods_id = " . $goods_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_price')
{
    $price = intval(isset($_REQUEST['price']) ? $_REQUEST['price'] : '');
    $price = round($price / RMB_RATE_ADMIN ,2);
    $goods_id = isset($_REQUEST['goods_id']) ? $_REQUEST['goods_id'] : '';
    $sql = "update ecs_goods set shop_price = " . $price . " where goods_id = " . $goods_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_purchase_price')
{
    $price = intval(isset($_REQUEST['price']) ? $_REQUEST['price'] : '');
    $goods_id = isset($_REQUEST['goods_id']) ? $_REQUEST['goods_id'] : '';
    $sql = "update ecs_order_goods set goods_purchase_price = '" . $price . "' where order_id = " . $order_id . " and goods_id = " . $goods_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_shippingfee')
{
    $shippingFee = intval(isset($_REQUEST['shippingFee']) ? $_REQUEST['shippingFee'] : '');
    $sql = "update ecs_order_info set order_real_shipping_fee = '" . $shippingFee . "' where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'update_customer_fee')
{
    $customerFee = intval(isset($_REQUEST['customerFee']) ? $_REQUEST['customerFee'] : '');
    $sql = "update ecs_order_info set order_customer_fee = '" . $customerFee . "' where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'set_sending')
{
    $sql = "update ecs_order_info set order_status = 5, shipping_status = 5 where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}
else if($act == 'set_shipped')
{
    $sql = "update ecs_order_info set order_status = 5, shipping_status = 1 where order_id = " . $order_id;
    $GLOBALS['db']->query($sql);
    $result['status'] = true;
    $res= json_encode($result);
    echo $callback . '(' . $res .')';
}







































