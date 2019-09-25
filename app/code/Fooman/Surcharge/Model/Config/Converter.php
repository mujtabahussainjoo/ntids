<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     *
     * @return array
     */
    public function convert($source)
    {
        $output = ['types' => []];

        if (!$source instanceof \DOMDocument) {
            return $output;
        }

        $xpath = new \DOMXPath($source);
        $types = $xpath->query('//types/*');

        foreach ($types as $element) {
            $instance = $element->getElementsByTagName('instance')->item(0);
            $label = $element->getElementsByTagName('label')->item(0);
            $tab = $element->getElementsByTagName('tab')->item(0);

            $output['types'][] = [
                'type' => (string)$element->tagName,
                'instance' => (string)$instance->nodeValue,
                'label' => (string)$label->nodeValue,
                'tab' => (string)$tab->nodeValue,
            ];
        }

        return $output;
    }
}
