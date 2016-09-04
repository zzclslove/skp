<?php

/**
 * ECSHOP PayPal插件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: ECSHOP插件网 $
 * $Id: paypal.php 17217 2014-08-18 06:29:08Z ECSHOP插件网 $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/paypal.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'paypal_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP插件网';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.edait.cn';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'client_id', 'type' => 'text', 'value' => ''),
        array('name' => 'client_secret', 'type' => 'text', 'value' => ''),
        array('name' => 'paypal_currency', 'type' => 'select', 'value' => 'USD'),
        array('name' => 'sandbox', 'type' => 'select', 'value' => '')
    );

    return;
}

/**
 * 类
 */
class paypal
{
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function __construct()
    {
        $this->paypal();
    }

    function paypal()
    {
    }

    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        $data_order_id      = $order['order_id'];
        //$data_client_id     = $payment['client_id'];
        //$data_client_secret = $payment['client_secret'];
        $data_return_url    = $GLOBALS['ecs']->url() . 'paypal_respond.php?success=true';
        $cancel_return      = $GLOBALS['ecs']->url() . 'paypal_respond.php?success=false';
        $sub_fix = preg_replace('/(.*?:\/\/(www\.)?)(.*?)\.(:?.*?)$/','$3',$data_return_url);
        $invoice = $data_order_id.'-'.$sub_fix;
        if($payment['sandbox'])
        {
            $mode = 'sandbox';
        }
        else
        {
            $mode = 'live';
        }

        $def_url  = '<br /><form style="text-align:center;" action="paypal.php" method="post" target="_blank">' .   // 不能省略             // 不能省略
            "<input type='hidden' name='mode' value='$mode'>" .                 // payment for
            "<input type='hidden' name='orderid' value='$data_order_id'>" .                 // payment for
            "<input type='hidden' name='item_name' value='$order[order_sn]'>" .             // payment for
            "<input type='hidden' name='return' value='$data_return_url'>" .                // 付款后页面
            "<input type='hidden' name='invoice' value='$invoice'>" .                       // 订单号
            "<input type='hidden' name='cancel_return' value='$cancel_return'>" .
            "<input type='submit' value='" . $GLOBALS['_LANG']['paypal_button'] . "'>" .    // 按钮
            "</form><br />";
        return $def_url;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment = get_payment('paypal');

        // assign posted variables to local variables
        $order_sn = intval($_POST['invoice']);
        $action_note = $GLOBALS['_LANG']['paypal_txn_id'] . '：' . $_POST['txn_id'];

        //输出$_POST的所有数据
        foreach($_POST as $key => $value)
        {  
            echo "POST Data: $key -> $value <br>";  
        }

        if($_POST['payer_status'] == 'verified')
        {
            // check the payment_status is Completed
            if ($_POST['payment_status'] != 'Completed' && $_POST['payment_status'] != 'Pending')
            {
				return false;
            }

            // check that receiver_email is your Primary PayPal email
            if ($_POST['receiver_email'] != $payment['paypal_account'])
            {
				return false;
            }

            // check that payment_amount/payment_currency are correct
            $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$order_sn'";
            if ($GLOBALS['db']->getOne($sql) != $_POST['mc_gross'])
            {
				return false;
            }
            if ($payment['paypal_currency'] != $_POST['mc_currency'])
            {
				return false;
            }

            // process payment
            order_paid($order_sn, PS_PAYED, $action_note);

            return true;
        }
        else
        {
            // log for manual investigation
            return false;
        }
    }
}

?>