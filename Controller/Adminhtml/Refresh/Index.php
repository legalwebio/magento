<?php


namespace LegalWeb\Cloud\Controller\Adminhtml\Refresh;


use Exception;
use LegalWeb\Cloud\Model\ConfigLoader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'LegalWeb_Cloud::config';

    /**
     * @var ConfigLoader
     */
    private $configLoader;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        RedirectFactory $redirectFactory,
        ConfigLoader $configLoader,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->configLoader    = $configLoader;
        $this->redirectFactory = $redirectFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->configLoader->refresh();
            $this->messageManager->addSuccessMessage('Configuration was reloaded');
        } catch (Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage('Configuration couldn\'t be loaded.');
        }

        return $this->redirectFactory->create()->setUrl(
            $this->_redirect->getRefererUrl()
        );
    }
}
