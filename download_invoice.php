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
$order['address'] = str_check($invoice['address'])?$invoice['address']:$order['address'];
$order['country'] = str_check($invoice['country'])?$invoice['country']:$order['country'];
$order['states'] = str_check($invoice['states'])?$invoice['states']:$order['states'];


$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('openskyphone');
$pdf->SetMargins(PDF_MARGIN_LEFT, 20,PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$html = <<<EOF
<style>
    *{font-family: helvetica;}
	h1{font-size: 18pt;text-align:center}
	.right{text-align:right}
	.left{text-align:left}
	td{padding:3px}
</style>
EOF;

$html .= '<h1>COMMERCIAL INVOICE</h1>';
$html .= '<p></p>';
$html .= '<table width="100%" class="address-table">'.
    '<tr>'.
        '<td align="right" width="5%">Attn: </td>'.
        '<td align="left"> '.$order['consignee'].'</td>'.
        '<td align="right" width="50%">Invoice NO. </td>'.
    '</tr>'.
'</table>';


$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('example_061.pdf', 'I');
















