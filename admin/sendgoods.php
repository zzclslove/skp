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
    "where o.pay_status = 2 and a.action_note like 'PayPal交易号：%' order by shipping_status asc, order_sn desc";
$row = $GLOBALS['db']->getAll($sql);
foreach($row as $val){
    $item = array();
    $item['order'] = $val;
    $sql = "select g.*,og.goods_price as sale_price,og.goods_attr,og.goods_purchase_price from ecs_order_goods as og left join ecs_goods as g on og.goods_id = g.goods_id where og.order_id = " . $val['order_id'];
    $goods = $GLOBALS['db']->getAll($sql);
    foreach($goods as $key=>$good){
        $goods[$key]['shop_price'] = intval($goods[$key]['shop_price'] * 6.7258);
        $goods[$key]['sale_price'] = intval($goods[$key]['sale_price'] * 6.7258);
        $goods[$key]['goods_name'] = str_len($good['seller_note']) > 0 ? $good['seller_note'] : $good['goods_name'];
    }
    $item['goods'] = $goods;
    $data[] = $item;
}

$smarty->assign('data', $data);
$smarty->display('sendgoods.htm');