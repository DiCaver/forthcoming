<?php
/**
 * @file ForthcomingHandler.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.forthcoming
 * @class ForthcomingHandler
 * Find forthcoming content and display it when requested.
 */

import('classes.handler.Handler');

class ForthcomingHandler extends Handler {

    public static $plugin;
    public static $forthcomingIssueId;

    public static function setPlugin($plugin)
    {
        self::$plugin = $plugin;
    }

    public static function setForthcomingId($forthcomingIssueId)
    {
        self::$forthcomingIssueId = $forthcomingIssueId;
    }


	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Handle index request (redirect to "view")
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function index($args, $request) {
			AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
			import('classes.submission.Submission');
			$context = $request->getContext();
			$contextId = $context->getId();
			$templateMgr = TemplateManager::getManager($request);
			$this->setupTemplate($request);

			if (self::$forthcomingIssueId) {
				$forthcomingIterator = iterator_to_array(
					Services::get('submission')->getMany([
						'contextId' => $contextId,
						'issueIds' => [self::$forthcomingIssueId],
						'status' => [STATUS_PUBLISHED],
						'orderBy' => 'datePublished',
						'orderDirection' => 'ASC',
				]));

				$forthcomingSubmissions = [];
				foreach ($forthcomingIterator as $submission) {
					if ($submission->getCurrentPublication()->getData('issueId') == self::$forthcomingIssueId && $submission->getCurrentPublication()->getData('datePublished')){
						$forthcomingSubmissions[] = $submission;
					}
				}

				$templateMgr->assign('forthcoming', $forthcomingSubmissions);
				$templateMgr->display(self::$plugin->getTemplateResource('content.tpl'));

			}
	}


}

?>
