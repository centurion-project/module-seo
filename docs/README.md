How use the trait SEO
=====================

Trait SEO gives your business objects the ability to support additional fields "Meta / Seo"
to use in the meta tags in the HTML rendering.

This trait has been designed to limit the impact in your business code and to ease its use :
You must only add some traits on your classes for "Rows" and "DbTable models" and,
if you are using "Centurion_Form_Model", on your forms classes.


Implementing the trait SEO on your Business objects :
-----------------------------------------------------

###Configure your installation (Mandatory) :
You must define the list of types of meta available in your application (like "keywords", "description", "fg", ...).
This list is defined by using the configuration key "seo.meta.types[]".

Like this :

                ;SEO
                seo.meta.types[] = "description"
                seo.meta.types[] = "keywords"

###On your DbTable Model classes (Mandatory) :
You must add the trait `Seo_Traits_Model_DbTable_Interface`.

With this trait, you must define the **public** method `getFieldsToGenerateMeta()`. This method is called automatically
 by the trait SEO to generate SEO's content when there are empty from the row's content. It must return an array as :

                'meta type name' => set_of_fields

The `set_of_fields` must be an array of fields of the current row. (They must be "Specials Gets").
If you want apply a specific processing some fields, please define the field name as key, and the method callback as
the value, like this :

                'meta type name' => array(
                    'title',
                    'teaser'        => array('MySeoClass', 'Keywordizer'),
                    'description'   => array('MySeoClass', 'Keywordizer')
                )

####Here is an example implementation of this method

                public function getFieldsToGenerateMeta(){
                    return array(
                        Seo_Traits_Model_DbTable_Interface::META_KEYWORDS => array(
                            'title',
                            'teaser' => array('BusinessInflector', 'Keywordizer')
                        ),
                        Seo_Traits_Model_DbTable_Interface::META_DESCRIPTION => array(
                            'content'
                        )
                    );
                }

####Customize the list of meta fields available for this model
By default, the list of available meta types are defined in the configuration by "seo.meta.types[]".
But, you can customize this list for a model by overwriting the **public** method "getDefinedMeta()".
This method must return an array of string like this :

                public function getDefinedMeta(){
                    return array('keywords');
                }

###On your Row classes (Mandatory) :
All your rows class get by previous models must implement the trait `Seo_Traits_Model_DbTable_Row_Interface`.
 There are not methods to implement. This trait provides two new **public** methods :

 *  `saveMetas(array())` :  This method accepts an array as array("meta type" => "value")
                            to define meta of the current row
 *  `getMetaRowset()` :     Return the set of defined metas for the current row.
                            If some metas are not available, there are automatically generated by
                            the trait at the first call.

###On your Form classes (Optional) :
If you want generate automatically fields in your form to manage Meta fields for your objects,
you can add the trait `Seo_Traits_Form_Model`. This trait is only available for `Centurion_Form_Model_Abstract` forms.

This trait generates automatically textarea fields to manage Meta of yours business objects and save value in yours
objects.

####On your CRUD classes (Optional) :
If you use the previous trait on your forms, you can also add the trait on your CRUD : `Seo_Traits_Controller_CRUD`
to place Meta's fields in a special Grid group, before buttons in the form (If you are using the Grid of the module Admin).

Use the trait SEO in a Multi-website context :
----------------------------------------------

You can use the trait SEO in a multi-website context. To do this, you must only defining a website adapter to pass to
the trait SEO. Your website adapter must implements the interface `Seo_Model_Website_Interface` and returns
 an uniq integer id for each website when its method `getWebsiteId()` is called.

To register your adapter, you must call the **static** method `Seo_Model_DbTable_Meta::registerWebsiteAdapter($adapter)`.
To unregister your adapter, you must pass `null` to this method.


How use Object's meta in your HTML Stream :
-------------------------------------------

Your application is not able to find automatically the main object in yours views to retrieve its meta and populate
 HTML's metas ; you must declare them in your controllers.

 To do it, you must use the **public** method or you `Centurion_Controller_Action` : `definingMainObject()`.
 The trait SEO intercepts this signal to use metas of your main objects to populate Meta variables in the view.