<?php
/**
 * Created by PhpStorm.
 * User: zzc
 * Date: 2016/10/3
 * Time: 19:53
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH. 'includes/tcpdf/tcpdf.php');

function str_check($str){
    if((!isset($str)) || empty($str) || str_len($str) == 0){
        return false;
    }else{
        return true;
    }
}

$order_sn = $_REQUEST['order_sn'];
$invoice_number = $_REQUEST['invoice_number'];
$invoice_price = $_REQUEST['invoice_price'];

$sql = "update ecs_invoice set items_number = " . $invoice_number . ", unit_value = " . $invoice_price . " where order_sn = '" . $order_sn . "'";
$db->query($sql);
$sql = "update ecs_order_info set has_print_invoice = 1 where order_sn ='" . compile_str($order_sn) . "'";
$db->query($sql);

$smarty->assign('order_sn', $order_sn);

$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " where order_sn = '" . compile_str($order_sn) . "'";
$order = $db->getRow($sql);

$sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
$invoice = $db->getRow($sql);

$sql = "select sum(goods_number) from " . $GLOBALS['ecs']->table('order_goods') . " as g left join " . $GLOBALS['ecs']->table('order_info') . " as o on g.order_id = o.order_id where o.order_sn ='" . $order_sn . "'";
$goods_num = $db->getOne($sql);

$order['consignee'] = str_check($invoice['consignee'])?$invoice['consignee']:$order['consignee'];
$order['address'] = str_check($invoice['address'])?$invoice['address']:$order['address'].', '.$order['city'].', '.$order['states'];
$sql = "select region_name from ".$GLOBALS['ecs']->table('region')." where region_id=" . $order['country'];
$country = $db->getOne($sql);
$order['country'] = str_check($invoice['country'])?$invoice['country']:$country;
$order['states'] = str_check($invoice['states'])?$invoice['states']:$order['states'];
$order['city'] = str_check($invoice['city'])?$invoice['city']:$order['city'];
$order['zipcode'] = str_check($invoice['postal_code'])?$invoice['postal_code']:$order['zipcode'];
$order['tel'] = str_check($invoice['phone_fax_email'])?$invoice['phone_fax_email']:$order['tel'];
$order['goods_num'] = str_check($invoice['items_number'])?$invoice['items_number']:$goods_num;
$order['unit_price'] = str_check($invoice['unit_value'])?$invoice['unit_value']:intval($order['goods_amount']*0.15/$order['goods_num']);
$order['goods_amount'] = intval($order['goods_num']) * intval($order['unit_price']);
$order['signature'] = str_check($invoice['signature'])?$invoice['signature']:'Claire';
$order['invoice_date'] = str_check($invoice['invoice_date'])?$invoice['invoice_date']:date('M.d, Y', time());

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

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('openskyphone');
//$pdf->SetFont('droidsansfallback', '', 10);
$pdf->SetFontSize(10);
$pdf->SetMargins(PDF_MARGIN_LEFT, 20,PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$html = <<<EOF
<style>
    h1{text-align:center;font-size:22pt}
    .table td{border:0.5px solid #222222}
    .title{color:#3c3c3c}
    .conditions{font-size:8pt}
    .conditions-title{font-size:10pt}
    .signature{text-align:right;font-size:10pt}
</style>
EOF;

$html .= '<h1>COMMERCIAL INVOICE</h1>';
$html .= '<p></p>';
$html .= '<table cellpadding="3" class="table">'.
    '<tr>'.
    '<td width="252" colspan="2" align="left"><span class="title">Invoice NO: </span><span class="content">'.$order['order_sn'].'</span></td>'.
    '<td width="126" align="right" class="title">Air waybill Number:</td>'.
    '<td width="126" align="center"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Company Name:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_company_name'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Address:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_address'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Contact Person:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_contact_person'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Phone/Fax:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_phone_fax'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="504" colspan="4" align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Consignee:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['consignee'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Address:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['address'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Phone/Fax:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['tel'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">City:</td>'.
    '<td width="126" align="left">'.$order['city'].'</td>'.
    '<td width="126" align="right" class="title">Postal Code:</td>'.
    '<td width="126" align="left">'.$order['zipcode'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">State/Country:</td>'.
    '<td width="126" align="left">'.$order['country'].'</td>'.
    '<td width="126" align="right" class="title">Total Weight:</td>'.
    '<td width="126" align="left"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="104" align="center" class="title">Description</td>'.
    '<td width="100" align="center" class="title">HS Code</td>'.
    '<td width="100" align="center" class="title">No. of Items</td>'.
    '<td width="100" align="center" class="title">Unit Value(USD)</td>'.
    '<td width="100" align="center" class="title">Total Value(USD)</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="104" align="center">Handset</td>'.
    '<td width="100" align="center">8529909090</td>'.
    '<td width="100" align="center">'.$order['goods_num'].'</td>'.
    '<td width="100" align="center">'.$order['unit_price'].'</td>'.
    '<td width="100" align="center">'.$order['goods_amount'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="404" colspan="3" align="right" class="title">Total Invoice Value(USD):</td>'.
    '<td width="100" align="center">'.$order['goods_amount'].'</td>'.
    '</tr>'.
    '</table>';
$html .= '<p></p>';
$html .= '<div class="conditions">'.
    '<span class="conditions-title">Conditions:</span><br />'.
    '<span>Payment terms: Payment in advance.</span><br />'.
    '<span>Delivery terms: Ex-Work factory</span><br />'.
    '<span>Delivery time: Within 5 days after payment.</span>'.
    '</div>';
$html .= '<div class="conditions">I declare that the above information is true and correct to the best of my knowledge,
And that the goods are origin china</div>';
$html .= '<p class="signature">Authorized Signature: '.$order['signature'].' '.$order['invoice_date'].'</p>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($order['order_sn'] . '.pdf', 'I');
















