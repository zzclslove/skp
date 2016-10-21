<?php
/**
 * Created by PhpStorm.
 * User: zzc
 * Date: 2016/10/3
 * Time: 19:53
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(dirname(__FILE__) . '/includes/tcpdf/tcpdf.php');

function str_check($str){
    if((!isset($str)) || empty($str) || str_len($str) == 0){
        return false;
    }else{
        return true;
    }
}

if (empty($_SESSION['user_id'])){
    die($_LANG['require_login']);
}
if (!isset($_REQUEST['order_sn']))
{
    ecs_header("Location: ./\n");
    exit;
}
$order_sn = $_REQUEST['order_sn'];

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
$pdf->SetFont('droidsansfallback', '', 10);
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
    '<td width="252" colspan="2" align="left"><span class="title">Invoice NO 单号: </span><span class="content">'.$order['order_sn'].'</span></td>'.
    '<td width="126" align="right" class="title">Air waybill Number 单号:</td>'.
    '<td width="126" align="center"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Company Name 公司名称:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_company_name'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Address 地址:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_address'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Contact Person 联系人:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_contact_person'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Phone/Fax 电话/传真:</td>'.
    '<td width="378" colspan="3" align="left">'.$invoice['bill_phone_fax'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="504" colspan="4" align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Consignee 收件人:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['consignee'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Address 地址:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['address'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">Phone/Fax 电话/传真:</td>'.
    '<td width="378" colspan="3" align="left">'.$order['tel'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">City 城市名:</td>'.
    '<td width="126" align="left">'.$order['city'].'</td>'.
    '<td width="126" align="right" class="title">Postal Code 邮编:</td>'.
    '<td width="126" align="left">'.$order['zipcode'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="right" class="title">State/Country 国家名:</td>'.
    '<td width="126" align="left">'.$order['country'].'</td>'.
    '<td width="126" align="right" class="title">Total Weight 重量:</td>'.
    '<td width="126" align="left"></td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="center" class="title">Description Of Goods<br />货 物 名 称</td>'.
    '<td width="126" align="center" class="title">No. of Items<br />数 量</td>'.
    '<td width="126" align="center" class="title">Unit Value<br />(USD)单价</td>'.
    '<td width="126" align="center" class="title">Total Value<br />(USD)总价</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="126" align="center">Mobile Phone</td>'.
    '<td width="126" align="center">'.$order['goods_num'].'</td>'.
    '<td width="126" align="center">'.$order['unit_price'].'</td>'.
    '<td width="126" align="center">'.$order['goods_amount'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td width="378" colspan="3" align="right" class="title">Total Invoice Value 总价:</td>'.
    '<td width="126" align="center">'.$order['goods_amount'].'</td>'.
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
And that the goods are origin china<br />本人认为以上提供的资料属实和正确，货物原产地是：中国</div>';
$html .= '<p class="signature">Authorized Signature: '.$order['signature'].' '.$order['invoice_date'].'</p>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('example_061.pdf', 'I');
















