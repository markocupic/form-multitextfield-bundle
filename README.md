# form-multitextfield-bundle

Usage with Contao Haste Form class:

```php
$objForm->addFormField('ffm_partner_open', array(
    'label'     => 'Öffnungszeiten',
    'inputType' => 'multitext',
    'eval'      => array('multiple' => true),
    'value'     => $objModel->ffm_partner_open
));
$objForm->bindModel($objModel);

if ($objForm->validate())
{
    // Decode entities
    $objWidget = $objForm->getWidget('ffm_partner_open');
    if(is_empty() || !is_array($objWidget->value))
    {
        $blnError = true;
        $objWidget->addError('Please add some content!');
    }
    if(!$blnError)
    {
        $objModel->save();
        $this->reload();
    }
}
```

