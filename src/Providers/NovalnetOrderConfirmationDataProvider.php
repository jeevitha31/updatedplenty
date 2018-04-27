<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License
 *
 * @author Novalnet <technic@novalnet.de>
 * @copyright Novalnet
 * @license GNU General Public License
 *
 * Script : NovalnetOrderConfirmationDataProvider.php
 *
 */

namespace Novalnet\Providers;

use Plenty\Plugin\Templates\Twig;

use Novalnet\Helper\PaymentHelper;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Comment\Contracts\CommentRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use \Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;

/**
 * Class NovalnetOrderConfirmationDataProvider
 *
 * @package Novalnet\Providers
 */
class NovalnetOrderConfirmationDataProvider
{
    /**
     * Setup the Novalnet transaction comments for the requested order
     *
     * @param Twig $twig
     * @param Arguments $arg
     * @return string
     */
    public function call(Twig $twig, PaymentRepositoryContract $paymentRepositoryContract, $arg)
    {
        $paymentHelper = pluginApp(PaymentHelper::class);
	$sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
       // $paymentMethodId = $paymentHelper->getPaymentMethod();
        $order = $arg[0];
        $paymentHelper->testLogTest('CHECK',$order);
        $paymentHelper->testLogTest('CHECK2',$order->properties);
        $paymentHelper->testLogTest('CHECK3',$order['properties']);
	$barzahlentoken = (string)$sessionStorage->getPlugin()->getValue('cashtoken');
	$testmode = (string)$sessionStorage->getPlugin()->getValue('testmode');
	     $paymentHelper->testLogTest('barzahlentoken',$barzahlentoken); 
	     $paymentHelper->testLogTest('testmode_orderconfirmation',$testmode); 
       // if(isset($order->order))
        //    $order = $order->order;
        
        //$properties = !empty($order->properties) ? $order->properties : $order['properties'];
        $properties = $order->properties;//!empty($order->properties) ? $order->properties : $order['properties'];
        $paymentHelper->testLogTest('CHECK4FINAL',$properties);
        $paymentHelper->testLogTest('orderid1',$order->id);
        $paymentHelper->testLogTest('orderid2',$order['id']);
         $orderno = $order['id'];
		$payments = $paymentRepositoryContract->getPaymentsByOrderId($orderno);
		$paymentHelper->testLogTest('paymentrepository',$payments);
        foreach($payments as $payment)
        {
          
            $paymentHelper->testLogTest('CHECKKKK',$payment); 
           // $paymentHelper->testLogTest('CHECKOBJ',is_string($property));                 
            $paymentHelper->testLogTest('CHECKOBJVAL',$payment->mopId);                
            //$paymentHelper->testLogTest('CHECKOBJTYPE',$payment->typeId);
            //if($property->typeId == '3' && $property->value == $paymentMethodId)
            if($paymentHelper->isNovalnetPaymentMethod($payment->mopId))
            {
                //$paymentHelper->testLogTest('CHECK5VAL',$property->value);                
                //$orderId = (int) $order->id;
                $orderId = (int) $payment->order['orderId'];

                $authHelper = pluginApp(AuthHelper::class);
                $orderComments = $authHelper->processUnguarded(
                        function () use ($orderId) {
                            $commentsObj = pluginApp(CommentRepositoryContract::class);
                            $commentsObj->setFilters(['referenceType' => 'order', 'referenceValue' => $orderId]);
                            return $commentsObj->listComments();
                        }
                );
                $paymentHelper->testLogTest('CHECK7CMD',$orderId);
                $paymentHelper->testLogTest('CHECK6CMD',$orderComments);
            $paymentHelper->testLogTest('CHECK8CMD',$order->id);
                $comment = '';
                foreach($orderComments as $data)
                {
                    $comment .= (string)$data->text;
                    $comment .= '</br>';
                }
		    $comment .= 'test_comment123';

              $payment_type = (string)$paymentHelper->getPaymentKeyByMop($payment->mopId);

                return $twig->render('Novalnet::NovalnetOrderHistory', ['comments' => html_entity_decode($comment),'barzahlentoken' => html_entity_decode($barzahlentoken),'payment_type' => html_entity_decode($payment_type),'testmode' => html_entity_decode($testmode)]);
            }
        }
    }
}
