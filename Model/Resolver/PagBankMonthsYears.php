<?php


namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Locale\ResolverInterface as LocaleResolverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Model\Config;

class PagBankMonthsYears implements ResolverInterface
{
    /** @var LocaleResolverInterface */
    protected LocaleResolverInterface $localeResolver;
    /** @var DateTime */
    protected DateTime $_date;
    /** @var SerializerInterface */
    protected SerializerInterface $serializer;

    public function __construct(
        LocaleResolverInterface $localeResolver,
        DateTime $date,
        SerializerInterface $serializer
    )
    {
        $this->localeResolver = $localeResolver;
        $this->_date = $date;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array
    {
        return [
            "months" => $this->serializer->serialize($this->getMonths()),
            "years" => $this->serializer->serialize($this->getYears())
        ];
    }

    /**
     * Retrieve list of months translation
     *
     * @return array
     */
    public function getMonths()
    {
        $data = [];
        $months = (new DataBundle())->get(
            $this->localeResolver->getLocale()
        )['calendar']['gregorian']['monthNames']['format']['wide'];
        foreach ($months as $key => $value) {
            $monthNum = ++$key < 10 ? '0' . $key : $key;
            $data[$key] = $monthNum . ' - ' . $value;
        }
        return $data;
    }

    /**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = [];
        $first = (int)$this->_date->date('Y');
        for ($index = 0; $index <= Config::YEARS_RANGE; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }
}
