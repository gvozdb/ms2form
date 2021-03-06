<?php
/* @var array $scriptProperties */
/* @var ms2form $ms2form */
/* @var string $tplCreate */
/* @var string $tplUpdate */

if (!$modx->user->isAuthenticated()) {
  //return $modx->lexicon('ms2form_err_no_auth');
}

$ms2form = $modx->getService('ms2form', 'ms2form', $modx->getOption('ms2form_core_path', null, $modx->getOption('core_path') . 'components/ms2form/') . 'model/ms2form/', $scriptProperties);
$config = $ms2form->initialize($modx->context->key);

$data = $config;

if (empty($templates)) {
  $templates = 0;
}
if (empty($source)) {
  $source = $modx->getOption('ms2_product_source_default');
}
$ms2_product_thumbnail_size = $modx->getOption('ms2_product_thumbnail_size', $scriptProperties, $modx->getOption('ms2_product_thumbnail_size'));

if (empty($parent)) {
  $data['parent'] = '0';
} else {
  $data['parent'] = $parent;
}
$pid = !empty($_REQUEST['pid']) ? (integer)$_REQUEST['pid'] : 0;

// Update of msProduct
if (!empty($pid)) {
  $tplWrapper = $tplUpdate;
  /* @var msProduct $product */
  if ($product = $modx->getObject('msProduct', array('id' => $pid))) {
    if ($product->get('createdby') != $modx->user->id && !$modx->hasPermission('edit_document')) {
      return $modx->lexicon('ms2form_err_wrong_user');
    }
    $charset = $modx->getOption('modx_charset');
    $allowedFields = array_map('trim', explode(',', $scriptProperties['allowedFields']));
    $allowedFields = array_unique(array_merge($allowedFields, array('parent', 'pagetitle', 'content')));

    $productArray = $product->toArray();
    $fields = $product->getAllFieldsNames();
    $options = $ms2form->getProductOptions($product);

    //
    foreach ($allowedFields as $field) {
      if (in_array($field, $fields)) {
        $value = $productArray[$field];
      } elseif (isset($options[$field])) {
        $value = $options[$field]['value'];
      } else {
        $tvId = (int)trim($field, 'tv');
        $value = $product->getTVValue($tvId);
      }
      if (is_string($value) && $field != 'content') {
        $value = html_entity_decode($value, ENT_QUOTES, $charset);
        $value = str_replace(array('[^', '^]', '[', ']'), array('&#91;^', '^&#93;', '{{{{{', '}}}}}'), $value);
        $value = htmlentities($value, ENT_QUOTES, $charset);
      }
      $data[$field] = $value;
    }

    //
    $data['id'] = $product->id;
    $data['published'] = $product->published;
    $data['alias'] = $product->alias;
    $data['context_key'] = $product->context_key;
    $data['tags'] = $scriptProperties['tags'];
    $data['template'] = $scriptProperties['template'];
  } else {
    return $modx->lexicon('ms2form_err_id', array('id' => $pid));
  }
} else {
  $tplWrapper = $tplCreate;
  $options = $ms2form->getProductOptions();
}

//
foreach ($options as &$option) {
  //
  $values = [];
  $option['values'] = @$option['properties']['values'] ?: [];

  // $option['values'] = [];
  // $response = $ms2form->getListCombobox([
  //   'pid' => $pid,
  //   'key' => $option['key'],
  // ]);
  // $response = !is_array($response) ? $modx->fromJSON($response) : $response;
  // if ($response['success'] === true && @$response['data']['exists'] === true && !empty($response['data']['all'])) {
  //   $option['values'] = $response['data']['all'];
  // }
  if (is_array($option['values'])) {
    foreach ($option['values'] as $v) {
      $values[$v] = $v;
    }
    $option['values'] = $values;
  }

  //
  $values = [];
  if (is_array($option['value'])) {
    foreach ($option['value'] as $v) {
      $values[$v['value']] = $v['value'];
    }
    $option['value'] = $values;
  }

  //
  if (is_array($option['value']) && is_array($option['values'])) {
    $option['selectAll'] = $option['value'] == $option['values'];
  }
}
$data['options'] = $options;
unset($response, $option, $values);


// todo-me Get available sections for msProduct create


// Get templates list
if(!$data['template']){
  $templates = explode(',', $data['templates']);
  if (count($templates) > 1) {
    foreach ($templates as $template) {
      $selected = '';
      if ($template = explode('==', $template)) {
        if (!empty($pid)) {
          if ($product->template == $template[0]) {
            $selected = 'selected';
          }
        }
        $data['templates'] .= "<option $selected value=\"$template[0]\">$template[1]</option>";
      } else {
        if (!empty($pid)) {
          if ($product->template == $template) {
            $selected = 'selected';
          }
        }
        $data['templates'] .= "<option $selected value=\"$template\">$template</option>";
      }
    }
  } else {
    if (!empty($pid)) {
      $data['template'] = $product->template;
    } else {
      $data['template'] = $templates[0];
    }
  }
}

// Get files list
if (!empty($allowFiles)) {
  /** @var modMediaSource $source */
  if ($source = $modx->getObject('sources.modMediaSource', $source)) {
    $sourceProperties = $source->getPropertyList();
  }
  $q = $modx->newQuery('msProductFile');
  if (empty($pid)) {
    $q->where(array(
      'product_id' => 0,
      'parent' => 0,
      'createdby' => $modx->user->id,
    ));
  }else{
    $q->where(array(
      'product_id' => $pid,
      'parent' => 0,
      // 'createdby' => $modx->user->id
    ));
  }
  $q->sortby('rank', 'ASC');
  $fileObjects = $modx->getIterator('msProductFile', $q);
  $files = '';
  /** @var msProductFile $fileObject */
  foreach ($fileObjects as $fileObject) {
    $item = $fileObject->toArray();
    $item['size'] = round($item['size'] / 1024, 2);
    $tmp = explode('.', $item['file']);
    $extension = strtolower(end($tmp));

    $item['thumb'] = '';
    if (empty($ms2_product_thumbnail_size)) {
      $c = $modx->newQuery('msProductFile', [
        'product_id' => $item['product_id'],
        'parent' => $item['id'],
        'type' => 'image',
      ])->select('url')->limit(1)->sortby('id', 'ASC');
      if ($c->prepare()->execute()) {
        $item['thumb'] = $c->stmt->fetch(PDO::FETCH_COLUMN) ?: '';
      }
      unset($c, $tmp);
    } else {
      $item['thumb'] = '/'.$sourceProperties['baseUrl'].$item['path']. $ms2_product_thumbnail_size.'/'. str_replace('.'.$extension, '.'.strtolower($sourceProperties['thumbnailType']), $item['file']);
    }

    $tpl = $item['type'] == 'image'
      ? $tplImage
      : $tplFile;
    $files .= $ms2form->getChunk($tpl, $item);

    unset($fileObject, $item);
  }
  $data['files'] = $ms2form->getChunk($tplFiles, array(
    'files' => $files,
  ));
}

//output
$output = $ms2form->getChunk($tplWrapper, $data);

return $output;