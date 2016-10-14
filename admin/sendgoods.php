<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/14
 * Time: 12:52
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

$data = array();

$sql = "select o.*,a.action_note from ecs_order_info as o left join ecs_order_action as a on a.order_id = o.order_id ".
    "where o.pay_status = 2 and o.shipping_status = 0 and a.action_note like 'PayPal交易号：%' order by order_sn desc";
$orders_unship = $GLOBALS['db']->getAll($sql);

$sql = "select o.*,a.action_note from ecs_order_info as o left join ecs_order_action as a on a.order_id = o.order_id ".
    "where o.pay_status = 2 and o.shipping_status = 5 and a.action_note like 'PayPal交易号：%' order by order_sn desc";
$orders_sending = $GLOBALS['db']->getAll($sql);

$sql = "select o.*,a.action_note from ecs_order_info as o left join ecs_order_action as a on a.order_id = o.order_id ".
    "where o.pay_status = 2 and (o.shipping_status = 1 or o.shipping_status = 2) and a.action_note like 'PayPal交易号：%' order by order_sn desc";
$orders_shipped = $GLOBALS['db']->getAll($sql);

$unship = count($orders_unship);
$sending = count($orders_sending);
$shipped = count($orders_shipped);
$total = $unship + $sending + $shipped;

$orders = array();
foreach($orders_unship as $order){
    array_push($orders, $order);
}
foreach($orders_sending as $order){
    array_push($orders, $order);
}
foreach($orders_shipped as $order){
    array_push($orders, $order);
}

foreach($orders as $key=>$order){
    $item = array();
    $item['order'] = $order;
    $item['order']['money_paid'] = intval($order['money_paid'] * RMB_RATE_ADMIN);
    $item['order']['money_get'] = intval($order['money_paid'] * 0.938 * RMB_RATE_ADMIN);
    $sql = "select g.*,og.goods_price as sale_price,og.goods_attr,og.goods_purchase_price,og.goods_number as buy_number,og.chuanma".
        " from ecs_order_goods as og left join ecs_goods as g on og.goods_id = g.goods_id where og.order_id = " . $order['order_id'];
    $goods = $GLOBALS['db']->getAll($sql);
    $totalGoodsNumber = 0;
    foreach($goods as $key=>$good){
        $totalGoodsNumber += $good['buy_number'];
        $goods[$key]['shop_price'] = intval($goods[$key]['shop_price'] * RMB_RATE_ADMIN);
        $goods[$key]['sale_price'] = intval($goods[$key]['sale_price'] * RMB_RATE_ADMIN);
        $goods[$key]['goods_name'] = str_len($good['seller_note']) > 0 ? $good['seller_note'] : $good['goods_name'];
        $goods_attr = str_replace('Color', '颜色', $good['goods_attr']);
        $goods_attr = str_replace('ROM', '容量', $goods_attr);
        $goods_attr = str_replace('White', '白色', $goods_attr);
        $goods_attr = str_replace('Black', '黑色', $goods_attr);
        $goods_attr = str_replace('Grey', '灰色', $goods_attr);
        $goods_attr = str_replace('Silver', '银色', $goods_attr);
        $goods_attr = str_replace('Gold', '金色', $goods_attr);
        $goods_attr = str_replace('Rose Gold', '玫瑰金', $goods_attr);
        $goods_attr = str_replace('Blue', '蓝色', $goods_attr);
        $goods_attr = str_replace('Green', '绿色', $goods_attr);
        $goods_attr = str_replace('Pink', '粉红', $goods_attr);
        $goods_attr = str_replace('Yellow', '黄色', $goods_attr);
        $goods_attr = str_replace('Red', '红色', $goods_attr);
        $goods_attr = str_replace('Orange', '橙色', $goods_attr);
        $goods_attr = str_replace('Magenta', '品红', $goods_attr);
        $goods_attr = str_replace('Purple', '紫色', $goods_attr);
        $goods[$key]['goods_name'] = $goods[$key]['goods_name'].' [ '.$goods_attr.']';
    }
    $sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order['order_sn'] . "'";
    $invoice = $db->getRow($sql);

    if(!isset($invoice['order_sn'])){
        $sql = "insert into ecs_invoice(order_sn, items_number, unit_value) values('".$order['order_sn']."','".$totalGoodsNumber."','".$item['unit_price']."')";
        $db->query($sql);
        $item['total_goods_number'] = $totalGoodsNumber;
        $item['unit_price'] = intval($order['goods_amount']*0.15/$totalGoodsNumber);
    }else{
        $item['total_goods_number'] = str_check($invoice['items_number']) ? $invoice['items_number'] : $totalGoodsNumber;
        $item['unit_price'] = str_check($invoice['unit_value']) ? $invoice['unit_value'] : intval($order['goods_amount']*0.15/$totalGoodsNumber);
    }
    $item['goods'] = $goods;
    $data[] = $item;
}

$smarty->assign('totalGoodsNumber', $totalGoodsNumber);
$smarty->assign('unship', $unship);
$smarty->assign('sending', $sending);
$smarty->assign('shipped', $shipped);
$smarty->assign('total', $total);

$smarty->assign('data', $data);
$smarty->display('sendgoods.htm');


function str_check($str){
    if((!isset($str)) || empty($str) || str_len($str) == 0){
        return false;
    }else{
        return true;
    }
}