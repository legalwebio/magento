<?php


namespace LegalWeb\Cloud\Model\Config\Source;


use Magento\Cms\Model\Page as PageModel;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Page implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    private $options;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->createOptions();
        }

        return $this->options;
    }

    private function createOptions(): array
    {
        $options = [
            [
                'value' => '',
                'label' => __('-- Please Select --')
            ]
        ];

        $this->collectionFactory->create()
            ->each(function (PageModel $page) use (&$options) {
                $options[] = [
                    'value' => $page->getId(),
                    'label' => $page->getTitle()
                ];
            });

        return $options;
    }
}
