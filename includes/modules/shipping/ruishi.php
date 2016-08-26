<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 11:32
 */
$shipping_lang = ROOT_PATH.'languages/' .$GLOBALS['_CFG']['lang']. '/shipping/ruishi.php';
if (file_exists($shipping_lang))
{
    global $_LANG;
    include_once($shipping_lang);
}
if (isset($set_modules) && $set_modules == TRUE) {
    $i = (isset($modules)) ? count($modules) : 0;
    $modules[$i]['code']    = 'ruishi';
    $modules[$i]['version'] = '1.1.0';
    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'ruishi_express_desc';
    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = false;
    /* 插件的作者 */
    $modules[$i]['author']  = 'aiken';

    $modules[$i]['configure'] = array(
        array('name' => 'pingyou_fee',     'value'=>0),
        array('name' => 'guahao_fee',     'value'=>0),
        array('name' => 'weight_limit',   'value'=>2)
    );
    return;
}

/**
 * 包裹费用计算方式
 * ====================================================================================
 * 2KG 以内
 * -------------------------------------------------------------------------------------
 *
 * -------------------------------------------------------------------------------------
 *
 */
class ruishi
{
    var $configure;

    function ruishi($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }

    function calculate($goods_weight, $goods_amount)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money']) {
            return 0;
        } else {
            $fee = $goods_weight * $this->configure['pingyou_fee'] + $this->configure['guahao_fee'];
        }

        return $fee * 0.1503;
    }

    function query($invoice_sn)
    {
        $str = '';
        return $str;
    }

}
?>