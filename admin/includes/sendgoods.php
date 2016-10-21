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
        " from ecs_order_goods as o