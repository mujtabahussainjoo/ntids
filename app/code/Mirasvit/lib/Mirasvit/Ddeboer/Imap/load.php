<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



require_once dirname(__FILE__) . '/Charset.php';
require_once dirname(__FILE__) . '/Connection.php';
require_once dirname(__FILE__) . '/Exception/Exception.php';
require_once dirname(__FILE__) . '/Exception/AuthenticationFailedException.php';
require_once dirname(__FILE__) . '/Exception/MailboxDoesNotExistException.php';
require_once dirname(__FILE__) . '/Exception/MessageDeleteException.php';
require_once dirname(__FILE__) . '/Exception/MessageMoveException.php';
require_once dirname(__FILE__) . '/Mailbox.php';
require_once dirname(__FILE__) . '/Message/Part.php';
require_once dirname(__FILE__) . '/Message/Attachment.php';
require_once dirname(__FILE__) . '/Message/EmailAddress.php';
require_once dirname(__FILE__) . '/Message/Headers.php';
require_once dirname(__FILE__) . '/Message.php';
require_once dirname(__FILE__) . '/MessageIterator.php';
require_once dirname(__FILE__) . '/Search/Condition.php';
require_once dirname(__FILE__) . '/Search/Date.php';
require_once dirname(__FILE__) . '/Search/Date/After.php';
require_once dirname(__FILE__) . '/Search/Date/Before.php';
require_once dirname(__FILE__) . '/Search/Date/On.php';
require_once dirname(__FILE__) . '/Search/Email.php';
require_once dirname(__FILE__) . '/Search/Email/FromAddress.php';
require_once dirname(__FILE__) . '/Search/Email/To.php';
require_once dirname(__FILE__) . '/Search/Flag/Answered.php';
require_once dirname(__FILE__) . '/Search/Flag/Flagged.php';
require_once dirname(__FILE__) . '/Search/Flag/Recent.php';
require_once dirname(__FILE__) . '/Search/Flag/Seen.php';
require_once dirname(__FILE__) . '/Search/Flag/Unanswered.php';
require_once dirname(__FILE__) . '/Search/Flag/Unflagged.php';
require_once dirname(__FILE__) . '/Search/Flag/Unseen.php';
require_once dirname(__FILE__) . '/Search/LogicalOperator/All.php';
require_once dirname(__FILE__) . '/Search/LogicalOperator/OrConditions.php';
require_once dirname(__FILE__) . '/Search/State/Deleted.php';
require_once dirname(__FILE__) . '/Search/State/NewMessage.php';
require_once dirname(__FILE__) . '/Search/State/Old.php';
require_once dirname(__FILE__) . '/Search/State/Undeleted.php';
require_once dirname(__FILE__) . '/Search/Text.php';
require_once dirname(__FILE__) . '/Search/Text/Body.php';
require_once dirname(__FILE__) . '/Search/Text/Keyword.php';
require_once dirname(__FILE__) . '/Search/Text/Subject.php';
require_once dirname(__FILE__) . '/Search/Text/Text.php';
require_once dirname(__FILE__) . '/Search/Text/Unkeyword.php';
require_once dirname(__FILE__) . '/SearchExpression.php';
require_once dirname(__FILE__) . '/Server.php';

