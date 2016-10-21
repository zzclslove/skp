<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/20
 * Time: 11:12
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$sql = "select sum(og.goods_number) as sales_number, og.goods_sn, og.goods_attr_id from ecs_order_goods as og ".
    "left join ecs_order_info as o on o.order_id = og.order_id ".
    "where o.shipping_status = 1 or o.shipping_status = 2 group by og.goods_sn, og.goods_attr_id order by sum(og.goods_number) desc";
$sales_goods = $GLOBALS['db']->getAll($sql);

foreach($sales_goods as $key=>$value){
    $sql = "select seller_note,goods_name from ecs_goods where goods_sn = '" . $value['goods_sn'] . "'";
    $goods = $GLOBALS['db']->getRow($sql);
    $sales_goods[$key]['goods_name'] = str_len($goods['seller_note']) == 0 ? $goods['goods_name'] : $goods['seller_note'];
    $goods_attr_ids = explode(',', $value['goods_attr_id']);
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
        $sales_goods[$key]['goods_attr_name'] .= $attr['attr_name'] . ':' . $color . ' ';
    }
}
$smarty->assign('sales_goods', $sales_goods);
$smarty->display('sales_tongji.htm');