<?php
namespace Ibnab\OwlSlider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {
	/**
	 * {@inheritdoc}
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		/**
		 * Drop tables if exists
		 */
		$installer->getConnection()->dropTable($installer->getTable('ibnab_owlslider_sliders'));
		$installer->getConnection()->dropTable($installer->getTable('ibnab_owlslider_banners'));
                $installer->getConnection()->dropTable($installer->getTable('ibnab_owlslider_sliders_banners'));

		$installer->getConnection()->dropTable($installer->getTable('ibnab_owlslider_value'));

                //http://www.owlcarousel.owlgraphic.com/
		$table = $installer->getConnection()->newTable(
			$installer->getTable('ibnab_owlslider_sliders')
		)->addColumn(
			'slider_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Slider ID'
		)->addColumn(
			'title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false, 'default' => ''],
			'Slider title'
		)->addColumn(
			'productImageview',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true, 'default' => ''],
			'Image format'
		)->addColumn(
			'showTitle',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Show title'
		)->addColumn('type',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            256,
                            [],
                            'Slyder type'
                )->addColumn('grid',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            [],
                            'Display items grid'
                )->addColumn('excludeFromCart',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '0'],
                            'Don\'t display slider on cart page'
                )->addColumn('excludeFromCheckout',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '0'],
                            'Don\'t display slider on checkout'
                )->addColumn('displayPrice',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '1'],
                            'Display product price'
                )->addColumn('displayCart',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '1'],
                            'Display add to cart button '
                 )->addColumn('displayWishlist',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '1'],
                            'Display add to wish list'                       
                )->addColumn('displayCompare',
                            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => '1'],
                            'Display add to compare'
                )->addColumn(
			'productsNumber',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			15,
			['nullable' => false, 'default' => ''],
			'Number of products in slider'
		)->addColumn('location',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            256,
                            [],
                            'Slider location'
                )->addColumn('product_imageview',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            256,
                            [],
                            'Product Image View'
                )->addColumn('startTime',
                            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            null,
                            [],
                            'Slider start time'
                )->addColumn('endTime',
                            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            null,
                            [],
                            'Slider end time'
                )->addColumn(
			'items',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '5'],
			'Items'
		)->addColumn(
			'itemsDesktop',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '4'],
			'Items Desktop'
		)->addColumn(
			'itemsDesktopSmall',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '3'],
			'Items Desktop Small'
		)->addColumn(
			'itemsTablet',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '2'],
			'Items Tablet'
		)->addColumn(
			'itemsMobile',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Items Mobile'
		)->addColumn(
			'singleItem',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Is One Slider'
		)->addColumn(
			'margin',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Margin'
		)->addColumn(
			'loop',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Loop'
		)->addColumn(
			'center',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Loop'
		)->addColumn(
			'mouseDrag',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Mouse Drag'
		)->addColumn(
			'touchDrag',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Touch Drag'
		)->addColumn(
			'pullDrag',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Pull Drag'
		)->addColumn(
			'freeDrag',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Free Drag'
		)->addColumn(
			'stagePadding',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Stage Padding'
		)->addColumn(
			'merge',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Merge'
		)->addColumn(
			'mergeFit',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Merge Fit'
		)->addColumn(
			'autoWidth',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Auto Width'
		)->addColumn(
			'startPosition',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			20,
			['nullable' => false, 'default' => '0'],
			'Start Position'
		)->addColumn(
			'URLhashListener',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'URL hash Listener'
		)->addColumn(
			'nav',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Nav'
		)->addColumn(
			'navRewind',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Nav Rewind'
		)->addColumn(
			'navText',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			44,
			['nullable' => false, 'default' => "[]"],
			'Nav Text'
		)->addColumn(
			'slideBy',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '1'],
			'Slide By'
		)->addColumn(
			'dots',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Dots'
		)->addColumn(
			'dotsEach',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Dots Each'
		)->addColumn(
			'dotData',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'dot Data'
		)->addColumn(
			'lazyLoad',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Lazy Load'
		)->addColumn(
			'autoplay',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Auto Play'
		)->addColumn(
			'autoplayTimeout',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '5000'],
			'Auto Play Timeout'
		)->addColumn(
			'autoplayHoverPause',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Auto Play Hove Pause'
		)->addColumn(
			'smartSpeed',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '500'],
			'Smart Speed'
		)->addColumn(
			'autoplaySpeed',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Auto Play Speed'
		)->addColumn(
			'navSpeed',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Nav Speed'
		)->addColumn(
			'dotsSpeed',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Dots Speed'
		)->addColumn(
			'dragEndSpeed',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Drag EndSpeed'
		)->addColumn(
			'responsive',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Responsive'
		)->addColumn(
			'responsiveRefreshRate',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '200'],
			'Responsive Refresh Rate'
		)->addColumn(
			'video',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '0'],
			'Video'
		)->addColumn(
			'videoHeight',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Video Height'
		)->addColumn(
			'videoWidth',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => false, 'default' => '0'],
			'Video Width'
		)->addColumn(
			'animateOut',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			30,
			['nullable' => false, 'default' => '0'],
			'Animate Out'
		)->addColumn(
			'animateIn',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			30,
			['nullable' => false, 'default' => '0'],
			'Animate In'
		)->addColumn(
			'keys',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => true, 'default' => '1'],
			'Enable keyboard'
		)->addColumn(
			'is_active',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Slider status'
		)->addColumn(
			'sortType',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true],
			'Sort type'
		)->addColumn(
			'styleSlide',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Slider style'
		)->addIndex(
                        $installer->getIdxName('ibnab_owlslider_sliders', ['slider_id']),
                        ['slider_id']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_sliders', ['is_active']),
                        ['is_active']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_sliders', ['styleSlide']),
                        ['styleSlide']
                );
		$installer->getConnection()->createTable($table);


		$table = $installer->getConnection()->newTable(
			$installer->getTable('ibnab_owlslider_banners')
		)->addColumn(
			'banner_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Banner ID'
		)->addColumn(
			'name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false, 'default' => ''],
			'Banner name'
		)->addColumn(
			'order',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true, 'default' => 0],
			'Banner order'
		)->addColumn(
			'slider_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true],
			'Slider Id'
		)->addColumn(
			'is_active',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Banner status'
		)->addColumn(
			'url',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true, 'default' => ''],
			'Banner click url'
		)->addColumn(
			'target',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			10,
			['nullable' => true, 'default' => ''],
			'Banner target'
		)->addColumn(
			'image',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Banner image'
		)->addColumn(
			'imageAlt',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Banner image alt'
		)->addColumn(
			'startTime',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
			null,
			['nullable' => true],
			'Banner starting time'
		)->addColumn(
			'endTime',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
			null,
			['nullable' => true],
			'Banner ending time'
		)->addIndex(
                        $installer->getIdxName('ibnab_owlslider_banners', ['banner_id']),
                        ['banner_id']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_banners', ['slider_id']),
                        ['slider_id']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_banners', ['is_active']),
                        ['is_active']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_banners', ['startTime']),
                        ['startTime']
                )->addIndex(
                        $installer->getIdxName('ibnab_owlslider_banners', ['endTime']),
                        ['endTime']
                );
		$installer->getConnection()->createTable($table);
         /**
         * Create table 'ibnab_owlslider_sliders_banners'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ibnab_owlslider_sliders_banners')
        )->addColumn(
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Slider ID'
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Banner ID'
        )->addIndex(
            $installer->getIdxName('ibnab_owlslider_sliders_banners', ['slider_id']),
            ['slider_id']
        )->addIndex(
            $installer->getIdxName('ibnab_owlslider_sliders_banners', ['banner_id']),
            ['banner_id']
        )->addForeignKey(
            $installer->getFkName('ibnab_owlslider_sliders_banners', 'slider_id', 'ibnab_owlslider_sliders', 'slider_id'),
            'slider_id',
            $installer->getTable('ibnab_owlslider_sliders'),
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ibnab_owlslider_sliders_banners', 'banner_id', 'ibnab_owlslider_banners', 'banner_id'),
            'banner_id',
            $installer->getTable('ibnab_owlslider_banners'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'cms_page_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ibnab_owlslider_banners_store')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Banner ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('ibnab_owlslider_banners_store', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('ibnab_owlslider_banners_store', ['banner_id']),
            ['banner_id']
        )->addForeignKey(
            $installer->getFkName('ibnab_owlslider_banners_store', 'banner_id', 'ibnab_owlslider_banners', 'banner_id'),
            'banner_id',
            $installer->getTable('ibnab_owlslider_banners'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ibnab_owlslider_banners_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Banner To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);
        
        
        $table_name = 'ibnab_owlslider_sliders_products';

        $table = $setup->getConnection()->newTable($setup->getTable($table_name))
            ->addColumn('slider_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true, 'primary' => true],
                        'Slider ID')
            ->addColumn('product_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true, 'primary' => true],
                        'Product ID')
            ->addColumn('position',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'Position')
            ->addIndex($setup->getIdxName($table_name,'product_id'),'product_id')
            ->addForeignKey($setup->getFkName($table_name,'slider_id','ibnab_owlslider_sliders','slider_id'),
                            'slider_id',
                            $setup->getTable('ibnab_owlslider_sliders'),
                            'slider_id',
                            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($setup->getFkName($table_name,'product_id','catalog_product_entity','entity_id'),
                            'product_id',
                            $setup->getTable('catalog_product_entity'),
                            'entity_id',
                            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Product table realated to slider');

        $installer->getConnection()->createTable($table);
               /*
		$table = $installer->getConnection()->newTable(
			$installer->getTable('ibnab_owlslider_value')
		)->addColumn(
			'value_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Value ID'
		)->addColumn(
			'banner_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Banner ID'
		)->addColumn(
			'store_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Store view ID'
		)->addColumn(
			'attribute_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			63,
			['nullable' => false, 'default' => ''],
			'Attribute Name'
		)->addColumn(
			'value',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => false],
			'Value'
		)->addIndex(
			$installer->getIdxName(
				'ibnab_owlslider_value',
				['banner_id', 'store_id', 'attribute_name'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
			),
			['banner_id', 'store_id', 'attribute_name'],
			['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
		)->addIndex(
			$installer->getIdxName('ibnab_owlslider_value', ['banner_id']),
			['banner_id']
		)->addIndex(
			$installer->getIdxName('ibnab_owlslider_value', ['store_id']),
			['store_id']
		)->addForeignKey(
			$installer->getFkName(
				'ibnab_owlslider_value',
				'banner_id',
				'ibnab_owlslider_banners',
				'banner_id'
			),
			'banner_id',
			$installer->getTable('ibnab_owlslider_banners'),
			'id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		)->addForeignKey(
			$installer->getFkName(
				'ibnab_owlslider_value',
				'store_id',
				'store',
				'store_id'
			),
			'store_id',
			$installer->getTable('store'),
			'store_id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		);
		$installer->getConnection()->createTable($table);
		*/
		$installer->endSetup();

	}
}
