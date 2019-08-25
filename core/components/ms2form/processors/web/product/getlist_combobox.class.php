<?php
/**
 * Get a list combobox options of product
 *
 * @package ms2form
 * @subpackage processors
 */
class ms2FormProductGetListComboboxProcessor  extends modObjectProcessor
{
  public $classKey = 'msProductOption';
  /** @var pdoFetch $pdoFetch */
  private $pdoFetch;
  private $pid;
  private $key;
  public $languageTopics = array('ms2form:default');

  /**
   * @return bool
   */
  public function initialize()
  {
    $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
    if (!$pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
      return false;
    }

    $properties = $this->getProperties();
    $this->pdoFetch = new $pdoClass($this->modx);
    $this->pid = $this->getProperty('pid', null);
    if (!$this->key = $this->getProperty('key', null)) {
      return false;
    }

    return true;
  }

  /**
   * @return array|mixed|string
   */
  public function process()
  {
    $output = [];

    //
    if (!$option = $this->modx->getObject('msOption', ['key' => $this->key])) {
      return $this->failure('');
    }

    //
    switch ($option->get('type')) {
      case 'combobox':
      case 'combo-multiple':
        if ($option_properties = $option->get('properties')) {
          $values = $option_properties['values'] ?: [];
          $values = array_map(function($value) {
              return [
                'id' => $value,
                'text' => $value,
              ];
            }, $values);
          $output['all'] =  $values;
          unset($values);
        }
        break;

      case 'combo-options':
        $this->pdoFetch->setConfig([
          'class' => $this->classKey,
          'where' => [
            'key' => $this->key,
            'msProductOption.value:!=' => '',
          ],
          'select' => $this->modx->toJSON(['value' => 'msProductOption.value']),
          'groupby' => 'msProductOption.value',
          'limit' => 0,
          'fastMode' => true,
          'sortby' => 'msProductOption.value',
          'sortdir' => 'ASC',
          'return' => 'data',
        ]);
        $values = $this->pdoFetch->run() ?: [];
        $values = array_map(function($row) {
            return [
              'id' => $row['value'],
              'text' => $row['value'],
            ];
          }, $values);
        $output['all'] =  $values;
        unset($values);
        break;
    }

    //
    if (!empty($this->pid)) {
      $queryProduct = [
        'class' => $this->classKey,
        'where' => [
          'product_id' => $this->pid,
          'key' => $this->key,
        ],
        'select' => $this->modx->toJSON(['value' => 'msProductOption.value']),
        'groupby' => 'msProductOption.value',
        'limit' => 0,
        'fastMode' => true,
        'sortby' => 'msProductOption.value',
        'sortdir' => 'ASC',
        'return' => 'data',
      ];
      $this->pdoFetch->setConfig($queryProduct);
      $values = $this->pdoFetch->run() ?: [];
      $values = array_map(function($value) {
          return $value['value'];
        }, $values);
      $output['product'] = $values;
      unset($values);
    }

    return $this->success('', $output);
  }
}

return 'ms2FormProductGetListComboboxProcessor';