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

$sql = "select * from " . $GLOBALS['ecs']->table('order_goods') . " as g left join " . $GLOBALS['ecs']->table('order_info') . " as o on g.order_id = o.order_id where o.order_sn ='" . $order_sn . "'";
$goods = $db->getAll($sql);

$order['consignee'] = str_check($invoice['consignee'])?$invoice['consignee']:$order['consignee'];
$order['address'] = str_check($invoice['address'])?$invoice['address']:$order['address'].', '.$order['city'].', '.$order['states'];
$order['country'] = str_check($invoice['country'])?$invoice['country']:$order['country'];
$order['states'] = str_check($invoice['states'])?$invoice['states']:$order['states'];
$order['city'] = str_check($invoice['city'])?$invoice['city']:$order['city'];
$order['zipcode'] = str_check($invoice['postal_code'])?$invoice['postal_code']:$order['zipcode'];
$order['tel'] = str_check($invoice['phone_fax_email'])?$invoice['phone_fax_email']:$order['tel'];
$order['goods_num'] = str_check($invoice['items_number'])?$invoice['items_number']:$goods_num;
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
$invoice['bill_vat_number'] = '';
$invoice['bill_country'] = '';
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
        $invoice['bill_address'] = $temp_array[11].', '.$temp_array[12].', '.$temp_array[13].', '.$temp_array[14];
    }else{
        $invoice['bill_address'] = $temp_array[11].', '.$temp_array[13].', '.$temp_array[14];
    }
    $invoice['bill_phone_fax'] = $temp_array[15];
    $invoice['bill_vat_number'] = $temp_array[10];
    $invoice['bill_country'] = $temp_array[7];
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('openskyphone');
$pdf->SetMargins(PDF_MARGIN_LEFT, 12,PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$html = <<<EOF
<style>
    *{font-family: 'helvetica';}
	h1{font-size: 20pt;text-align:center}
	.right{text-align:right}
	.left{text-align:left}
	.address-table td{font-size:9pt;}
	.address-table td.content{font-family:dejavusans;font-size:10pt}
	.address-table{font-size:9pt}
	.product-table-header td{background-color:#cccccc;}
	.product-table td{border:1px solid #3c3c3c;font-size:9pt;}
	.conditions{font-size:8pt}
	.conditions-title{font-family:dejavusans;font-size:11pt}
	.signature{text-align:right;font-size:8pt;}
	.company_name{color:blue;}
	.ititle{font-size:9pt}
</style>
EOF;

$html .= '<table cellpadding="2" class="ititle">'.
    '<tr>'.
    '<td width="150" rowspan="5"><img width="150" src="themes/default/images/logo.png"></td>'.
    '<td width="350" align="right"><h2 class="company_name">Shenzhen GEZIXI Technology Limited</h2></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Tel: 86-13923413120</td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">3/F, Building A, Dalang Street Baifuli Industrial Zone Longhua District, Shenzhen</td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Contact: Claire, Email: openskyphone@hotmail.com</td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Website: www.openskyphone.com</td>'.
    '</tr>'.
    '</table>';
$html .= '<h1>COMMERCIAL INVOICE</h1>';
$html .= '<br />';
$html .= '<table class="address-table">'.
    '<tr>'.
    '<td align="left" width="310">Name:'.$invoice['bill_contact_person'].'</td>'.
    '<td align="right" width="180">Invoice NO. '.$order['order_sn'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Company: '.$invoice['bill_company_name'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">VAT Number: '.$invoice['bill_vat_number'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Address: '.$invoice['bill_address'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Country: '.$invoice['bill_country'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Telephone: '.$invoice['bill_phone_fax'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right"></td>'.
    '<td align="left"></td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '</table>';
$html .= '<h4>DELIVERY ADDRESS</h4>';
$html .= '<table class="address-table">'.
    '<tr>'.
        '<td align="left" width="310">Attn:'.$order['consignee'].'</td>'.
        '<td align="right" width="180"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Address: '.$order['address'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Postal Code: '.$order['zipcode'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">City: '.$order['city'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Country: '.$order['country'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="left">Telephone: '.$order['tel'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right"></td>'.
    '<td align="left"></td>'.
    '<td align="right"></td>'.
    '</tr>'.
'</table>';
$html .= '<table cellpadding="4" class="product-table">'.
    '<tr class="product-table-header">'.
    '<td width="30" align="center">No.</td>'.
    '<td width="60" align="center">P/N</td>'.
    '<td width="260" align="center">Description</td>'.
    '<td width="30" align="center">Qty</td>'.
    '<td width="60" align="center">Price</td>'.
    '<td width="60" align="center">Amount</td>'.
    '</tr>';
$i = 1;
foreach($goods as $good){
    $html .= '<tr class="product-table-content">'.
        '<td align="center">'.$i.'</td>'.
        '<td align="center">'.$good['goods_sn'].'</td>'.
        '<td align="center">'.$good['goods_name'].'</td>'.
        '<td align="right">'.$good['goods_number'].'</td>'.
        '<td align="right">$'.$good['goods_price'].'</td>'.
        '<td align="right">$'.$good['goods_price']*$good['goods_number'].'</td>'.
        '</tr>';
    $i++;
}
$html .= '<tr class="product-table-content" nobr="true">'.
    '<td align="right" colspan="5">Sum: </td>'.
    '<td align="right">$'.$order['goods_amount'].'</td>'.
    '</tr>'.
    '<tr class="product-table-content" nobr="true">'.
    '<td align="right" colspan="5">Freight: </td>'.
    '<td align="right">$'.$order['shipping_fee'].'</td>'.
    '</tr>'.
    '<tr class="product-table-content" nobr="true">'.
    '<td align="right" colspan="5">Total: </td>'.
    '<td align="right">$'.$order['order_amount'].'</td>'.
    '</tr>'.
'</table>';
$html .= '<br />';
$html .= '<div class="conditions">'.
    '<span class="conditions-title">Conditions:</span><br />'.
    '<span>Payment terms: Payment in advance.</span><br />'.
    '<span>Delivery terms: Ex-Work factory</span><br />'.
    '<span>Delivery time: Within 5 days after payment.</span>'.
    '</div>';
$html .= '<div class="conditions">'.
    '<span class="conditions-title">Bank info: (Please pay all relevant bank charges from your end, thanks.)</span><br />'.
    '<span>Bank Name: Bank of China，Hubei branch Chongyang County Tiancheng Subbranch </span><br />'.
    '<span>Address: N.21 Mingzhu Road, Tiancheng Town,Chongyang County, Hubei Province </span><br />'.
    '<span>Swift Code: BKCHCNBJ600 </span><br />'.
    '<span>Beneficiary Name: GANGPING ZHANG </span><br />'.
    '<span>Account No: 6013827609000075560 </span>'.
    '</div>';
$html .= '<div class="conditions">'.
    '<span class="conditions-title">MoneyGram info</span><br />'.
    '<span>First Name: YANGCHAO </span><br />'.
    '<span>Last Name: PENG </span><br />'.
    '<span>Country: CHINA </span><br />'.
    '<span>Address: 2/F, Tong Jian Building (No.) 3, Shen Nan Zhong Road, Futian District, Shenzhen </span>'.
    '</div>';

$html .= '<p class="signature">Authorized Signature: '.$order['signature'].' '.$order['invoice_date'].'</p>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('example_061.pdf', 'I');
















