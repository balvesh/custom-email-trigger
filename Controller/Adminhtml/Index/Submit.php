<?php

/**
 * Learning_TriggerCustomEmail submit controller
 * @category Learning
 * @package Learning_TriggerCustomEmail
 * @version 1.0.0
 * @author Learningetzwelt
 */

namespace Learning\TriggerCustomEmail\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Email\Model\Template\SenderResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;
use Mageplaza\Smtp\Helper\Data as SmtpData;
use Mageplaza\Smtp\Mail\Rse\Mail;
use Psr\Log\LoggerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Submit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */


    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SmtpData
     */
    protected $smtpDataHelper;

    /**
     * @var Mail
     */
    protected $mailResource;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var SenderResolver
     */
    protected $senderResolver;

    protected $inlineTranslation;

    protected $storeManager;

    /**
     * Test constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param SmtpData $smtpDataHelper
     * @param Mail $mailResource
     * @param TransportBuilder $transportBuilder
     * @param SenderResolver $senderResolver
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SmtpData $smtpDataHelper,
        Mail $mailResource,
        TransportBuilder $transportBuilder,
        SenderResolver $senderResolver,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->smtpDataHelper = $smtpDataHelper;
        $this->mailResource = $mailResource;
        $this->_transportBuilder = $transportBuilder;
        $this->senderResolver = $senderResolver;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {

        $result = ['status' => false];
        $fromEmail = 'owner@learning.com';
        $params = $this->getRequest()->getParams();
        if ($params && $params['to']) {

            $template = $params['template'];
            $from = $this->senderResolver->resolve(
                isset($params['from']) ? $params['from'] : $fromEmail,
                $this->smtpDataHelper->getScopeId()
            );

            $this->inlineTranslation->suspend();
            $this->_transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId()])
                ->setTemplateVars([])
                ->setFrom($from)
                ->addTo($params['to']);

            try {
                $this->_transportBuilder->getTransport()->sendMessage();
                $result = [
                    'status'  => true,
                    'content' => __('Sent successfully! Please check your email box.')
                ];
            } catch (Exception $e) {
                $result['content'] = $e->getMessage();
                $this->logger->critical($e);
            }
        } else {
            $result['content'] = __('Error');
        }
        $this->inlineTranslation->resume();
        return $this->getResponse()->representJson(SmtpData::jsonEncode($result));
    }
}
