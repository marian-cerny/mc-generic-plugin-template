<h1>Generic Wordpress Plugin Template</h1>

<p>by <a href="http://mariancerny.com/" title="Marian Cerny">Marian Cerny</a></p>

<p>
This is a generic template to speed up Wordpress object oriented plug-in development. 
</p>

<h2>Features</h2>

<h3>Options</h3>
<p>
The main purpose of this template is to make the configuration of options as simple as possible. 
</p>
<p>
There's a class variable called 'settings', which is a multi-dimensional array. The first level of the array is for <strong>setting sections</strong>. Put in as many sections as you need.
</p>
<p>
Every section should have the following fields defined:
</p>
<p>
<strong>title</strong> - the title, displayed on the settings page <br/>
<strong>output_function</strong> - The name of the function which shows the description for this section. If no description is needed, use <code>output_settings_section_general</code>, which will output an empty string.  <br/>
<strong>fields</strong> - a list of fields within the current section <br/>
</p>

<p>
Every array in the list of fields should have the following fields:
</p>

<p>
<strong>title</strong> - the title of the field <br/>
<strong>type</strong> - type of field (currently works with checkbox, radio, text and number) <br/>
<strong>value</strong> - default value for the setting <br/>
<strong>description</strong> - field description (displayed after input) <br/>
<strong>*options</strong> - list of options for radio buttons (DB value => display value) <br/>
</p>

<p>
An options menu is automatically created and all settings are automatically registered.
</p>

<p>
To get settings values, use the <code>get_setting()</code> function. It takes a single string parameter - the name of the setting to retrieve (without namespace).
</p>

<h3>Javascript</h3>
<p>
A blank Javascript file with the same name as the main plug-in php file is included (using enqueue_script). The settings array is passed to the script and can be accessed through the <code>&lt;plugin_namespace&gt;ajax_vars</code> variable. This script is dependent on jQuery, which is enqueued too.
</p>
