<?php

namespace MagePsycho\RedirectPro\Block\Adminhtml\System\Config\Form\Field;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ManualContent extends \Magento\Backend\Block\Template
{
    public function getContent()
    {
        return json_encode([
          'html' => $this->getManualContent()
        ]);
    }

    protected function getManualContent()
    {
        return "<div style='padding: 10px;'>
You can use either a relative url or an absolute url for custom url redirection. Also you can use custom variables like <code>{{referer}}</code>, <code>{{redirect_to}}</code>, <code>{{assigned_base_url}}</code> etc. as redirection url.
<br /><br />
<strong>Valid Examples:</strong><br />
- <code>/</code><br />
- /welcome<br />
- /vendor/<code>{{user_name}}</code> <br />
- <code>{{referer}}</code><br />
- <code>{{assigned_base_url}}</code><br />
- <code>{{redirect_to}}</code><br />
- http://my-another-store.com/welcome <br />
- http://mystore.com/read-me-first <br /><br />
<strong>Notes:</strong>
<br /> 1. <code>/</code> denotes the base url of current store, used for relative url redirection.<br />
2. <code>{{referer}}</code> variable is used when you want to redirect back to previous page. <br />
3. <code>{{assigned_base_url}}</code> variable is used when you want to redirect customer to their assigned website. <br />
4. <code>{{redirect_to}}</code> is used when you want to redirect to the url mentioned in the query string(Ref: Misc Settings > Redirect To Param)<br />
5. You can also use absolute url for url redirection. Example: http://my-another-store.com/welcome<br />
6. Other available variables are: <strike>{{ip}} - IP Address, {{country_code}} - Country Code,</strike> <code>{{user_name}}</code> - User Full Name, <code>{{user_email}}</code> - User Email Address, <code>{{user_id}}</code> - User Id, <code>{{user_group_id}}</code> - User Group Id<br />
7. If Customer Group Wise Redirection Url is not defined then Default Redirection Url will be used.
</div>
";

    }
}