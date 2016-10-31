<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24
 * Time: 14:31
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/cls_matrix.php');
include_once(ROOT_PATH . 'includes/cls_certificate.php');

$sql = "SELECT o.order_id, o.order_sn, r.region_name " .
    " FROM " . $GLOBALS['ecs']->table('order_info') . " as o" .
    " left join " . $GLOBALS['ecs']->table('region') . " as r on r.region_id = o.country ".
    " where o.pay_status = 2 and (o.shipping_status = 0 or o.shipping_status = 4 or o.shipping_status = 3) order by o.add_time asc";
$row = $GLOBALS['db']->getAll($sql);
$order_goods = array();
foreach($row as $val){
    $order_good = array();
    $order_good['order_number'] = $val['order_sn'];
    $order_good['region_name'] = $val['region_name'];
    $order_good['goods'] = array();
    $sql = "select g.goods_name, g.seller_note, o.goods_attr_id, o.goods_number as num from " . $GLOBALS['ecs']->table('order_goods') . "as o".
        " left join " . $GLOBALS['ecs']->table('goods') . " as g on o.goods_id = g.goods_id ".
        "where o.order_id = " . $val['order_id'];
    $products = $GLOBALS['db']->getAll($sql);
    foreach($products as $product){
        $sql = " select a.attr_name, ga.attr_value from " . $GLOBALS['ecs']->table('goods_attr') . " as ga ".
            "left join " . $GLOBALS['ecs']->table('attribute') . " as a on ga.attr_id = a.attr_id" .
            " where ga.goods_attr_id in (" .$product['goods_attr_id'] . ")";
        $goods_attrs = $GLOBALS['db']->getAll($sql);
        $product['attr'] = "";
        foreach($goods_attrs as $goods_attr){
            if($goods_attr['attr_name'] == 'ROM'){
                $product['attr'] .= "容量:" . $goods_attr['attr_value'] . " ";
            }else if($goods_attr['attr_name'] == 'Color'){
                $color = '';
                switch($goods_attr['attr_value']){
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
                $product['attr'] .= "颜色:" . $color . " ";
            }else{
                if($goods_attr['attr_value'] == 'No-Fingerprint'){
                    $product['attr'] .= "其他:无指纹";
                }else if($goods_attr['attr_value'] == 'Has-Fingerprint'){
                    $product['attr'] .= "其他:有指纹";
                }else{
                    $product['attr'] .= "其他:" . $goods_attr['attr_value'] . " ";
                }
            }
        }
        $order_good['goods'][] = $product;
    }
    $order_goods[] = $order_good;
}
$smarty->assign('order_goods', $order_goods);
$smarty->display('unshipgoods.htm');