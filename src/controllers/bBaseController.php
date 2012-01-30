<?php

/**
 *
 */
abstract class bBaseController extends CController
{
	private $_widgetStack = array();

	/**
	 * Returns the directory containing view files for this controller.
	 * We're overriding this since CController's version defaults $module to Yii::app().
	 * @return string the directory containing the view files for this controller.
	 */
	public function getViewPath()
	{
		if (($module = $this->getModule()) === null)
			$module = Blocks::app();

		return $module->getViewPath().'/';
	}

	/**
	 * Overriding so we can check if the viewName is a request for an email template vs. a site template.
	 * @param $viewName
	 * @return mixed
	 */
	public function getViewFile($viewName)
	{
		if (($theme = Blocks::app()->getTheme()) !== null && ($viewFile = $theme->getViewFile($this, $viewName)) !== false)
			return $viewFile;

		$moduleViewPath = $basePath = Blocks::app()->getViewPath();
		if (($module = $this->getModule()) !== null)
			$moduleViewPath = $module->getViewPath();

		if (strncmp($viewName,'///email', 8) === 0)
		{
			$viewPath = rtrim(Blocks::app()->path->emailTemplatePath, '/');
			$viewName = substr($viewName, 9);
		}
		else
			$viewPath = $this->getViewPath();

		return $this->resolveViewFile($viewName, $viewPath, $basePath, $moduleViewPath);
	}

	/**
	 * Loads a template
	 * @param       $relativeTemplatePath
	 * @param array $data Any variables that should be available to the template
	 * @param bool  $return Whether to return the results, rather than output them
	 * @return mixed
	 */
	public function loadTemplate($relativeTemplatePath, $data = array(), $return = false)
	{
		if (!is_array($data))
			$data = array();

		foreach ($data as &$tag)
		{
			$tag = bTemplateHelper::getVarTag($tag);
		}

		$baseTemplatePath = Blocks::app()->path->normalizeTrailingSlash(Blocks::app()->viewPath);

		if (bTemplateHelper::findFileSystemMatch($baseTemplatePath, $relativeTemplatePath) !== false)
			return $this->renderPartial($relativeTemplatePath, $data, $return);

		throw new bHttpException(404);
	}

	/**
	 * @param $relativeTemplatePath
	 * @param array $data
	 * @return mixed
	 */
	public function loadEmailTemplate($relativeTemplatePath, $data = array())
	{
		if (!is_array($data))
			$data = array();

		foreach ($data as &$tag)
		{
			$tag = bTemplateHelper::getVarTag($tag);
		}

		$baseTemplatePath = Blocks::app()->path->normalizeTrailingSlash(Blocks::app()->path->emailTemplatePath);

		if (bTemplateHelper::findFileSystemMatch($baseTemplatePath, $relativeTemplatePath) !== false)
		{
			$relativeTemplatePath = '///email/'.$relativeTemplatePath;
			return $this->renderPartial($relativeTemplatePath, $data, true);
		}

		throw new bException('Could not find the email template '.$relativeTemplatePath);
	}

	/**
	 * @param $viewFile
	 * @param null $data
	 * @param bool $return
	 * @return mixed|string
	 * @throws bException
	 */
	public function renderFile($viewFile, $data = null, $return = false)
	{
		$widgetCount = count($this->_widgetStack);

		if (strpos($viewFile, 'email_templates') !== false)
		{
			$emailRenderer = new bEmailTemplateRenderer();
			$content = $emailRenderer->renderFile($this, $viewFile, $data, $return);
		}
		else
		{
			if (($renderer = Blocks::app()->getViewRenderer()) !== null && $renderer->fileExtension === '.'.CFileHelper::getExtension($viewFile))
				$content = $renderer->renderFile($this, $viewFile, $data, $return);
			else
				$content = $this->renderInternal($viewFile, $data, $return);
		}

		if (count($this->_widgetStack) === $widgetCount)
			return $content;
		else
		{
			$widget = end($this->_widgetStack);
			throw new bException(Blocks::t('blocks','{controller} contains improperly nested widget tags in its view "{view}". A {widget} widget does not have an endWidget() call.',
				array('{controller}' => get_class($this), '{view}' => $viewFile, '{widget}' => get_class($widget))));
		}
	}

	/**
	 * Returns a 404 if this isn't a POST request
	 */
	public function requirePostRequest()
	{
		if (!Blocks::app()->getConfig('devMode') && Blocks::app()->request->requestType !== 'POST')
			throw new bHttpException(404);
	}

	/**
	 * Returns a 404 if this isn't an Ajax request
	 */
	public function requireAjaxRequest()
	{
		if (!Blocks::app()->getConfig('devMode') && !Blocks::app()->request->isAjaxRequest)
			throw new bHttpException(404);
	}

	/**
	 * Redirect
	 */
	public function redirect($url, $terminate=true, $statusCode=302)
	{
		if (is_string($url))
			$url = bUrlHelper::generateUrl($url);

		parent::redirect($url, $terminate, $statusCode);
	}
}
