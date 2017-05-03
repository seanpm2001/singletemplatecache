<?php

namespace Craft;

class SingleTemplateCacheController extends BaseController
{
  /**
   * Constructor
   */
  public function __construct()
  {
      $this->requirePostRequest();
  }

  /**
   * Delete one or many cache entries
   * @return JSON
   */
  public function actionDelete()
  {
    $this->requireAjaxRequest();

    $key = craft()->request->getRequiredPost('id');

    if (is_array($key))
    {
         $condition = array('in', 'cacheKey', $key);
         $params = array();
    }
    else
    {
         $condition = 'cacheKey = :cacheKey';
         $params = array(':cacheKey' => $key);
    }

    $result = craft()->db->createCommand()->delete('templatecaches', $condition, $params);
    $this->returnJson(array('success' => $result));
  }

  /**
   * Find and return results based on search string
   * @return JSON
   */
  public function actionSearch()
  {
    $this->requireAjaxRequest();

    $search = urldecode(craft()->request->getRequiredPost('search'));

    $cache = craft()->db->createCommand()
                ->select('id, cacheKey, path')
                ->from('templatecaches')
                ->where('cacheKey LIKE "%'.$search.'%"')
                ->group('cacheKey')
                ->queryAll();

    $html = '';

    foreach ($cache as $record) {
      $html .= '<tr data-id="'.$record['cacheKey'].'" data-name="'.$record['cacheKey'].'">';
        $html .= '<td><div class="checkbox" title="Select" data-id="'.$record['cacheKey'].'"></div></td>';
        $html .= '<td>'.$record['cacheKey'].'</td>';
        $html .= '<td>'.$record['path'].'</td>';
        $html .= '<td><a class="delete icon" title="Delete"></a></td>';
      $html .= '</tr>';
    }
    $this->returnJson(array('html' => $html));
  }
}