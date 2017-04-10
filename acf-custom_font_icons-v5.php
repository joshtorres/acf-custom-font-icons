<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('acf_field_custom_font_icons') ) :

    class acf_field_custom_font_icons extends acf_field {
        /**
         *  __construct
         *
         *  This function will setup the field type data
         *
         *  @date	3/15/2017
         *  @since	5.0.0
         *
         *  @param	$settings
         */

        function __construct() {
            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */
            $this->name = 'custom_font_icons';

            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */
            $this->label = __('Custom Font Icons', 'acf-custom_font_icons');

            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */
            $this->category = 'basic';

            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */
            $this->defaults = array(
                'class_prefix'	=> "fa",
            );


            /*
            *  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
            *  var message = acf._e('custom_font_icons', 'error');
            */
            $this->l10n = array(
                'error'	=> __('Error! Please enter a higher value', 'acf-custom_font_icons'),
            );

            // do not delete!
            parent::__construct();
        }

        /**
         * Get the list of CSS classes
         *
         * @author (original) Alessandro Gubitosi <gubi.ale@iod.io>
         * @author (modifications) Dan Green-Leipciger <dan@trampolinedigital.com>
         * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
         * @param $icons_file string
         * @param $class_prefix string
         * @return array
         */
        public function get_css_class_list($icons_file, $class_prefix) {


            $parsed_file = file_get_contents($icons_file);
            preg_match_all("/$class_prefix\-([a-zA-z0-9\-]+[^\:\.\,\s])/", $parsed_file, $matches);
            $exclude_icons = array("fa-lg", "fa-2x", "fa-3x", "fa-4x", "fa-5x", "fa-ul", "fa-li", "fa-fw", "fa-border", "fa-pulse", "fa-rotate-90", "fa-rotate-180", "fa-rotate-270", "fa-spin", "fa-flip-horizontal", "fa-flip-vertical", "fa-stack", "fa-stack-1x", "fa-stack-2x", "fa-inverse", "fa-lg{", "fa-2x{", "fa-3x{", "fa-4x{", "fa-5x{", "fa-ul{", "fa-li{", "fa-fw{", "fa-border{", "fa-pulse{", "fa-rotate-90{", "fa-rotate-180{", "fa-rotate-270{", "fa-spin{", "fa-flip-horizontal{", "fa-flip-vertical{", "fa-stack{", "fa-stack-1x{", "fa-stack-2x{", "fa-inverse{", "fa-pull-left", "fa-pull-left{", "fa-pull-right", "fa-pull-right{", "fa-ul>");
            $icons = $this->array_delete($matches[0], $exclude_icons);
            sort($icons);
            return $icons;
        }

        function array_delete($array, $element) {
            return (is_array($element)) ? array_values(array_diff($array, $element)) : array_values(array_diff($array, array($element)));
        }

        /**
         *  render_field_settings()
         *
         *  Create extra settings for your field. These are visible when editing a field
         *
         *  @since	3.6
         *  @date	23/01/13
         *
         *  @param	$field (array) the $field being edited
         */
        function render_field_settings( $field ) {
            acf_render_field_setting( $field, array(
                'label'			=> __('Font CSS File Location','acf-custom_font_icons'),
                'instructions'	=> __('The path of the CSS file relatice to the theme','acf-custom_font_icons'),
                'type'			=> 'text',
                'name'			=> 'relative_path',
                'required'      => true
            ));
            acf_render_field_setting( $field, array(
                'label'			=> __('CSS Class Prefix','acf-custom_font_icons'),
                'instructions'	=> __('defaults to "fa" for font-awesome if left blank','acf-custom_font_icons'),
                'type'			=> 'text',
                'name'			=> 'class_prefix'
            ));
        }



        /**
         *  render_field()
         *
         *  Create the HTML interface for your field
         *
         *  @param	$field (array) the $field being rendered
         *
         *  @since	3.6
         *  @date	23/01/13
         *
         *  @param	$field (array) the $field being edited
         */

        function render_field( $field ) {
            if (!$field['class_prefix']){
                $class_prefix = "fa";
            } else{
                $class_prefix = $field['class_prefix'];
            }



            $icons_url = \App\asset_path($field['relative_path']);

            if (!$field['relative_path']){
                echo '<pre style="color:red;">YOU NEED TO CHOOSE A PATH FOR THE FILES!!!</pre>';
            } else{
                $font_classes = $this->get_css_class_list($icons_url,$class_prefix);
                array_walk($font_classes, function(&$font, $key) use (&$font_classes){
                    $font = str_replace('{','',$font);
                    if($font == "icon-]"){
                        unset($font_classes[$key]);
                    }
                });
                $font_classes = array_unique($font_classes);

                ?>
                <script>
                    jQuery('head').append('<link rel="stylesheet" type="text/css" href="<?php echo $icons_url ?>">');
                </script>
                <style>
										input.dental-font-icons{display: none;}
										input..dental-font-icons + label{display: block;}
                    .dental-font-icons:checked + label {
                        border: #33b3db 1px solid;
                        min-height:50px;
                    }
                    .dental-font-icons label span {
                        line-height:45px;
                    }
                </style>

                <?php
                foreach($font_classes as $font_class) {
                    $checked = "";
                    $active = strpos($field['value'], $font_class);
                    if ( $active !== false ) $checked = "checked='checked'";
                    echo "<div style='min-height:50px;min-width:50px;display:inline-block;margin:10px'>";
                    echo "<input class='dental-font-icons' id='icon-" . $font_class ."' type=\"radio\" name=\"" .  esc_attr($field['name']) . "\" id=\"sad\" value='" . $font_class ."' $checked>";
                    echo "<label for='icon-" . $font_class ."'><span class='icon ". $font_class ."'></span></label>";
                    echo "</div>";
                }
            }
        }


        /**
         *  update_value()
         *
         *  This filter is applied to the $value before it is saved in the db
         *
         *  type	filter
         *  @since	3.6
         *  @date	23/01/13
         *
         *  @param	$value (mixed) the value found in the database
         *  @param	$post_id (mixed) the $post_id from which the value was loaded
         *  @param	$field (array) the field array holding all the field options
         *  @return	$value
         */
        function update_value( $value, $post_id, $field ) {
            if (!$field['class_prefix']){
                $class_prefix = "fa";
            } else{
                $class_prefix = $field['class_prefix'];
            }
            $value = "$class_prefix $value";
            return $value;

        }
    }


// initialize
    new acf_field_custom_font_icons( $this->settings );

// class_exists check
endif;
?>
