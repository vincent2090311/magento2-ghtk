<?php

namespace Ecom\Ghtk\Controller\Adminhtml\Tracking;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Ecom\Ghtk\Api\Adapter
     */
    protected $apiAdapter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Ecom\Ghtk\Api\Adapter $apiAdapter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Ecom\Ghtk\Api\Adapter $apiAdapter
    ) {
        
        $this->apiAdapter = $apiAdapter;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $response = [];
            try {
                $trackingLabel = $this->getRequest()->getPostValue('tracking_label');
                $result = $this->apiAdapter->getOrderStatus($trackingLabel);
                if ($result['success'] == true) {
                    $response['success'] = true;
                    $response['data'] = $result['order'];
                } else {
                    $response['success'] = false;
                    $response['message'] = __('Sorry, something went wrong.');
                }
            } catch (\Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            return $this->getResponse()->representJson($this->serializer->serialize($response));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order');
    }
}