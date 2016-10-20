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
    "where o.pay_status = 2 and o.shipping_status = 0 and a.order_status = 1 and a.shipping_status = 0 and a.pay_status = 2 order by order_sn desc";
$orders_unship = $GLOBALS['db']->getAll($sql);

$sql = "select o.*,a.action_note from ecs_order_info as o left join ecs_order_action as a on a.order_id = o.order_id ".
    "where o.pay_status = 2 and o.shipping_status = 5 and a.order_status = 1 and a.shipping_status = 0 and a.pay_status = 2 order by order_sn desc";
$orders_sending = $GLOBALS['db']->getAll($sql);

$sql = "select o.*,a.action_note from ecs_order_info as o left join ecs_order_action as a on a.order_id = o.order_id ".
    "where o.pay_status = 2 and (o.shipping_status = 1 or o.shipping_status = 2) and a.order_status = 1 and a.shipping_status = 0 and a.pay_status = 2 order by order_sn desc";
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
    $sql = "select g.*,og.goods_price as sale_price,og.goods_attr,og.goods_attr_id,og.goods_purchase_price,og.goods_number as buy_number,og.chuanma".
        " from ecs_order_goods as og left join ecs_goods as g on og.goods_id = g.goods_id where og.order_id = " . $order['order_id'];
    $goods = $GLOBALS['db']->getAll($sql);
    $totalGoodsNumber = 0;
    foreach($goods as $key=>$good){
        $totalGoodsNumber += $good['buy_number'];
        $goods[$key]['shop_price'] = intval($goods[$key]['shop_price'] * RMB_RATE_ADMIN);
        $goods[$key]['sale_price'] = intval($goods[$key]['sale_price'] * RMB_RATE_ADMIN);
        $goods[$key]['goods_name'] = str_len($good['seller_note']) > 0 ? $good['seller_note'] : $good['goods_name'];
        $goods_attr_ids = explode(',', $good['goods_attr_id']);
        $goods[$key]['goods_add_price'] = 0;
        $attr_str = '';
        foreach($goods_attr_ids as $val){
            $sql = "select ga.attr_id, ga.attr_value, a.attr_name, ga.attr_price from ecs_goods_attr as ga left join ecs_attribute as a on ga.attr_id = a.attr_id where ga.goods_attr_id = ". $val;
            $attr = $GLOBALS['db']->getRow($sql);
            $attr['attr_name'] = $attr['attr_name'] == 'ROM' ? '容量' : ($attr['attr_name'] == 'Color' ? '颜色' : $attr['attr_name']);
            $color = $attr['attr_value'];
            switch($attr['attr_value']){
                case 'White':
                    $color = '白色';
                    break;
                case 'Black':
                    $color = '黑色';
                    break;
                case 'Gold':
                    $color = '金色';
                    break;
                case 'Grey':
                    $color = '灰色';
                    break;
                case 'Silver':
                    $color = '银色';
                    break;
                case 'Rose Gold':
                    $color = '玫瑰金';
                    break;
                case 'Blue':
                    $color = '蓝色';
                    break;
                case 'Green':
                    $color = '绿色';
                    break;
                case 'Pink':
                    $color = '粉红';
                    break;
                case 'Yellow':
                    $color = '黄色';
                    break;
                case 'Red':
                    $color = '红色';
                    break;
                case 'Orange':
                    $color = '橙色';
                    break;
                case 'Magenta':
                    $color = '品红';
                    break;
                case 'Purple':
                    $color = '紫色';
                    break;
            }
            $attr_str .= $attr['attr_name'] . ':' . $color . ' ';
            $goods[$key]['goods_add_price'] += intval($attr['attr_price'] * RMB_RATE_ADMIN);
        }
        $goods[$key]['goods_name'] .= ' [' . $attr_str . ']';
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