<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
    $html = '<html><head></head><body><table>';
    $sql = "SELECT g.goods_id, g.seller_note, g.goods_name, g.shop_price ".
        " FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
        " WHERE g.is_delete = 0 order by cat_id, goods_name asc";
    $goods = $GLOBALS['db']->GetAll($sql);
    $index = 1;
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
                if($proattr[0] == 'ROM'){
                    $attr_str = $attr_str . '容量:' .$proattr[1] . ' ';
                }else if($proattr[0] == 'Color'){
                    $color = $proattr[1];
                    switch($proattr[1]){
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
                    $attr_str = $attr_str . '颜色:' .$color . ' ';
                }else{
                    $attr_str = $attr_str . $proattr[0] . ':' . $proattr[1] . ' ';
                }

            }

            $shop_price  = get_final_price($good['goods_id'], 1, true, $attr_id);
            $shop_price = intval($shop_price * 6.7216);
            $html .= '<tr>';
            $html .= '<td style="padding:3px;border:1px solid #3c3c3c"';
            if($index % 2 == 0){
                $html .= ' bgcolor="#eeeeee" ';
            }
            $html .= '>'.(strlen(trim($good['seller_note'])) == 0?$good['goods_name']:$good['seller_note']).'[ '.$attr_str.' ]</td>';
            $html .= '<td style="padding:3px;border:1px solid #3c3c3c"';
            if($index % 2 == 0){
                $html .= ' bgcolor="#eeeeee" ';
            }
            $html .= '>'.$shop_price.'</td>';
            $html .= '</tr>';
            $index ++;
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