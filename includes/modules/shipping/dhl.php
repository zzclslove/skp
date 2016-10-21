<?php
/**
* ECSHOP dhl插件
* $Date: 2007-07-2  $
* $Id: dhl.php 8272 2007-07-7 10:11:35Z fanan $
*/
$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/dhl.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}
/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;
    /* 配送方式插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'dhl';
    $modules[$i]['version'] = '1.1.0';
    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'dhl_express_desc';
    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = false;
    /* 插件的作者 */
    $modules[$i]['author']  = 'goalbell';
    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.ygdns.com';
    /* 配送接口需要的参数 */
    $modules[$i]['configure'] = array(
        array('name' => 'base1_fee',     'value'=>100),
        array('name' => 'base2_fee',     'value'=>100),
        array('name' => 'base3_fee',     'value'=>100),
        array('name' => 'base4_fee',     'value'=>100),
        array('name' => 'base5_fee',     'value'=>100),
        array('name' => 'base6_fee',     'value'=>100),
        array('name' => 'base7_fee',     'value'=>100),
        array('name' => 'base8_fee',     'value'=>100),
        array('name' => 'base9_fee',     'value'=>100),
        array('name' => 'base10_fee',     'value'=>100),
        array('name' => 'base11_fee',     'value'=>100),
        array('name' => 'base12_fee',     'value'=>100),
        array('name' => 'base13_fee',     'value'=>100),
        array('name' => 'base14_fee',     'value'=>100),
        array('name' => 'base15_fee',     'value'=>100),
        array('name' => 'base16_fee',     'value'=>100),
        array('name' => 'base17_fee',     'value'=>100),
        array('name' => 'base18_fee',     'value'=>100),
        array('name' => 'base19_fee',     'value'=>100),
        array('name' => 'base20_fee',     'value'=>100),
        array('name' => 'base21_fee',     'value'=>100),
        array('name' => 'base22_fee',     'value'=>100),
        array('name' => 'base23_fee',     'value'=>100),
        array('name' => 'base24_fee',     'value'=>100),
        array('name' => 'base25_fee',     'value'=>100),
        array('name' => 'base26_fee',     'value'=>100),
        array('name' => 'base27_fee',     'value'=>100),
        array('name' => 'base28_fee',     'value'=>100),
        array('name' => 'base29_fee',     'value'=>100),
        array('name' => 'base30_fee',     'value'=>100),
        array('name' => 'base31_fee',     'value'=>100),
        array('name' => 'base32_fee',     'value'=>100),
        array('name' => 'base33_fee',     'value'=>100),
        array('name' => 'base34_fee',     'value'=>100),
        array('name' => 'base35_fee',     'value'=>100),
        array('name' => 'base36_fee',     'value'=>100),
        array('name' => 'base37_fee',     'value'=>100),
        array('name' => 'base38_fee',     'value'=>100),
        array('name' => 'base39_fee',     'value'=>100),
        array('name' => 'base40_fee',     'value'=>100),
        array('name' => 'step1_fee',     'value'=>100),
        array('name' => 'step2_fee',     'value'=>100),
        array('name' => 'step3_fee',     'value'=>100),
        array('name' => 'step4_fee',     'value'=>100),
        array('name' => 'step5_fee',     'value'=>100),
        array('name' => 'step6_fee',     'value'=>100),
        array('name' => 'step7_fee',     'value'=>100),
        array('name' => 'step8_fee',     'value'=>100)
    );
    return;
}
/**
* 包裹费用计算方式
* ====================================================================================
* 500g及500g以内                             20元
* -------------------------------------------------------------------------------------
* 续重每500克或其零数                       15元
* -------------------------------------------------------------------------------------
*
*/
class dhl
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */
    /**
     * 配置信息
     */
    var $configure;
    /*------------------------------------------------------ */
    //-- PUBLIC METHODs
    /*------------------------------------------------------ */
    /**
     * 构造函数
     *
     * @param: $configure[array]    配送方式的参数的数组
     *
     * @return null
     */
    function dhl($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }
    /**
     * 计算订单的配送费用的函数
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金额
     * @return  decimal
     */
    function calculate($goods_weight, $goods_amount)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            $fee = $this->configure['base1_fee'];
            if($goods_weight <= 0.5){
                $fee = $this->configure['base1_fee'];
            }else if($goods_weight > 0.5 && $goods_weight <= 1.0){
                $fee = $this->configure['base2_fee'];
            }else if($goods_weight > 1.0 && $goods_weight <= 1.5){
                $fee = $this->configure['base3_fee'];
            }else if($goods_weight > 1.5 && $goods_weight <= 2.0){
                $fee = $this->configure['base4_fee'];
            }else if($goods_weight > 2.0 && $goods_weight <= 2.5){
                $fee = $this->configure['base5_fee'];
            }else if($goods_weight > 2.5 && $goods_weight <= 3.0){
                $fee = $this->configure['base6_fee'];
            }else if($goods_weight > 3.0 && $goods_weight <= 3.5){
                $fee = $this->configure['base7_fee'];
            }else if($goods_weight > 3.5 && $goods_weight <= 4.0){
                $fee = $this->configure['base8_fee'];
            }else if($goods_weight > 4.0 && $goods_weight <= 4.5){
                $fee = $this->configure['base9_fee'];
            }else if($goods_weight > 4.5 && $goods_weight <= 5.0){
                $fee = $this->configure['base10_fee'];
            }else if($goods_weight > 5.0 && $goods_weight <= 5.5){
                $fee = $this->configure['base11_fee'];
            }else if($goods_weight > 5.5 && $goods_weight <= 6.0){
                $fee = $this->configure['base12_fee'];
            }else if($goods_weight > 6.0 && $goods_weight <= 6.5){
                $fee = $this->configure['base13_fee'];
            }else if($goods_weight > 6.5 && $goods_weight <= 7.0){
                $fee = $this->configure['base14_fee'];
            }else if($goods_weight > 7.0 && $goods_weight <= 7.5){
                $fee = $this->configure['base15_fee'];
            }else if($goods_weight > 7.5 && $goods_weight <= 8.0){
                $fee = $this->configure['base16_fee'];
            }else if($goods_weight > 8.0 && $goods_weight <= 8.5){
                $fee = $this->configure['base17_fee'];
            }else if($goods_weight > 8.5 && $goods_weight <= 9.0){
                $fee = $this->configure['base18_fee'];
            }else if($goods_weight > 9.0 && $goods_weight <= 9.5){
                $fee = $this->configure['base19_fee'];
            }else if($goods_weight > 9.5 && $goods_weight <= 10.0){
                $fee = $this->configure['base20_fee'];
            }else if($goods_weight > 10.0 && $goods_weight <= 10.5){
                $fee = $this->configure['base21_fee'];
            }else if($goods_weight > 10.5 && $goods_weight <= 11.0){
                $fee = $this->configure['base22_fee'];
            }else if($goods_weight > 11.0 && $goods_weight <= 11.5){
                $fee = $this->configure['base23_fee'];
            }else if($goods_weight > 11.5 && $goods_weight <= 12.0){
                $fee = $this->configure['base24_fee'];
            }else if($goods_weight > 12.0 && $goods_weight <= 12.5){
                $fee = $this->configure['base25_fee'];
            }else if($goods_weight > 12.5 && $goods_weight <= 13.0){
                $fee = $this->configure['base26_fee'];
            }else if($goods_weight > 13.0 && $goods_weight <= 13.5){
                $fee = $this->configure['base27_fee'];
            }else if($goods_weight > 13.5 && $goods_weight <= 14.0){
                $fee = $this->configure['base28_fee'];
            }else if($goods_weight > 14.0 && $goods_weight <= 14.5){
                $fee = $this->configure['base29_fee'];
            }else if($goods_weight > 14.5 && $goods_weight <= 15.0){
                $fee = $this->configure['base30_fee'];
            }else if($goods_weight > 15.0 && $goods_weight <= 15.5){
                $fee = $this->configure['base31_fee'];
            }else if($goods_weight > 15.5 && $goods_weight <= 16.0){
                $fee = $this->configure['base32_fee'];
            }else if($goods_weight > 16.0 && $goods_weight <= 16.5){
                $fee = $this->configure['base33_fee'];
            }else if($goods_weight > 16.5 && $goods_weight <= 17.0){
                $fee = $this->configure['base34_fee'];
            }else if($goods_weight > 17.0 && $goods_weight <= 17.5){
                $fee = $this->configure['base35_fee'];
            }else if($goods_weight > 17.5 && $goods_weight <= 18.0){
                $fee = $this->configure['base36_fee'];
            }else if($goods_weight > 18.0 && $goods_weight <= 18.5){
                $fee = $this->configure['base37_fee'];
            }else if($goods_weight > 18.5 && $goods_weight <= 19.0){
                $fee = $this->configure['base38_fee'];
            }else if($goods_weight > 19.0 && $goods_weight <= 19.5){
                $fee = $this->configure['base39_fee'];
            }else if($goods_weight > 19.5 && $goods_weight <= 20.0){
                $fee = $this->configure['base40_fee'];
            }else if($goods_weight > 20.0 && $goods_weight <= 30){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 20) * $this->configure['step1_fee'];
            }else if($goods_weight > 30.0 && $goods_weight <= 50){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 30) * $this->configure['step2_fee'] +
                    10 * $this->configure['step1_fee'];
            }else if($goods_weight > 50.0 && $goods_weight <= 70){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 50) * $this->configure['step3_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'];
            }else if($goods_weight > 70.0 && $goods_weight <= 100){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 70) * $this->configure['step4_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'] +
                    20 * $this->configure['step3_fee'];
            }else if($goods_weight > 100.0 && $goods_weight <= 200){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 100) * $this->configure['step5_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'] +
                    20 * $this->configure['step3_fee'] +
                    30 * $this->configure['step4_fee'];
            }else if($goods_weight > 200.0 && $goods_weight <= 300.0){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 200) * $this->configure['step6_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'] +
                    20 * $this->configure['step3_fee'] +
                    30 * $this->configure['step4_fee'] +
                    100 * $this->configure['step5_fee'];
            }else if($goods_weight > 300.0 && $goods_weight <= 500.0){
                $fee = $this->configure['base40_fee'] + ($goods_weight - 300) * $this->configure['step7_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'] +
                    20 * $this->configure['step3_fee'] +
                    30 * $this->configure['step4_fee'] +
                    100 * $this->configure['step5_fee'] +
                    100 * $this->configure['step6_fee'];
            }else{
                $fee = $this->configure['base40_fee'] + ($goods_weight - 500) * $this->configure['step8_fee'] +
                    10 * $this->configure['step1_fee'] +
                    20 * $this->configure['step2_fee'] +
                    20 * $this->configure['step3_fee'] +
                    30 * $this->configure['step4_fee'] +
                    100 * $this->configure['step5_fee'] +
                    100 * $this->configure['step6_fee'] +
                    200 * $this->configure['step7_fee'];
            }
            return $fee * 0.1503 * 1.6;
        }
    }
    /**
     * 查询发货状态
     *
     * @access  public
     * @param   string  $invoice_sn     发货单号
     * @return  string
     */
    function query($invoice_sn)
    {
        $str = '<form style="margin:0px" methods="post" '.
            'action="http://www.cn.dhl.com/publish/cn/zh/eshipping/track.high.html" name="queryForm_' .$invoice_sn. '" target="_blank">'.
   '<input type="hidden" name="pageToInclude" value="RESULTS" />'.
            '<input type="hidden" name="AWB" value="' .$invoice_sn. '" />'.
            '<a href="javascript:document.forms[\'queryForm_' .$invoice_sn. '\'].submit();">' .$invoice_sn. '</a>'.
            '<input type="hidden" name="type" value="fasttrack"/>'.
            '</form>';
        return $str;
    }
}
?>