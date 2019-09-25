<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\PdfInvoice\Model;
/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice{
	public function draw(){
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0] = [['text' => $this->string->split($item->getName(), 55, true, true),'font_size' =>8, 'feed' => 35]];
		
		// draw Stockloc
		$objManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productId = $item->getProductId();
		$productObject = $objManager->create('Magento\Catalog\Model\Product')->load($productId);
		$stockloc =$productObject->getStockloc();
		
	// if($stockloc!=''){
        // // draw Stockloc
        // $lines[0][] = ['text' => $stockloc,'feed' => 225,'font_size' =>8,'align' => 'right'];    
	// }else{ 
		 // // draw Stockloc
        // $lines[0][] = ['text' => 'Nil','feed' => 225,'font_size' =>8,'align' => 'right'];  
	// }
		// draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 25),
            'feed' => 352,
			'font_size' =>8,
            'align' => 'right',
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 410,'font_size' =>8, 'align' => 'right'];

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 460;
        $feedSubtotal = $feedPrice + 125;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice,'font_size' =>8, 'align' => 'right'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => 565,'font_size' =>8, 'align' => 'right'];
                $i++;
            }
            // draw Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => $feedPrice,
                'font' => 'normal',
                'align' => 'right',
				'font_size' =>8,
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => 565,
                'font' => 'normal',
                'align' => 'right',
				'font_size' =>8,
            ];
            $i++;
        }

        // draw Tax
        $lines[0][] = [
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 495,
            'font' => 'normal',
            'align' => 'right',
			'font_size' =>8,
        ];

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
					'font_size' =>8
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                    }
                }
            }
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
