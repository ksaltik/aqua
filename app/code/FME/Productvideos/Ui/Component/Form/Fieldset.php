<?php
namespace FME\Productvideos\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;

class Fieldset extends BaseFieldset
{
    /**
     * @var FieldFactory
     */
    private $fieldFactory;
    protected $helper;
    public function __construct(
        ContextInterface $context,
        \FME\Productvideos\Helper\Data $helper,
        FieldFactory $fieldFactory,
        array $components = [],
        array $data = []
    ) 
    {
    
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->helper = $helper;
    }

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        $fields = [
            [
                'label' => __('youtubekey'),
                'value' => __($this->helper->getYouTubeApiKey()),
                'formElement' => 'input',
            ]
        ];

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'youtube_key';

            $fieldInstance->setData(
                [
                    'config' => $fieldConfig,
                    'name' => $name
                ]
            );

            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }

        return parent::getChildComponents();
    }
}
