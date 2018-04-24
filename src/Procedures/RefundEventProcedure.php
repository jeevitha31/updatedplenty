<?php
namespace Novalnet\Procedures;

use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Plenty\Plugin\Log\Loggable;

class RefundEventProcedure
{
	 use Loggable;
	 
	 public function run(EventProceduresTriggered $eventTriggered, PaymentService $paymentService, PaymentRepositoryContract $paymentContract, PaymentHelper $paymentHelper)
    {
       
        $order = $eventTriggered->getOrder();

        $this->getLogger(__METHOD__)->error('refund order', $order);
}
	
	
}
?>
