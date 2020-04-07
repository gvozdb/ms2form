<form class="well" method="post" action="" id="ms2form" role="form" data-reset="true">
  <input type="hidden" id="ms2formFormKey" name="form_key" value="[[+formKey]]">
  <input type="hidden" name="pid" value="[[+id]]">
  <input type="hidden" name="parent" value="[[+parent]]">
  <input type="hidden" name="alias" value="[[+alias]]">
  <input type="hidden" name="context_key" value="[[+context_key]]">
  <input type="hidden" name="published" value="1"/>
  <input type="hidden" name="hidemenu" value="0"/>
  <input type="hidden" name="redirectPublished" value="[[+redirectPublished]]"/>
  <input type="hidden" id="ms2formTagsNew" value="[[+tagsNew]]"/>

  <div class="form-group">
    <label>[[%ms2form_pagetitle]]</label>
    <input type="text" class="form-control" placeholder="[[%ms2form_pagetitle]]" name="pagetitle" value="[[+pagetitle]]" maxlength="50" id="ms2form-pagetitle"/>
  </div>

  [[+parentMse2form.element]]

  [[+templates]]

  <div class="form-group">
    <label>[[%ms2form_categories]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formSections">
  </div>

  [[+tags]]

  <div class="form-group">
    <label>Text option</label>
    <input type="text" class="form-control" name="textoption" value="[[+textoption]]">
  </div>

  <div class="form-group">
    <label>Single list</label>
    <br/>
    <input type="hidden" class="form-control [ js-ms2f-combobox-single ]" name="singlelist">
  </div>

  <div class="form-group">
    <label>Multi list</label>
    <br/>
    <input type="hidden" class="form-control [ js-ms2f-combobox-multiple ]" name="multilist">
    <label><input type="checkbox" class="[ js-ms2f-combobox-select-all ]" data-name="multilist"> Выбрать всё</label>
  </div>

  <div class="form-group">
    <label>Auto list</label>
    <br/>
    <input type="hidden" class="form-control [ js-ms2f-combobox-auto ]" name="autolist">
  </div>

  {if isset($options['checkboxes1']) && $options['checkboxes1']['type'] === 'combo-multiple'}
    <div class="form-group">
      <label>Checkboxes 1</label>
      <br/>
      <input type="hidden" name="checkboxes1" value="">
      <div><label><input type="checkbox" class="[ js-ms2f-checkboxes-select-all ]" data-name="checkboxes1"
                      {$options['checkboxes1']['selectAll'] ? 'checked' : ''}> Выбрать всё</label></div>
      <div>
        {foreach $options['checkboxes1']['values'] as $v}
          {var $checked = isset($options['checkboxes1']['value'][$v])}
          <label><input type="checkbox" name="checkboxes1[]" value="{$v}" {$checked ? 'checked' : ''}> {$v}</label>&nbsp;
          {unset $checked}
        {/foreach}
      </div>
    </div>
  {/if}

  {if isset($options['checkboxes2']) && $options['checkboxes2']['type'] === 'combo-multiple'}
    <div class="form-group">
      <label>Checkboxes 2</label>
      <br/>
      <input type="hidden" name="checkboxes2" value="">
      <div><label><input type="checkbox" class="[ js-ms2f-checkboxes-select-all ]" data-name="checkboxes2"
                      {$options['checkboxes2']['selectAll'] ? 'checked' : ''}> Выбрать всё</label></div>
      <div>
        {foreach $options['checkboxes2']['values'] as $v}
          {var $checked = isset($options['checkboxes2']['value'][$v])}
          <label><input type="checkbox" name="checkboxes2[]" value="{$v}" {$checked ? 'checked' : ''}> {$v}</label>&nbsp;
          {unset $checked}
        {/foreach}
      </div>
    </div>
  {/if}

  <div class="form-group">
    <label>Пример TV </label>
    <br/> указать в параметре allowedFields=`parent,pagetitle,content,published,template,hidemenu,tags,tv1`
    <br/>
    <input type="text" value="[[+tv1]]" name="tv1" class="form-control">
  </div>


  <div class="form-group popover-help" id="formGroupContent">
    <input id="content" name="content" type="hidden" value="[[+content:escape]]"/>
    [[$tpl.ms2form.editor.[[+editor]]?content=`[[+content]]`]]
  </div>

  <div class="ticket-form-files popover-help">
    [[+files]]
  </div>

  <div class="form-actions">
    <input type="submit" id="ms2formSubmit" class="btn btn-primary submit" value="[[%ms2form_save]]"/>
  </div>
</form>

<!--pdotools_parentMse2form.element
  <div class="form-group">
    <label>[[%ms2form_category]]</label>
    <span class="text-danger">*</span>
    <input type="text" data-key=[[+mse2formKey]] id="ms2formParentMse2form" class="form-control disable-sisyphus" name="[[+parentMse2form.queryVar]]" placeholder="[[%ms2form_search]]" value="" />
  </div>
-->

<!--pdotools_tags
  <div class="form-group">
    <label>[[%ms2form_tags]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formTags">
  </div>
-->

<!--pdotools_templates
<div class="form-group">
  <label>[[%ms2form_template]]</label>
  <br/>
  <select class="form-control" name="template" id="ms2formTemplate">
    [[+templates]]
  </select>
</div>
-->
<!--pdotools_!templates
<input type="hidden" name="template" value="[[+template]]">
-->
