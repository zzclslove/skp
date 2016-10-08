<?php
/**
 * Created by PhpStorm.
 * User: zzc
 * Date: 2016/10/3
 * Time: 19:53
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
$order_sn = $_REQUEST['order_sn'];

$smarty->assign('order_sn', $order_sn);

$sql = "SELECT goods_amount FROM " . $GLOBALS['ecs']->table('order_info') . " where order_sn = '" . compile_str($order_sn) . "'";
$amount = $db->getOne($sql);

$sql = "SELECT count(o.goods_id) " .
    "FROM " . $ecs->table('order_goods') . " AS o ".
    "LEFT JOIN " . $ecs->table('order_info') . " AS g ON o.order_id = g.order_id " .
    "WHERE g.order_sn = '". compile_str($order_sn) . "'";
$res = $db->getOne($sql);

$count = (intval($res) / 2) < 1?1:intval(intval($res) / 2);
$amount = intval($amount / 10);
$danjia = intval($amount/$count);
$total = $danjia * $count;

$sql = "select * from " . $ecs->table('invoice') . " where order_sn = '" . $order_sn . "'";
$invoice = $db->getRow($sql);

$html = '
<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 11">
<meta name=Originator content="Microsoft Word 11">
<title>Commercial Invoice</title>
<o:SmartTagType namespaceuri="urn:schemas-microsoft-com:office:smarttags"
 name="place"/>
<o:SmartTagType namespaceuri="urn:schemas-microsoft-com:office:smarttags"
 name="country-region"/>
<o:SmartTagType namespaceuri="urn:schemas-microsoft-com:office:smarttags"
 name="City"/>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>HT</o:Author>
  <o:Template>Normal</o:Template>
  <o:LastAuthor>microsoft</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>4</o:TotalTime>
  <o:LastPrinted>2010-11-25T05:47:00Z</o:LastPrinted>
  <o:Created>2016-09-21T10:25:00Z</o:Created>
  <o:LastSaved>2016-09-21T10:25:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>111</o:Words>
  <o:Characters>637</o:Characters>
  <o:Company>HT</o:Company>
  <o:Lines>5</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>747</o:CharactersWithSpaces>
  <o:Version>11.9999</o:Version>
 </o:DocumentProperties>
 <o:CustomDocumentProperties>
  <o:KSOProductBuildVer dt:dt="string">2052-10.1.0.5866</o:KSOProductBuildVer>
 </o:CustomDocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:DrawingGridVerticalSpacing>7.8 磅</w:DrawingGridVerticalSpacing>
  <w:ValidateAgainstSchemas>false</w:ValidateAgainstSchemas>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:DoNotUnderlineInvalidXML/>
  <w:Compatibility>
   <w:SpaceForUL/>
   <w:BalanceSingleByteDoubleByteWidth/>
   <w:DoNotLeaveBackslashAlone/>
   <w:DoNotExpandShiftReturn/>
   <w:AdjustLineHeightInTable/>
   <w:SnapToGridInCell/>
   <w:DontGrowAutofit/>
   <w:UseFELayout/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
 </w:LatentStyles>
</xml><![endif]--><!--[if !mso]><object
 classid="clsid:38481807-CA0E-42D2-BF39-B33AF135CC4D" id=ieooui></object>
<style>
st1\:*{behavior:url(#ieooui) }
</style>
<![endif]-->
<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:"New York";
	panose-1:2 4 5 3 6 5 6 2 3 4;
	mso-font-alt:"Times New Roman";
	mso-font-charset:0;
	mso-generic-font-family:roman;
	mso-font-format:other;
	mso-font-pitch:variable;
	mso-font-signature:3 0 0 0 1 0;}
@font-face
	{font-family:宋体;
	panose-1:2 1 6 0 3 1 1 1 1 1;
	mso-font-alt:SimSun;
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:variable;
	mso-font-signature:3 680460288 22 0 262145 0;}
@font-face
	{font-family:TimesNewRomanPSMT-Identity-H;
	mso-font-alt:黑体;
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:auto;
	mso-font-signature:1 135135232 16 0 262144 0;}
@font-face
	{font-family:"\@宋体";
	panose-1:2 1 6 0 3 1 1 1 1 1;
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:variable;
	mso-font-signature:3 680460288 22 0 262145 0;}
@font-face
	{font-family:"\@TimesNewRomanPSMT-Identity-H";
	mso-font-charset:134;
	mso-generic-font-family:auto;
	mso-font-pitch:auto;
	mso-font-signature:1 135135232 16 0 262144 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	text-align:justify;
	text-justify:inter-ideograph;
	mso-pagination:none;
	font-size:10.5pt;
	mso-bidi-font-size:10.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:宋体;
	mso-font-kerning:1.0pt;}
h3
	{mso-style-next:正文;
	margin:0cm;
	margin-bottom:.0001pt;
	text-align:justify;
	text-justify:inter-ideograph;
	line-height:12.0pt;
	mso-line-height-rule:exactly;
	mso-pagination:none;
	page-break-after:avoid;
	mso-outline-level:3;
	font-size:10.5pt;
	mso-bidi-font-size:10.0pt;
	font-family:Arial;
	mso-bidi-font-family:"Times New Roman";
	mso-font-kerning:1.0pt;
	mso-bidi-font-weight:normal;}
p.MsoHeader, li.MsoHeader, div.MsoHeader
	{margin:0cm;
	margin-bottom:.0001pt;
	text-align:center;
	mso-pagination:none;
	tab-stops:center 207.65pt right 415.3pt;
	layout-grid-mode:char;
	border:none;
	mso-border-bottom-alt:solid windowtext .75pt;
	padding:0cm;
	mso-padding-alt:0cm 0cm 1.0pt 0cm;
	font-size:9.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:宋体;
	mso-font-kerning:1.0pt;}
p.MsoFooter, li.MsoFooter, div.MsoFooter
	{margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:none;
	tab-stops:center 207.65pt right 415.3pt;
	layout-grid-mode:char;
	font-size:9.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:宋体;
	mso-font-kerning:1.0pt;}
p.MsoTitle, li.MsoTitle, div.MsoTitle
	{margin:0cm;
	margin-bottom:.0001pt;
	text-align:center;
	mso-pagination:none;
	font-size:16.0pt;
	mso-bidi-font-size:10.0pt;
	font-family:Arial;
	mso-fareast-font-family:宋体;
	mso-bidi-font-family:"Times New Roman";
	mso-font-kerning:1.0pt;}
span.shorttext1
	{mso-style-name:short_text1;
	mso-style-parent:"";
	mso-ansi-font-size:12.0pt;
	mso-bidi-font-size:12.0pt;}
span.hps
	{mso-style-name:hps;}
span.apple-style-span
	{mso-style-name:apple-style-span;}
span.shorttext
	{mso-style-name:short_text;}
p.tgt2, li.tgt2, div.tgt2
	{mso-style-name:tgt2;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:6.45pt;
	margin-left:0cm;
	line-height:150%;
	mso-pagination:widow-orphan;
	font-size:18.0pt;
	font-family:宋体;
	mso-bidi-font-family:宋体;
	font-weight:bold;}
 /* Page Definitions */

@page Section1
	{size:595.3pt 841.9pt;
	margin:1.0cm 2.0cm 1.0cm 51.05pt;
	mso-header-margin:1.0cm;
	mso-footer-margin:17.0pt;
	mso-paper-source:0;
	layout-grid:15.6pt;}
div.Section1
	{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:普通表格;
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]--><!--[if gte mso 9]><xml>
 <o:shapedefaults v:ext="edit" spidmax="2050" strokecolor="#739cc3">
  <v:fill angle="90" type="gradient">
   <o:fill v:ext="view" type="gradientUnscaled"/>
  </v:fill>
  <v:stroke color="#739cc3" weight="1.25pt"/>
 </o:shapedefaults></xml><![endif]--><!--[if gte mso 9]><xml>
 <o:shapelayout v:ext="edit">
  <o:idmap v:ext="edit" data="1"/>
 </o:shapelayout></xml><![endif]-->
</head>

<body bgcolor=white lang=ZH-CN style=\'tab-interval:21.0pt;text-justify-trim:
punctuation\'>

<div class=Section1 style=\'layout-grid:15.6pt\'>

<p class=MsoNormal align=center style=\'text-align:center\'><span
style=\'font-size:20.0pt;color:#002060\'><span
style=\'mso-spacerun:yes\'>&nbsp;</span><span style=\'mso-bidi-font-weight:bold\'><span
style=\'mso-spacerun:yes\'>&nbsp;</span></span></span><b><span style=\'font-size:
20.0pt;font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:
"Times New Roman";color:#002060\'>商业发票</span></b><b><span lang=EN-US
style=\'font-size:24.0pt;color:#002060\'><o:p></o:p></span></b></p>

<p class=MsoTitle style=\'line-height:18.0pt;mso-line-height-rule:exactly\'><span
lang=EN-US style=\'color:#002060\'><o:p>&nbsp;</o:p></span></p>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
 style=\'border-collapse:collapse;border:none;mso-border-alt:solid windowtext .5pt;
 mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-border-insideh:.5pt solid windowtext;
 mso-border-insidev:.5pt solid windowtext\'>
 <tr style=\'mso-yfti-irow:0;mso-yfti-firstrow:yes\'>
  <td width=331 colspan=3 valign=top style=\'width:248.4pt;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt\'>
  <p class=MsoNormal style=\'margin-right:-5.35pt;mso-para-margin-right:-.51gd;
  line-height:125%;line-height:16.05pt;\'><span style=\'mso-bidi-font-size:10.5pt;line-height:125%;
  font-family:宋体;mso-ascii-font-family:Arial;mso-hansi-font-family:Arial\'>订单号:</span><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:
  Arial;mso-bidi-font-family:"Times New Roman"\'>'.$invoice['order_sn'].'<o:p></o:p></span></p>
  </td>
  <td width=168 colspan=4 valign=top style=\'width:126.0pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
  solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Air waybill Number</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>单号</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=120 valign=top style=\'width:90.0pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
  solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>&nbsp;</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:1;height:23.7pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:23.7pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Company Name</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>公司名称</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.7pt\'>
  <p class=MsoNormal style=\'line-height:16.5pt;mso-line-height-rule:exactly\'><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;font-family:Arial;mso-bidi-font-family:
  "Times New Roman"\'><o:p>'. $invoice['bill_company_name'] .'</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:2;height:22.7pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Address</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>地址</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.7pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>'. $invoice['bill_address'] .'</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:3;height:22.9pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:22.9pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Contact Person</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>联系人</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.9pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>'.$invoice['bill_contact_person'].'</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:4;height:23.2pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:23.2pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Phone/Fax:</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>电话</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>/</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>传真:</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.2pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>'.$invoice['bill_phone_fax'].'</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:5;height:5.65pt\'>
  <td width=619 colspan=8 valign=top style=\'width:464.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5.65pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>&nbsp;</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:6;height:25.95pt\'>
  <td width=619 colspan=8 valign=top style=\'width:464.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:25.95pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Consignee</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>收件人</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%\'> </span><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;line-height:125%\'><span
  style=\'mso-spacerun:yes\'>&nbsp;&nbsp; </span>'.$invoice['consignee'].'<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:8;height:46.15pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:46.15pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Address</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>地址</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  <p class=MsoNormal style=\'line-height:125%\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:46.15pt\'>
  <p class=MsoNormal align=left style=\'text-align:left;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;font-family:"New York";mso-bidi-font-family:
  Arial;mso-font-kerning:0pt\'>'.$invoice['address'].'</span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:10;page-break-inside:avoid;height:22.4pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:22.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Phone/Fax/E-mail</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>电话</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>/</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>传真</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>/</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>邮箱</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=432 colspan=7 valign=top style=\'width:324.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.4pt\'>
  <p class=MsoNormal align=left style=\'text-align:left;line-height:16.05pt;mso-pagination:widow-orphan\'><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;font-family:Arial;mso-font-kerning:
  0pt\'>'.$invoice['phone_fax_email'].'<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:11;height:23.4pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:23.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>City</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>城市名</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=144 colspan=2 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-font-kerning:0pt\'>'.$invoice['city'].'<o:p>&nbsp;</o:p></span></p>
  </td>
  <td width=144 colspan=2 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.4pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Postal Code</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>邮编</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=144 colspan=3 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.4pt\'>
  <p class=MsoNormal style="line-height:16.05pt;"><span lang=EN-US style=\'mso-bidi-font-size:10.5pt;
  font-family:Arial;mso-font-kerning:0pt\'>'.$invoice['postal_code'].'<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:12;height:23.0pt\'>
  <td width=187 valign=top style=\'width:140.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:23.0pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>State/Country</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>国家名</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=144 colspan=2 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.0pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><st1:country-region w:st="on"><st1:place
   w:st="on"><span lang=EN-US style=\'mso-bidi-font-size:10.5pt;line-height:
    125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>'.$invoice['state_country'].'</span></st1:place></st1:country-region><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:
  Arial;mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=144 colspan=2 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.0pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>Total Weight</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>重量</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=144 colspan=3 valign=top style=\'width:108.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:23.0pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:16.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p>'.$invoice['total_weight'].'</o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:13;page-break-inside:avoid;height:31.25pt\'>
  <td width=283 colspan=2 valign=top style=\'width:212.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:31.25pt\'>
  <p class=MsoNormal align=center style=\'text-align:center;line-height:125%;line-height:16.05pt;\'><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:
  Arial;mso-bidi-font-family:"Times New Roman"\'>Full Description Of <span
  style=\'mso-spacerun:yes\'>&nbsp;</span>Goods<o:p></o:p></span></p>
  <p class=MsoNormal align=center style=\'text-align:center;line-height:125%;line-height:16.05pt;\'><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>货</span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>
  </span><span style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:
  宋体;mso-ascii-font-family:Arial;mso-hansi-font-family:Arial\'>物</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'> </span><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>名</span><span style=\'mso-bidi-font-size:10.5pt;
  line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'> </span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>称</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=108 colspan=2 style=\'width:81.0pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:31.25pt\'>
  <p class=MsoNormal align=center style=\'margin-right:-5.35pt;mso-para-margin-right:
  -.51gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>No.
  of Items<o:p></o:p></span></p>
  <p class=MsoNormal align=center style=\'margin-right:-5.35pt;mso-para-margin-right:
  -.51gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:Arial;
  mso-hansi-font-family:Arial\'>数</span><span style=\'mso-bidi-font-size:10.5pt;
  line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'> </span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>量</span><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=96 colspan=2 style=\'width:72.0pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:31.25pt\'>
  <p class=MsoNormal align=center style=\'margin-right:-4.85pt;mso-para-margin-right:
  -.46gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>Unit
  Value<o:p></o:p></span></p>
  <p class=MsoNormal align=center style=\'margin-right:-4.85pt;mso-para-margin-right:
  -.46gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>(USD)</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>单价</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
  <td width=132 colspan=2 style=\'width:99.0pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:31.25pt\'>
  <p class=MsoNormal align=center style=\'margin-right:-4.85pt;mso-para-margin-right:
  -.46gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>Total
  Value<o:p></o:p></span></p>
  <p class=MsoNormal align=center style=\'margin-right:-4.85pt;mso-para-margin-right:
  -.46gd;text-align:center;line-height:125%;line-height:16.05pt;\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;line-height:125%;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>(USD)</span><span
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>总价</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'><o:p></o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:14;page-break-inside:avoid;height:22.5pt\'>
  <td width=283 colspan=2 valign=top style=\'width:212.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:22.5pt\'>
  <p class=tgt2 style=\'background:#FAFAFA;line-height:22.05pt;text-align:center\'><span lang=EN-US style=\'font-size:
  13.5pt;line-height:150%;font-family:Arial;color:#2B2B2B;background:#F8F8F8;
  font-weight:normal\'>Mobile phone</span><span lang=EN-US style=\'font-size:
  10.5pt;line-height:150%;font-family:Arial;mso-bidi-font-family:宋体;font-weight:
  normal;mso-bidi-font-weight:bold\'><o:p></o:p></span></p>
  </td>
  <td width=108 colspan=2 valign=top style=\'width:81.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.5pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:18.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>'.$count.'<o:p></o:p></span></p>
  </td>
  <td width=96 colspan=2 valign=top style=\'width:72.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.5pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:18.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>'.$danjia.'<o:p></o:p></span></p>
  </td>
  <td width=132 colspan=2 valign=top style=\'width:99.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.5pt\'>
  <p class=MsoNormal style=\'line-height:125%;line-height:18.05pt;\'><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;line-height:125%;font-family:Arial;
  mso-bidi-font-family:"Times New Roman"\'>'.$total.'<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style=\'mso-yfti-irow:15;mso-yfti-lastrow:yes;page-break-inside:avoid;
  height:22.8pt\'>
  <td width=487 colspan=6 valign=top style=\'width:365.4pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:22.8pt\'>
  <p class=MsoNormal align=right style=\'text-align:right;line-height:15.0pt;
  mso-line-height-rule:exactly\'><span lang=EN-US style=\'mso-bidi-font-size:
  10.5pt;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>Total Invoice
  Value</span><span style=\'mso-bidi-font-size:10.5pt;font-family:宋体;mso-ascii-font-family:
  Arial;mso-hansi-font-family:Arial\'>总价</span><span lang=EN-US
  style=\'mso-bidi-font-size:10.5pt;font-family:Arial;mso-bidi-font-family:"Times New Roman"\'>:<o:p></o:p></span></p>
  </td>
  <td width=132 colspan=2 valign=top style=\'width:99.0pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:22.8pt\'>
  <p class=MsoNormal style=\'line-height:15.0pt;mso-line-height-rule:exactly\'><span
  lang=EN-US style=\'mso-bidi-font-size:10.5pt;font-family:Arial;mso-bidi-font-family:
  "Times New Roman"\'>'.$total.'<o:p></o:p></span></p>
  </td>
 </tr>
 <![if !supportMisalignedColumns]>
 <tr height=0>
  <td width=187 style=\'border:none\'></td>
  <td width=96 style=\'border:none\'></td>
  <td width=48 style=\'border:none\'></td>
  <td width=60 style=\'border:none\'></td>
  <td width=84 style=\'border:none\'></td>
  <td width=12 style=\'border:none\'></td>
  <td width=12 style=\'border:none\'></td>
  <td width=120 style=\'border:none\'></td>
 </tr>
 <![endif]>
</table>
<p class=MsoNormal style=\'line-height:8.0pt;mso-line-height-rule:exactly\'><span
lang=EN-US style=\'font-size:12.0pt;mso-bidi-font-size:10.0pt;font-family:Arial;
mso-bidi-font-family:"Times New Roman";color:#002060\'><o:p>&nbsp;</o:p></span></p>
<p class=MsoNormal style=\'line-height:19.0pt;mso-line-height-rule:exactly\'><span
lang=EN-US style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";
color:#002060\'>I declare that the above information is true and correct to the
best of my knowledge,<o:p></o:p></span></p>
<p class=MsoNormal style=\'line-height:19.0pt;mso-line-height-rule:exactly\'><span
lang=EN-US style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";
color:#002060\'>And that the goods are origin china<o:p></o:p></span></p>
<p class=MsoNormal style=\'line-height:19.0pt;mso-line-height-rule:exactly\'><span
style=\'font-family:宋体;mso-ascii-font-family:Arial;mso-hansi-font-family:Arial;
color:#002060\'>本人认为以上提供的资料属实和正确，货物原产地是：中国</span><span lang=EN-US
style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";color:#002060\'><o:p></o:p></span></p>
<p class=MsoNormal style=\'line-height:19.0pt;mso-line-height-rule:exactly\'><span
lang=EN-US style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";
color:#002060\'>Authorized Signature</span><span style=\'font-family:宋体;
mso-ascii-font-family:Arial;mso-hansi-font-family:Arial;color:#002060\'>签名：</span><u><span
style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";color:#002060\'>
<span lang=EN-US><span
style=\'mso-spacerun:yes\'>'.$invoice['signature'].'</span><span
style=\'mso-spacerun:yes\'>&nbsp;</span></span></span></u><span lang=EN-US
style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";color:#002060\'><span
style=\'mso-spacerun:yes\'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>DATE</span><span
style=\'font-family:宋体;mso-ascii-font-family:Arial;mso-hansi-font-family:Arial;
color:#002060\'>日期：</span><u><span lang=EN-US style=\'font-family:Arial;
mso-bidi-font-family:"Times New Roman";color:#002060\'><span
style=\'mso-spacerun:yes\'>'.$invoice['invoice_date'].'</span></span></u><span lang=EN-US
style=\'font-family:Arial;mso-bidi-font-family:"Times New Roman";color:#002060\'><span
style=\'mso-spacerun:yes\'></span><o:p></o:p></span></p>
</div>
</body>
';
echo $html;
ob_start(); //打开缓冲区
header("Cache-Control: public");
Header("Content-type: application/octet-stream");
Header("Accept-Ranges: bytes");
if (strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')) {
header('Content-Disposition: attachment; filename=test.doc');
}else if (strpos($_SERVER["HTTP_USER_AGENT"],'Firefox')) {
Header('Content-Disposition: attachment; filename=test.doc');
} else {
header('Content-Disposition: attachment; filename='.$invoice['order_sn'].'.doc');
}
header("Pragma:no-cache");
header("Expires:0");
ob_end_flush();//输出全部内容到浏览器
















