<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$html = '<html><head></head><body><table class="outer" width="750" border="0" cellspacing="1" cellpadding="3" bgcolor="black" align="left">';
    $sql = "SELECT g.goods_id, g.goods_sn, g.seller_note, g.goods_name, g.promote_price, g.promote_start_date, g.promote_end_date, ".
    "IFNULL(mp.user_price, g.shop_price * '" . $_SESSION['discount'] . "') AS shop_price ".
    " FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
    " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
    "ON mp.goods_id = g.goods_id AND mp.user_rank = '" . $_SESSION['user_rank']. "' ".
    " WHERE g.is_delete = 0";
    $goods = $GLOBALS['db']->GetAll($sql);
    foreach($goods as $good){
    $pro = get_goods_properties($good['goods_id']);
    $zharray = zuhe($pro['spe']);
    foreach($zharray as $val){
        $tmpgood = array();
        $attr_id = array();
        $attr_str = '';
        $proarr = explode(',', $val);
        foreach($proarr as $pro){
            $proattr = explode('@', $pro);
            array_push($attr_id, $proattr[2]);
            $attr_str = $attr_str . $proattr[0] . ':' . $proattr[1] . ' ';
        }
        $shop_price  = get_final_price($good['goods_id'], 1, true, $attr_id);
        if($good['goods_name'] != 'Refurbished Original iPhone 6s Plus' && $good['goods_name'] != 'Refurbished Original iPhone 6s'){
            $html .= '<tr bgcolor="white">';
            $html .= '<td>'.$good['goods_name'].'</td>';
            $html .= '<td>'.$attr_str.'</td>';
            $html .= '<td>$'.$shop_price.'</td>';
            $html .= '</tr>';
        }
    }
}
$html .= '</table></body></html>';
echo $html;
exit(1);

function zuhe($arr){
    $tmparr = array();
    if(count($arr) == 1){
        $arr1 = array_shift($arr);
        foreach($arr1['values'] as $k1 => $v1){
            $tmparr[] = $arr1['name'].'@'.$v1['label'].'@'. $v1['id'];
        }
        return $tmparr;
    }else if(count($arr) == 2){
        $arr1 = array_shift($arr);
        $arr2 = array_shift($arr);
        foreach($arr1['values'] as $k1 => $v1){
            foreach($arr2['values'] as $k2 => $v2){
                $tmparr[] = $arr1['name'].'@'.$v1['label'].'@'. $v1['id'].','.$arr2['name'].'@'.$v2['label'].'@'. $v2['id'];
            }
        }
        return $tmparr;
    }else if(count($arr) == 3){
        $arr1 = array_shift($arr);
        $arr2 = array_shift($arr);
        $arr3 = array_shift($arr);
        foreach($arr1['values'] as $k1 => $v1){
            foreach($arr2['values'] as $k2 => $v2){
                foreach($arr3['values'] as $k3 => $v3){
                    $tmparr[] = $arr1['name'].'@'.$v1['label'].'@'. $v1['id'].','.$arr2['name'].'@'.$v2['label'].'@'. $v2['id'].','.$arr3['name'].'@'.$v3['label'].'@'. $v3['id'];
                }
            }
        }
        return $tmparr;
    }
}