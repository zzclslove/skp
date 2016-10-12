<?php
/**
 * Created by PhpStorm.
 * User: zzc
 * Date: 2016/10/2
 * Time: 14:54
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if (empty($_SESSION['user_id'])){
    die($_LANG['require_login']);
}

if (!isset($_REQUEST['order_sn']))
{
    ecs_header("Location: ./\n");
    exit;
}

$act = empty($_REQUEST['act'])?'':$_REQUEST['act'];
$order_sn = $_REQUEST['order_sn'];
if($act == 'set_invoice'){

    $smarty->assign('order_sn', $order_sn);

    $sql = "SELECT t1.*, t2.region_name as country_name FROM " . $ecs->table('order_info') . " as t1 left join ". $ecs->table('region') ." as t2 on t1.country = t2.region_id where t1.order_sn = '" . compile_str($order_sn) . "'";
    $row = $db->getRow($sql);
    $amount = $row['goods_amount'];

    $sql = "SELECT sum(o.goods_number) " .
        "FROM " . $ecs->table('order_goods') . " AS o ".
        "LEFT JOIN " . $ecs->table('order_info') . " AS g ON o.order_id = g.order_id " .
        "WHERE g.order_sn = '". compile_str($order_sn) . "'";
    $count = $db->getOne($sql);
    $amount = intval($amount / 10);
    $danjia = intval($amount/$count);
    $total = $danjia * $count;

    $smarty->assign('count', $count);
    $smarty->assign('danjia', $danjia);

    $sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
    $invoice = $db->getRow($sql);

    $sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . ' WHERE user_id = '.$_SESSION['user_id'];
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type = 3 ORDER BY dis_order, id';
    $dropshipping_filed_list = $db->getAll($sql);

    foreach ($dropshipping_filed_list AS $key => $val){
        $dropshipping_filed_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']];
    }

    $invoice['bill_company_name'] = '';
    $invoice['bill_address'] = '';
    $invoice['bill_contact_person'] = '';
    $invoice['bill_phone_fax'] = '';
    $datacheck = true;
    $temp_array = array();
    foreach ($dropshipping_filed_list AS $key => $val){
        if($val['id'] != 9 && $val['id'] != 12){
            if(str_len($val['content']) == 0){
                $datacheck = false;
            }
        }
        $temp_array[$val['id']] = $val['content'];
    }
    if($datacheck){
        $invoice['bill_company_name'] = $temp_array[8];
        $invoice['bill_contact_person'] = $temp_array[16];
        if(str_len($temp_array[12]) > 0){
            $invoice['bill_address'] = $temp_array[11].', '.$temp_array[12].', '.$temp_array[13].', '.$temp_array[14].', '.$temp_array[7];
        }else{
            $invoice['bill_address'] = $temp_array[11].', '.$temp_array[13].', '.$temp_array[14].', '.$temp_array[7];
        }

        $invoice['bill_phone_fax'] = $temp_array[15];
    }

    $invoice['consignee'] = isset($invoice['consignee']) && str_len($invoice['consignee']) > 0 ? $invoice['consignee'] : $row['consignee'];
    $invoice['address'] = isset($invoice['address']) && str_len($invoice['address']) > 0 ? $invoice['address'] : $row['address'] . ', ' . $row['city'] . ', ' . $row['states'];
    $invoice['city'] = isset($invoice['city']) && str_len($invoice['city']) > 0 ? $invoice['city'] : $row['city'];
    $invoice['state_country'] = isset($invoice['state_country']) && str_len($invoice['state_country']) > 0 ? $invoice['state_country'] : $row['country_name'];
    $invoice['phone_fax_email'] = isset($invoice['phone_fax_email']) && str_len($invoice['phone_fax_email']) > 0 ? $invoice['phone_fax_email'] : $row['tel'];
    $invoice['postal_code'] = isset($invoice['postal_code']) && str_len($invoice['postal_code']) > 0 ? $invoice['postal_code'] : $row['zipcode'];
    $invoice['invoice_date'] = isset($invoice['invoice_date']) && str_len($invoice['invoice_date']) > 0 ? $invoice['invoice_date'] : date('M.d, Y', time());
    $invoice['signature'] = isset($invoice['signature']) && str_len($invoice['signature']) > 0 ? $invoice['signature'] : 'Claire';

    $invoice['items_number'] = isset($invoice['items_number']) && str_len($invoice['items_number']) > 0 ? $invoice['items_number'] : $count;
    $invoice['unit_value'] = isset($invoice['unit_value']) && str_len($invoice['unit_value']) > 0 ? $invoice['unit_value'] : $danjia;

    $smarty->assign('invoice', $invoice);

    $smarty->display('set_invoice.dwt');
}else if($act == 'set_invoice_done'){

    $_SESSION['bill_company_name'] = compile_str($_REQUEST['bill_company_name']);
    $_SESSION['bill_address'] = compile_str($_REQUEST['bill_address']);
    $_SESSION['bill_contact_person'] = compile_str($_REQUEST['bill_contact_person']);
    $_SESSION['bill_phone_fax'] = compile_str($_REQUEST['bill_phone_fax']);

    $count = intval($_REQUEST['count']);
    $danjia = intval($_REQUEST['danjia']);

    $invoice = array(
    'order_sn'=> compile_str($order_sn),
    'bill_company_name' => compile_str($_REQUEST['bill_company_name']),
    'bill_address' => compile_str($_REQUEST['bill_address']),
    'bill_contact_person' => compile_str($_REQUEST['bill_contact_person']),
    'bill_phone_fax' => compile_str($_REQUEST['bill_phone_fax']),
    'consignee' => compile_str($_REQUEST['consignee']),
    'address' => compile_str($_REQUEST['address']),
    'phone_fax_email' => compile_str($_REQUEST['phone_fax_email']),
    'city' => compile_str($_REQUEST['city']),
    'postal_code'  => compile_str($_REQUEST['postal_code']),
    'state_country'  => compile_str($_REQUEST['state_country']),
    'total_weight'   => compile_str($_REQUEST['total_weight']),
    'signature'      => compile_str($_REQUEST['signature']),
    'invoice_date'   => compile_str($_REQUEST['invoice_date']),
    'items_number'   => $count,
    'unit_value'     =>  $danjia
    );
    $sql = "delete from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
    $db->query($sql);
    if($db->autoExecute($ecs->table('invoice'), $invoice, 'INSERT')){
        $smarty->assign('result', 'true');
    }else{
        $smarty->assign('result', 'false');
    }
    $smarty->display('set_invoice_done.dwt');
}


