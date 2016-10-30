<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/24
 * Time: 14:31
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH. 'includes/tcpdf/tcpdf.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

$order_ids = isset($_REQUEST['orderid'])?$_REQUEST['orderid']:'';
if($order_ids.length == 0){
    die('请选择订单。');
}

$sql = "update " . $GLOBALS['ecs']->table('order_info') . " set has_print_sender = 1 where order_id in (" . $order_ids . ")";
$GLOBALS['db']->query($sql);

$sql = "SELECT o.order_id, o.order_sn, r.region_name " .
    " FROM " . $GLOBALS['ecs']->table('order_info') . " as o" .
    " left join " . $GLOBALS['ecs']->table('region') . " as r on r.region_id = o.country ".
    " where o.order_id in (".$order_ids.") order by o.add_time asc";
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

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('openskyphone');
$pdf->SetFont('droidsansfallback', '', 9);
$pdf->SetMargins(5, 5, 70);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->setCellHeightRatio(2);

$html = <<<EOF
<style>
table{border:1px solid #3c3c3c;}
th{background-color:#dedede;border-bottom:1px solid #3c3c3c;}
.title{border-bottom:1px solid #999999;border-right:1px solid #999999;}
.content{border-bottom:1px solid #999999;}
.bright{border-right:1px solid #999999;}
.order_number{font-size:12pt;}
div{height:20px}
</style>
EOF;
foreach($order_goods as $order_good){
    $html .= '<table width="382" cellpadding="1" cellspacing="1" class="table"><tr><th colspan="2"><b class="order_number">' . $order_good['order_number'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $order_good['region_name'] . '</b></th></tr>';
    $index = 0;
    $count = count($order_good['goods']);
    foreach ($order_good['goods'] as $goods){
        $index++;
        $gn = str_len($goods['seller_note']) == 0?$goods['goods_name']:$goods['seller_note'];
        $gn .= ' '.$goods['attr'];
        if($index == $count){
            $html .= '<tr><td width="330" class="bright"><p>'.$gn.'</p></td><td width="49"><p>数量：'.$goods['num'].'个</p></td></tr>';
        }else{
            $html .= '<tr><td width="330" class="title"><p>'.$gn.'</p></td><td width="49" class="content"><p>数量：'.$goods['num'].'个</p></td></tr>';
        }
    }
    $html .= '</table><div></div>';
}
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($order['order_sn'] . '.pdf', 'I');
