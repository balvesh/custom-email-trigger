<?php

/**
 * Learning_TriggerCustomEmail Button
 * @category Learning
 * @package Learning_TriggerCustomEmail
 * @version 1.0.0
 * @author Learningetzwelt
 */


namespace Learning\TriggerCustomEmail\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Button
 * @package Magento\Paypal\Block\Adminhtml\System\Config
 */
class Button extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/button.phtml';

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData([
            'button_label' => $originalData['button_label'],
            'button_url'   => $this->getUrl("triggercustomemail/index/submit", ['_current' => true]),
            'html_id'      => $element->getHtmlId(),
        ]);

        return $this->_toHtml();
    }
}
