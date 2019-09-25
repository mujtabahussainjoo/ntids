<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model;

class Config
{

    /**
     * @var \Fooman\Surcharge\Model\Config\Data
     */
    private $dataContainer;

    /**
     * Config constructor.
     *
     * @param \Fooman\Surcharge\Model\Config\Data $dataContainer
     */
    public function __construct(\Fooman\Surcharge\Model\Config\Data $dataContainer)
    {
        $this->dataContainer = $dataContainer;
    }

    public function getTypes()
    {
        return $this->dataContainer->get('types');
    }

    public function getType($type)
    {
        $types = $this->getTypes();

        if ($types) {
            foreach ($types as $item) {
                if ($item['type'] === $type) {
                    return $item;
                }
            }
        }

        return null;
    }
}
