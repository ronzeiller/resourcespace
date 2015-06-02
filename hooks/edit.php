<?php
/* 
 * Show Field Information on edit all
 * Plugin by Ronnie Zeiller
 */

function HookMulti_editEditEditbeforesectionhead() {
	
	global $items, $resources_all;
	
	$resources_all=get_resource_field_data_batch($items);
	
	/*
	array(17) {
		[5354]=> array(10) {
			[75]=> array(36) {........}
			[76]=> array(36) {........}
			[18]=> array(36) { 
				["resource"]=> string(4) "5354" 
				["ref"]=> string(2) "18" 
				["name"]=> string(7) "caption" 
				["title"]=> string(7) "Caption" 
				["type"]=> string(1) "1" 
				["options"]=> string(0) "" 
				["order_by"]=> string(2) "80" 
				["keywords_index"]=> string(1) "1" 
				["partial_index"]=> string(1) "0" 
				["resource_type"]=> string(1) "0" 
				["resource_column"]=> string(0) "" 
				["display_field"]=> string(1) "1" 
				["use_for_similar"]=> string(1) "0" 
				["iptc_equiv"]=> string(5) "2#120" 
				["display_template"]=> string(90) "[title][value]" 
				["tab_name"]=> string(0) "" 
				["required"]=> string(1) "0" 
				["smart_theme_name"]=> string(0) "" 
				["exiftool_field"]=> string(45) "imagedescription,description,caption-abstract" 
				["advanced_search"]=> string(1) "1" 
				["simple_search"]=> string(1) "1" 
				["help_text"]=> string(0) "" 
				["display_as_dropdown"]=> string(1) "0" 
				["external_user_access"]=> string(1) "1" 
				["autocomplete_macro"]=> string(0) "" 
				["hide_when_uploading"]=> string(1) "0" 
				["hide_when_restricted"]=> string(1) "0" 
				["value_filter"]=> string(0) "" 
				["exiftool_filter"]=> string(0) "" 
				["omit_when_copying"]=> string(1) "0" 
				["tooltip_text"]=> string(0) "" 
				["regexp_filter"]=> string(0) "" 
				["sync_field"]=> string(0) "" 
				["display_condition"]=> string(0) "" 
				["onchange_macro"]=> string(0) "" 
				["value"]=> string(78) "AIT Bilanzpressekonferenz am 14.6.2011, AIT - Austrian Institute of Technology" 
			}
		}
	}
	 */
	
}


## ersetzt die gewöhnliche Checkbox
## iteriert zu jedem $fields (Zeile 1228) --> Aufruf Funktion display_field($n, $fields[$n], $newtab);
## Übergeben wird $fieldref (id des aktuellen Feldes das angezeigt werden soll) = array($field["ref"] für $ref=$items[0];
## es werden die Inhalte der entsprechenden Felder angezeigt, wenn sie überall gleich befüllt sind
## oder leere Felder angezeigt wenn noch kein Eintrag existiert
## oder ein Hinweis gegeben, wenn verschiedene Werte in den Feldern stehen
function HookMulti_editEditReplace_edit_all_checkbox($fieldref) {
	
	global $name, $n, $fields, $items,$language, $lang,$resources_all,$filename_field;
	
	//var_dump($fields);	// in $fields stehen alle Infos nur zum ersten(!) Bild in der Collection 
	$field = $fields[$n];
	$name="field_" . $fieldref;	
	// makes no sense to write original filename on multi-edit!!
	if ($fieldref==$filename_field) {return true;}
	
	## in $value steht der Inhalt zu jedem Feld $fieldref (nur zum ersten(!) Bild in der Collection)
	$value=$field["value"];
	$value=trim($value);
	//var_dump($value);	// caption string
	# Multiple items, a toggle checkbox appears which activates the question
	?>
		<div class="edit_multi_checkbox">
			<input name="editthis_<?php echo htmlspecialchars($name)?>" id="editthis_<?php echo $n?>" 
					 type="checkbox" value="yes" 
					 onClick="var q=document.getElementById('question_<?php echo $n?>');
							 var m=document.getElementById('modeselect_<?php echo $n?>');
							 var f=document.getElementById('findreplace_<?php echo $n?>');
							 var content=document.getElementById('fieldcontent_<?php echo $n?>');
							 if (this.checked) {q.style.display='block';m.style.display='block';content.style.display='block';} 
							 else {q.style.display='none';m.style.display='none';f.style.display='none';content.style.display='none';
								 document.getElementById('modeselectinput_<?php echo $n?>').selectedIndex=0;}"
			>&nbsp;<label for="editthis<?php echo $n?>"><?php echo htmlspecialchars($field["title"])?></label>
			<?php
			## für alle Resourcen das Feld $field["title"] auslesen
			//var_dump($items);
			//var_dump($resources_all);
			$fieldval = array();
			$count_array_unique = 0;
			$fieldvalmessage = '';
			foreach ($resources_all as $resourcefields) {
				if (isset($resourcefields[$fieldref])) {
					//var_dump($resourcefield[$fieldref]);
					$fieldval[] = $resourcefields[$fieldref]['value'];
				} else {
					$fieldval[]= '';
				}
				/*
				 * array(10) { 
				 *		[0]=> int(75) [1]=> int(76) [2]=> int(10) [3]=> int(8) [4]=> int(51) [5]=> int(18) [6]=> int(29) [7]=> int(12) [8]=> int(52) [9]=> int(54) } 
				 */
			}
			
			$count_array_unique = count(array_unique($fieldval));
			
			if($count_array_unique > 1) {
					//verschiedene werte
					$fieldvalmessage = $lang["different_values"];
					?>
					<script>
						jQuery(document).ready(function ($) {
							$('#fieldcontentx_<?php echo $n;?>').css("background-color","lightblue");
						});
					</script>
					<?php
			}
			if($count_array_unique == 1) {
				//var_dump($fieldval[0]);
				//gleiche werte
				if ($fieldval[0] == '') {
					$fieldvalmessage = $lang["field_empty"];
					//var_dump($fieldval[0]);
				} else {
					$fieldvalmessage = $fieldval[0];
				}
			}
			
			?>
		</div><!-- End of edit_multi_checkbox -->
		<div class="Question" id='fieldcontent_<?php echo $n?>' style="display:none;">
            <label for="archive"><?php echo $lang["field_content"]; ?></label>
		<?php
			if ($field["type"]==0)
				{
				?>
				<input id='fieldcontentx_<?php echo $n?>' class="stdwidth" type="text" value="<?php echo $fieldvalmessage; ?>" disabled>
				<?php
				}
			else
				{
				?>
				<textarea id='fieldcontentx_<?php echo $n?>' rows=6 cols=50 class="stdwidth" disabled><?php echo $fieldvalmessage; ?></textarea>
				<?php
				}
			?>
				
		</div>
	<?php
	return true;
}
