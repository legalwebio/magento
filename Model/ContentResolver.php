<?php


namespace LegalWeb\Cloud\Model;


use Magento\Framework\DataObject;
use Magento\Framework\Locale\ResolverInterface;

class ContentResolver
{
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    public function __construct(
        ResolverInterface $localeResolver
    )
    {
        $this->localeResolver = $localeResolver;
    }

    public function getHtml(DataObject $model, string $path = null): string
    {
        $lang = $this->getLang();
        $data = $model->getData($path ?: $lang);

        if (!$data || !is_array($data)) {
            return $data ?: '';
        }

        if (isset($data[$lang])) {
            return $data[$lang];
        }

        return $data[array_keys($data)[0]] ?? '';
    }

    private function getLang(): string
    {
        $locale = $this->localeResolver->getLocale();
        return explode('_', $locale)[0];
    }

}
