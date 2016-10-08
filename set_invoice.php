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

    $sql = "SELECT count(o.goods_id) " .
        "FROM " . $ecs->table('order_goods') . " AS o ".
        "LEFT JOIN " . $ecs->table('order_info') . " AS g ON o.order_id = g.order_id " .
        "WHERE g.order_sn = '". compile_str($order_sn) . "'";
    $res = $db->getOne($sql);

    $count = (intval($res) / 2) < 1?1:intval(intval($res) / 2);
    $amount = intval($amount / 10);
    $danjia = intval($amount/$count);
    $total = $danjia * $count;

    $smarty->assign('count', $count);
    $smarty->assign('danjia', $danjia);

    $sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
    $invoice = $db->getRow($sql);

    $invoice['bill_company_name'] = isset($invoice['bill_company_name']) && str_len($invoice['bill_company_name']) > 0 ? $invoice['bill_company_name']:(isset($_SESSION['bill_company_name'])?$_SESSION['bill_company_name']:'');
    $invoice['bill_address'] = isset($invoice['bill_address']) && str_len($invoice['bill_address']) > 0 ? $invoice['bill_address']:(isset($_SESSION['bill_address'])?$_SESSION['bill_address']:'');
    $invoice['bill_contact_person'] = isset($invoice['bill_contact_person']) && str_len($invoice['bill_contact_person']) > 0 ? $invoice['bill_contact_person']:(isset($_SESSION['bill_contact_person'])?$_SESSION['bill_contact_person']:'');
    $invoice['bill_phone_fax'] = isset($invoice['bill_phone_fax']) && str_len($invoice['bill_phone_fax']) > 0 ? $invoice['bill_phone_fax']:(isset($_SESSION['bill_phone_fax'])?$_SESSION['bill_phone_fax']:'');

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


