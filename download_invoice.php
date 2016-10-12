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

$order['consignee'] = str_check($invoice['consignee'])?$invoice['consignee']:$order['consignee'];
$order['address'] = str_check($invoice['address'])?$invoice['address']:$order['address'].', '.$order['city'].', '.$order['states'];
$order['country'] = str_check($invoice['country'])?$invoice['country']:$order['country'];
$order['states'] = str_check($invoice['states'])?$invoice['states']:$order['states'];
$order['city'] = str_check($invoice['city'])?$invoice['city']:$order['city'];
$order['zipcode'] = str_check($invoice['postal_code'])?$invoice['postal_code']:$order['zipcode'];
$order['tel'] = str_check($invoice['phone_fax_email'])?$invoice['phone_fax_email']:$order['tel'];

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('openskyphone');
$pdf->SetMargins(PDF_MARGIN_LEFT, 20,PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$html = <<<EOF
<style>
    *{font-family: 'helvetica';}
	h1{font-size: 20pt;text-align:center}
	.right{text-align:right}
	.left{text-align:left}
	.address-table td{line-height:1.6}
	.address-table td.content{font-family:dejavusans;font-size:11pt}
	.address-table{font-size:9pt}
	.product-table-header td{background-color:#e5e5e5;}
	.product-table td{border:1px solid #3c3c3c;font-size:10pt}
</style>
EOF;

$html .= '<h1>COMMERCIAL INVOICE</h1>';
$html .= '<p></p>';
$html .= '<table width="100%" class="address-table">'.
    '<tr>'.
        '<td align="right" width="13%">Attn:</td>'.
        '<td class="content" align="left" width="47%"> '.$order['consignee'].'</td>'.
        '<td align="right" width="40%">Invoice NO. '.$order['order_sn'].'</td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Address:</td>'.
    '<td class="content" align="left"> '.$order['address'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Postal Code:</td>'.
    '<td class="content" align="left"> '.$order['zipcode'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">City:</td>'.
    '<td class="content" align="left"> '.$order['city'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Country:</td>'.
    '<td class="content" align="left"> '.$order['country'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right">Telephone:</td>'.
    '<td class="content" align="left"> '.$order['tel'].'</td>'.
    '<td align="right"></td>'.
    '</tr>'.
    '<tr>'.
    '<td align="right"></td>'.
    '<td align="left"></td>'.
    '<td align="right"></td>'.
    '</tr>'.
'</table>';
$html .= '<table width="100%" cellpadding="4" class="product-table">'.
    '<tr class="product-table-header">'.
    '<td align="center">Description</td>'.
    '<td align="center">HSCode</td>'.
    '<td align="center">Qty</td>'.
    '<td align="center">Price</td>'.
    '<td align="center">Amount</td>'.
    '</tr>'.
'</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('example_061.pdf', 'I');
















